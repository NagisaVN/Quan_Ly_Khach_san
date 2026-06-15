<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetBranchContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $branchId = $request->session()->get('current_branch_id', $user->current_branch_id);

            if (! $branchId) {
                $defaultBranch = $user->branches()->wherePivot('is_default', true)->first();
                $branchId = $defaultBranch?->id;
            }

            if ($branchId) {
                $request->session()->put('current_branch_id', $branchId);

                if ((int) $user->current_branch_id !== (int) $branchId) {
                    $user->update(['current_branch_id' => $branchId]);
                }
            }
        }

        return $next($request);
    }
}
