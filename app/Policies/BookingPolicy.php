<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('bookings.view');
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->can('bookings.view')
            && $this->sameBranch($user, $booking);
    }

    public function create(User $user): bool
    {
        return $user->can('bookings.create');
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->can('bookings.update')
            && $this->sameBranch($user, $booking);
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->can('bookings.delete')
            && $this->sameBranch($user, $booking);
    }

    public function checkIn(User $user, Booking $booking): bool
    {
        return $user->can('bookings.update')
            && $this->sameBranch($user, $booking);
    }

    public function checkOut(User $user, Booking $booking): bool
    {
        return $user->can('bookings.update')
            && $this->sameBranch($user, $booking);
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->can('bookings.update')
            && $this->sameBranch($user, $booking);
    }

    protected function sameBranch(User $user, Booking $booking): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        $branchId = session('current_branch_id', $user->current_branch_id);

        return (int) $booking->branch_id === (int) $branchId;
    }
}
