<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine if the user can view the users list.
     * Only super_admin can access user management.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can view a specific user.
     * Only super_admin can view user details.
     */
    public function view(User $user, User $target): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can create a new user.
     * Only super_admin can create users.
     */
    public function create(User $user): bool
    {
        // Allow both admin and super_admin to create users (admins create employee accounts)
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine if the user can update another user.
     * Super admin can update anyone (but cannot change role to super_admin for non-super_admins).
     * SECURITY: Prevent privilege escalation - only super_admin can be assigned super_admin role.
     */
    public function update(User $user, User $target, array $data = []): Response|bool
    {
        // Only super_admin can update accounts
        if (!$user->hasRole('super_admin')) {
            return Response::deny('Only Super Admin can update user accounts.');
        }

        // Additional checks for role assignment are handled server-side in the controller
        // super_admin may update any account (including self and other super_admins)
        return true;
    }

    /**
     * Determine if the user can deactivate another user.
     * Super admin can deactivate anyone except:
     * - Themselves (cannot deactivate own account)
     * - The last remaining super_admin (must have at least one active super_admin)
     * Regular admins cannot deactivate admin accounts.
     */
    public function deactivate(User $user, User $target): Response|bool
    {
        // Cannot deactivate yourself
        if ($user->id === $target->id) {
            return Response::deny('You cannot deactivate your own account.');
        }

        // Only super_admin can deactivate anyone
        if (!$user->hasRole('super_admin')) {
            return Response::deny('Only Super Admin can deactivate accounts.');
        }

        // Check if target is the last super_admin (delegation to controller for final check)
        // Note: The controller will verify the count to ensure we don't deactivate the last super_admin
        return true;
    }

    /**
     * Determine if the user can reactivate another user.
     * Only super_admin can reactivate accounts.
     */
    public function reactivate(User $user, User $target): Response|bool
    {
        // Only super_admin can reactivate accounts
        if (!$user->hasRole('super_admin')) {
            return Response::deny('Only Super Admin can reactivate accounts.');
        }

        return true;
    }

    /**
     * Determine if the user can delete another user.
     * 
     * DELETION RULES:
     * - Only super_admin can delete anyone (including other super_admins)
     * - Cannot delete your own account (self-delete protection)
     * - Users with reservations cannot be deleted (must deactivate first)
     * 
     * @param User $user The authenticated user performing the deletion
     * @param User $target The user to be deleted
     * @return Response|bool True if allowed, Response::deny() with message if not
     */
    public function delete(User $user, User $target): Response|bool
    {
        // Self-delete protection: Cannot delete yourself
        if ($user->id === $target->id) {
            return Response::deny('You cannot delete your own account.');
        }

        // Only super_admin can delete anyone (including other super_admins)
        if (!$user->hasRole('super_admin')) {
            return Response::deny('Only Super Admin can delete user accounts.');
        }

        // Authorization passed - deletion is allowed
        // Note: Additional validation for reservations is handled in the Controller
        return true;
    }

    /**
     * Determine if the user can reset the password of another user.
     * Only super_admin can reset passwords for admin accounts.
     */
    public function resetPassword(User $user, User $target): Response|bool
    {
        // Cannot reset your own password through this action
        if ($user->id === $target->id) {
            return Response::deny('Use the change password feature for your own account.');
        }

        // Only super_admin can reset admin passwords
        if ($target->hasRole('admin') && !$user->hasRole('super_admin')) {
            return Response::deny('Only Super Admin can reset admin passwords.');
        }

        // Only super_admin can reset passwords
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can confirm deletion of another user.
     * This is called before actually deleting to check for related data.
     */
    public function forceDelete(User $user, User $target): bool
    {
        // Force deletion requires same permissions as regular delete
        return $this->delete($user, $target) === true;
    }
}
