<?php

namespace App\Http\Controllers\Portal;

use App\DTOs\CreateBookingDto;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\RoomType;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function dashboard(): View
    {
        $customer = $this->customer();
        $bookings = Booking::where('customer_id', $customer->id)->orderByDesc('id')->limit(5)->get();

        return view('portal.dashboard', compact('customer', 'bookings'));
    }

    public function bookings(): View
    {
        $customer = $this->customer();
        $bookings = Booking::where('customer_id', $customer->id)->orderByDesc('id')->paginate(10);

        return view('portal.bookings.index', compact('bookings', 'customer'));
    }

    public function showBooking(Booking $booking): View
    {
        $customer = $this->customer();
        abort_unless((int) $booking->customer_id === (int) $customer->id, 403);
        $booking->load(['bookingRooms.room', 'invoices']);

        return view('portal.bookings.show', compact('booking', 'customer'));
    }

    public function createBooking(): View
    {
        $customer = $this->customer();
        $roomTypes = RoomType::where('is_active', true)->orderBy('name')->get();

        return view('portal.bookings.create', compact('customer', 'roomTypes'));
    }

    public function storeBooking(Request $request): RedirectResponse
    {
        $customer = $this->customer();
        $validated = $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_ids' => 'required|array|min:1',
            'room_ids.*' => 'integer|exists:rooms,id',
            'adults' => 'required|integer|min:1',
        ]);

        $booking = $this->bookingService->createBooking(new CreateBookingDto(
            customerId: $customer->id,
            branchId: $customer->branch_id,
            checkInDate: Carbon::parse($validated['check_in_date']),
            checkOutDate: Carbon::parse($validated['check_out_date']),
            roomIds: array_map('intval', $validated['room_ids']),
            adults: (int) $validated['adults'],
            source: 'online',
        ));

        return redirect()->route('portal.bookings.show', $booking)->with('success', 'Đặt phòng thành công');
    }

    protected function customer(): Customer
    {
        return Customer::where('user_id', auth()->id())->firstOrFail();
    }
}
