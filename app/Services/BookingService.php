<?php

namespace App\Services;

use App\DTOs\CreateBookingDto;
use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Exceptions\BookingInvalidStatusException;
use App\Exceptions\PaymentRequiredException;
use App\Exceptions\RoomMaintenanceException;
use App\Exceptions\RoomNotAvailableException;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\BookingRoom;
use App\Models\BookingServiceItem;
use App\Models\HotelService;
use App\Models\InvoiceItem;
use App\Models\Room;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookingRepository,
        private readonly PricingService $pricingService,
        private readonly InvoiceService $invoiceService,
    ) {}

    public function checkAvailability(
        int $branchId,
        Carbon $checkIn,
        Carbon $checkOut,
        ?array $roomIds = null,
        ?int $roomTypeId = null,
    ): array {
        $availableRooms = $this->bookingRepository->getAvailableRooms(
            $branchId,
            $checkIn,
            $checkOut,
            $roomTypeId,
            $roomIds
        );

        return [
            'total' => $availableRooms->count(),
            'available_rooms' => $availableRooms,
        ];
    }

    public function createBooking(CreateBookingDto|array $data): Booking
    {
        if ($data instanceof CreateBookingDto) {
            $data = [
                'customer_id' => $data->customerId,
                'branch_id' => $data->branchId,
                'check_in_date' => $data->checkInDate->toDateString(),
                'check_out_date' => $data->checkOutDate->toDateString(),
                'room_ids' => $data->roomIds,
                'adults' => $data->adults,
                'children' => $data->children,
                'source' => $data->source,
                'special_requests' => $data->specialRequests,
            ];
        }

        $checkIn = Carbon::parse($data['check_in_date']);
        $checkOut = Carbon::parse($data['check_out_date']);
        $roomIds = $data['room_ids'];
        $branchId = (int) $data['branch_id'];

        foreach ($roomIds as $roomId) {
            if ($this->bookingRepository->hasOverlap((int) $roomId, $checkIn, $checkOut)) {
                throw new RoomNotAvailableException();
            }
        }

        return DB::transaction(function () use ($data, $checkIn, $checkOut, $roomIds, $branchId) {
            $total = 0;
            $booking = $this->bookingRepository->create([
                'branch_id' => $branchId,
                'customer_id' => $data['customer_id'],
                'booking_code' => 'BK-'.strtoupper(Str::random(8)),
                'status' => BookingStatus::Confirmed->value,
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'source' => $data['source'] ?? 'offline',
                'adults' => $data['adults'] ?? 1,
                'children' => $data['children'] ?? 0,
                'special_requests' => $data['special_requests'] ?? null,
            ]);

            foreach ($roomIds as $roomId) {
                $room = Room::findOrFail($roomId);
                $nights = max(1, $checkIn->diffInDays($checkOut));
                $rate = $this->pricingService->calculateNightlyRate($room->room_type_id, $branchId, $checkIn, $data['customer_id'] ?? null);
                $lineTotal = $rate * $nights;
                $total += $lineTotal;

                BookingRoom::create([
                    'booking_id' => $booking->id,
                    'room_id' => $room->id,
                    'room_type_id' => $room->room_type_id,
                    'check_in_date' => $checkIn->toDateString(),
                    'check_out_date' => $checkOut->toDateString(),
                    'rate_snapshot' => $rate,
                    'total_amount' => $lineTotal,
                    'nights' => $nights,
                ]);

                $room->update(['status' => RoomStatus::Reserved->value]);
            }

            $booking->update(['total_amount' => $total]);
            $this->logHistory($booking, 'created', null, BookingStatus::Confirmed->value);

            return $booking->load(['customer', 'bookingRooms.room']);
        });
    }

    public function checkIn(int $bookingId): Booking
    {
        return DB::transaction(function () use ($bookingId) {
            $booking = Booking::lockForUpdate()->with('bookingRooms.room')->findOrFail($bookingId);

            if ($booking->status !== BookingStatus::Confirmed) {
                throw new BookingInvalidStatusException('Trạng thái booking không hợp lệ cho check-in.');
            }

            foreach ($booking->bookingRooms as $br) {
                if ($br->room->status === RoomStatus::Maintenance) {
                    throw new RoomMaintenanceException();
                }
                $br->room->update(['status' => RoomStatus::Occupied->value]);
            }

            $booking->update([
                'status' => BookingStatus::CheckedIn->value,
                'actual_check_in_at' => now(),
            ]);

            $this->logHistory($booking, 'check_in', BookingStatus::Confirmed->value, BookingStatus::CheckedIn->value);
            $this->invoiceService->generateFromBooking($booking);

            return $booking->fresh(['customer', 'bookingRooms.room', 'invoices']);
        });
    }

    public function checkOut(int $bookingId, ?string $notes = null): Booking
    {
        return DB::transaction(function () use ($bookingId, $notes) {
            $booking = Booking::lockForUpdate()->with(['bookingRooms.room', 'invoices'])->findOrFail($bookingId);

            if ($booking->status !== BookingStatus::CheckedIn) {
                throw new BookingInvalidStatusException('Trạng thái booking không hợp lệ cho check-out.');
            }

            $invoice = $booking->invoices()->latest()->first();
            if ($invoice && (float) $invoice->balance > 0) {
                throw new PaymentRequiredException();
            }

            foreach ($booking->bookingRooms as $br) {
                $br->room->update(['status' => RoomStatus::Cleaning->value]);
            }

            $booking->update([
                'status' => BookingStatus::CheckedOut->value,
                'actual_check_out_at' => now(),
                'special_requests' => $notes ? trim(($booking->special_requests ?? '')."\n".$notes) : $booking->special_requests,
            ]);

            $this->logHistory($booking, 'check_out', BookingStatus::CheckedIn->value, BookingStatus::CheckedOut->value);

            if ($booking->customer) {
                $points = max(1, (int) floor((float) $booking->total_amount / 100000));
                $booking->customer->increment('loyalty_points', $points);
            }

            return $booking->fresh();
        });
    }

    public function cancelBooking(int $bookingId, string $reason): Booking
    {
        return DB::transaction(function () use ($bookingId, $reason) {
            $booking = Booking::lockForUpdate()->with('bookingRooms.room')->findOrFail($bookingId);

            if (in_array($booking->status, [BookingStatus::CheckedOut, BookingStatus::Cancelled], true)) {
                throw new BookingInvalidStatusException('Không thể hủy booking ở trạng thái hiện tại.');
            }

            $fromStatus = $booking->status->value;

            foreach ($booking->bookingRooms as $br) {
                $br->room->update(['status' => RoomStatus::Available->value]);
            }

            $booking->update([
                'status' => BookingStatus::Cancelled->value,
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            $this->logHistory($booking, 'cancelled', $fromStatus, BookingStatus::Cancelled->value);

            return $booking->fresh();
        });
    }

    public function extendBooking(int $bookingId, Carbon $newCheckOut): Booking
    {
        return DB::transaction(function () use ($bookingId, $newCheckOut) {
            $booking = Booking::lockForUpdate()->with('bookingRooms.room')->findOrFail($bookingId);

            if (! in_array($booking->status, [BookingStatus::Confirmed, BookingStatus::CheckedIn], true)) {
                throw new BookingInvalidStatusException('Không thể gia hạn booking ở trạng thái hiện tại.');
            }

            $currentCheckOut = Carbon::parse($booking->check_out_date);
            if ($newCheckOut->lte($currentCheckOut)) {
                throw new BookingInvalidStatusException('Ngày gia hạn phải sau ngày check-out hiện tại.');
            }

            foreach ($booking->bookingRooms as $br) {
                if ($this->bookingRepository->hasOverlap($br->room_id, $currentCheckOut, $newCheckOut, $booking->id)) {
                    throw new RoomNotAvailableException('Phòng '.$br->room->room_number.' không trống trong khoảng gia hạn.');
                }
            }

            $additionalTotal = 0;
            foreach ($booking->bookingRooms as $br) {
                $extraNights = max(1, $currentCheckOut->diffInDays($newCheckOut));
                $rate = $this->pricingService->calculateNightlyRate(
                    $br->room_type_id,
                    $booking->branch_id,
                    $currentCheckOut,
                    $booking->customer_id
                );
                $extraAmount = $rate * $extraNights;
                $additionalTotal += $extraAmount;

                $br->update([
                    'check_out_date' => $newCheckOut->toDateString(),
                    'nights' => $br->nights + $extraNights,
                    'total_amount' => (float) $br->total_amount + $extraAmount,
                ]);
            }

            $booking->update([
                'check_out_date' => $newCheckOut->toDateString(),
                'total_amount' => (float) $booking->total_amount + $additionalTotal,
            ]);

            $this->logHistory($booking, 'extended', $booking->status->value, $booking->status->value);

            return $booking->fresh(['customer', 'bookingRooms.room']);
        });
    }

    public function changeRoom(int $bookingId, int $oldRoomId, int $newRoomId): Booking
    {
        return DB::transaction(function () use ($bookingId, $oldRoomId, $newRoomId) {
            $booking = Booking::lockForUpdate()->with('bookingRooms.room')->findOrFail($bookingId);

            if (! in_array($booking->status, [BookingStatus::Confirmed, BookingStatus::CheckedIn], true)) {
                throw new BookingInvalidStatusException('Không thể đổi phòng ở trạng thái hiện tại.');
            }

            $bookingRoom = $booking->bookingRooms->firstWhere('room_id', $oldRoomId);
            if (! $bookingRoom) {
                throw new BookingInvalidStatusException('Phòng cũ không thuộc booking này.');
            }

            $newRoom = Room::findOrFail($newRoomId);
            if ($newRoom->status === RoomStatus::Maintenance) {
                throw new RoomMaintenanceException();
            }

            $checkIn = Carbon::parse($bookingRoom->check_in_date);
            $checkOut = Carbon::parse($bookingRoom->check_out_date);

            if ($this->bookingRepository->hasOverlap($newRoomId, $checkIn, $checkOut, $booking->id)) {
                throw new RoomNotAvailableException();
            }

            $oldRoom = $bookingRoom->room;
            $oldRoom->update(['status' => RoomStatus::Available->value]);

            $rate = $this->pricingService->calculateNightlyRate(
                $newRoom->room_type_id,
                $booking->branch_id,
                $checkIn,
                $booking->customer_id
            );
            $lineTotal = $rate * $bookingRoom->nights;

            $bookingRoom->update([
                'room_id' => $newRoom->id,
                'room_type_id' => $newRoom->room_type_id,
                'rate_snapshot' => $rate,
                'total_amount' => $lineTotal,
            ]);

            $newRoom->update([
                'status' => $booking->status === BookingStatus::CheckedIn
                    ? RoomStatus::Occupied->value
                    : RoomStatus::Reserved->value,
            ]);

            $booking->update([
                'total_amount' => $booking->bookingRooms()->sum('total_amount'),
            ]);

            $this->logHistory($booking, 'change_room', $booking->status->value, $booking->status->value);

            return $booking->fresh(['customer', 'bookingRooms.room']);
        });
    }

    public function addService(int $bookingId, int $serviceId, int $quantity, ?float $unitPrice = null): Booking
    {
        return DB::transaction(function () use ($bookingId, $serviceId, $quantity, $unitPrice) {
            $booking = Booking::lockForUpdate()->with('invoices')->findOrFail($bookingId);

            if (! in_array($booking->status, [BookingStatus::Confirmed, BookingStatus::CheckedIn], true)) {
                throw new BookingInvalidStatusException('Không thể thêm dịch vụ ở trạng thái hiện tại.');
            }

            $service = HotelService::findOrFail($serviceId);
            $price = $unitPrice ?? (float) $service->unit_price;
            $total = $price * $quantity;

            BookingServiceItem::create([
                'booking_id' => $booking->id,
                'service_id' => $service->id,
                'quantity' => $quantity,
                'unit_price' => $price,
                'total_amount' => $total,
                'service_date' => now(),
            ]);

            $booking->update([
                'total_amount' => (float) $booking->total_amount + $total,
            ]);

            $invoice = $booking->invoices()->latest()->first();
            if ($invoice) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'service',
                    'reference_id' => $service->id,
                    'description' => $service->name,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total_amount' => $total,
                ]);

                $this->invoiceService->recalculateTotals($invoice);
            }

            $this->logHistory($booking, 'add_service', $booking->status->value, $booking->status->value);

            return $booking->fresh(['serviceItems.service', 'invoices']);
        });
    }

    private function logHistory(Booking $booking, string $action, ?string $from, ?string $to): void
    {
        BookingHistory::create([
            'booking_id' => $booking->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'from_status' => $from,
            'to_status' => $to,
        ]);
    }
}
