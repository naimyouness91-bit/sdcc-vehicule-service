<?php

namespace App\Policies;

use App\Models\Demande;
use App\Models\User;

class DemandPolicy
{
    /**
     * Determine whether the user can view any model.
     */
    public function viewAny(User $user): bool
    {
        return true;  // Authenticated users can see the list filtered for them
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Demande $demande): bool
    {
        // Owner can view own demand
        if ($demande->user_id === $user->id) {
            return true;
        }

        // Admins can view all demands
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create demands for themselves
        return $user->is_active ?? true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Demande $demande): bool
    {
        // Admins can update any demand
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Owner can only update if still pending
        return $demande->user_id === $user->id && $demande->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Demande $demande): bool
    {
        // Admins can delete any demand
        if ($user->hasRole(['admin', 'super_admin'])) {
            return true;
        }

        // Owner can only delete if still pending
        return $demande->user_id === $user->id && $demande->status === 'pending';
    }

    /**
     * Determine whether the user can approve a demand.
     */
    public function approve(User $user, Demande $demande): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can reject a demand.
     */
    public function reject(User $user, Demande $demande): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can cancel a demand.
     */
    public function cancel(User $user, Demande $demande): bool
    {
        // Owner can cancel own demand if approved
        if ($demande->user_id === $user->id && $demande->status === 'approved') {
            return true;
        }

        // Admins can cancel any demand
        return $user->hasRole(['admin', 'super_admin']);
    }
}
