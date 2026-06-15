<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('payments.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('payments.view') && $this->sameBranch($user, $invoice);
    }

    protected function sameBranch(User $user, Invoice $invoice): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        $branchId = session('current_branch_id', $user->current_branch_id);

        return (int) $invoice->branch_id === (int) $branchId;
    }
}
