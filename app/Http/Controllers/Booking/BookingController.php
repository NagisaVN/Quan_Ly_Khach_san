<?php

namespace App\Http\Controllers\Booking;

use App\DTOs\CreateBookingDto;
use App\Exceptions\BookingInvalidStatusException;
use App\Exceptions\PaymentRequiredException;
use App\Exceptions\RoomMaintenanceException;
use App\Exceptions\RoomNotAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\AddServiceRequest;
use App\Http\Requests\Booking\CancelBookingRequest;
use App\Http\Requests\Booking\ChangeRoomRequest;
use App\Http\Requests\Booking\CheckAvailabilityRequest;
use App\Http\Requests\Booking\ExtendBookingRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\HotelService;
use App\Models\RoomType;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected BookingRepositoryInterface $bookingRepository,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Booking::class);

        $branchId = $this->branchId();
        $bookings = $this->bookingRepository->paginateForBranch($branchId, [
            'status' => $request->get('status'),
            'search' => $request->get('search'),
        ]);

        return view('bookings.index', compact('bookings'));
    }

    public function create(): View
    {
        $this->authorize('create', Booking::class);

        $branchId = $this->branchId();
        $customers = Customer::query()
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();
        $roomTypes = RoomType::query()->where('is_active', true)->orderBy('name')->get();

        return view('bookings.create', compact('customers', 'roomTypes'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $booking = $this->bookingService->createBooking(new CreateBookingDto(
                customerId: (int) $request->customer_id,
                branchId: $this->branchId(),
                checkInDate: Carbon::parse($request->check_in_date),
                checkOutDate: Carbon::parse($request->check_out_date),
                roomIds: array_map('intval', $request->room_ids),
                adults: (int) $request->adults,
                children: (int) ($request->children ?? 0),
                source: $request->source ?? 'offline',
                specialRequests: $request->special_requests,
                createdBy: $request->user()->id,
            ));

            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', 'Tạo booking thành công: '.$booking->booking_code);
        } catch (RoomNotAvailableException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking = $this->bookingRepository->findById($booking->id);
        $services = HotelService::query()
            ->forBranch($booking->branch_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('bookings.show', compact('booking', 'services'));
    }

    public function availability(CheckAvailabilityRequest $request): JsonResponse
    {
        $result = $this->bookingService->checkAvailability(
            $this->branchId(),
            Carbon::parse($request->check_in_date),
            Carbon::parse($request->check_out_date),
            $request->room_ids,
            $request->room_type_id ? (int) $request->room_type_id : null
        );

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $result['total'],
                'available_rooms' => $result['available_rooms']->map(fn ($room) => [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'room_type' => $room->roomType->name,
                    'floor' => $room->floor?->name,
                    'base_price' => $room->roomType->base_price,
                    'status' => $room->status->value,
                ]),
            ],
        ]);
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        $this->authorize('checkIn', $booking);

        try {
            $this->bookingService->checkIn($booking->id);

            return back()->with('success', 'Check-in thành công');
        } catch (BookingInvalidStatusException|RoomMaintenanceException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkOut(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('checkOut', $booking);

        try {
            $this->bookingService->checkOut($booking->id, $request->notes);

            return back()->with('success', 'Check-out thành công');
        } catch (BookingInvalidStatusException|PaymentRequiredException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(CancelBookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);

        try {
            $this->bookingService->cancelBooking($booking->id, $request->cancel_reason);

            return redirect()
                ->route('bookings.index')
                ->with('success', 'Đã hủy booking');
        } catch (BookingInvalidStatusException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function extend(ExtendBookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);

        try {
            $this->bookingService->extendBooking(
                $booking->id,
                Carbon::parse($request->new_check_out_date)
            );

            return back()->with('success', 'Gia hạn booking thành công');
        } catch (BookingInvalidStatusException|RoomNotAvailableException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function changeRoom(ChangeRoomRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);

        try {
            $this->bookingService->changeRoom(
                $booking->id,
                (int) $request->old_room_id,
                (int) $request->new_room_id
            );

            return back()->with('success', 'Đổi phòng thành công');
        } catch (BookingInvalidStatusException|RoomNotAvailableException|RoomMaintenanceException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addService(AddServiceRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);

        try {
            $this->bookingService->addService(
                $booking->id,
                (int) $request->service_id,
                (int) $request->quantity,
                $request->unit_price ? (float) $request->unit_price : null
            );

            return back()->with('success', 'Đã thêm dịch vụ');
        } catch (BookingInvalidStatusException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function branchId(): int
    {
        return (int) session('current_branch_id', auth()->user()->current_branch_id);
    }
}
