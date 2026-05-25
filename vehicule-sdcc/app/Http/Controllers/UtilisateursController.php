<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UtilisateursController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display users management page (embedded table view)
     */
    public function index(Request $request)
    {
        $users = User::all();
        return view('utilisateurs.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('utilisateurs.create');
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('utilisateurs.edit', compact('user'));
    }

    public function store(StoreUserRequest $request)
    {
        // Check authorization
        $this->authorize('create', User::class);

        $validated = $request->validated();

        // SECURITY: Prevent privilege escalation - only super_admin can create super_admin accounts
        if ($validated['role'] === 'super_admin' && !$request->user()->hasRole('super_admin')) {
            abort(403, 'Only Super Admin can create Super Admin accounts.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'service' => $validated['service'] ?? null,
        ]);

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$validated['role']]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'user' => $user]);
        }

        return redirect()->back()->with('success', 'Utilisateur créé avec succès.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // Check authorization
        $this->authorize('update', $user);

        $validated = $request->validated();

        // SECURITY: Prevent privilege escalation - cannot assign super_admin role unless acting user is super_admin
        if ($validated['role'] === 'super_admin' && !$request->user()->hasRole('super_admin')) {
            abort(403, 'Cannot assign Super Admin role. Only Super Admin can assign this role.');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'service' => $validated['service'] ?? null,
        ]);

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$validated['role']]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Utilisateur mis à jour avec succès.']);
        }

        return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function deactivate(Request $request, User $user)
    {
        // Check authorization
        $this->authorize('deactivate', $user);

        // Prevent deactivating your own account
        if ($user->id === $request->user()->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas désactiver votre propre compte.'], 422);
            }
            return redirect()->back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        // If target is super_admin, ensure it's not the last one
        if ($user->hasRole('super_admin')) {
            $superCount = User::whereHas('roles', function ($q) {
                $q->where('name', 'super_admin');
            })->count();

            if ($superCount <= 1) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Impossible de désactiver le dernier Super Admin.'], 422);
                }
                return redirect()->back()->with('error', 'Impossible de désactiver le dernier Super Admin.');
            }
        }

        $user->update(['is_active' => false]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Compte désactivé avec succès.']);
        }

        return redirect()->back()->with('success', 'Compte désactivé avec succès.');
    }

    public function reactivate(Request $request, User $user)
    {
        // Check authorization
        $this->authorize('reactivate', $user);
        if ($user->is_active) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ce compte est déjà actif.'], 422);
            }
            return redirect()->back()->with('error', 'Ce compte est déjà actif.');
        }

        $user->update(['is_active' => true]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Compte réactivé avec succès.']);
        }

        return redirect()->back()->with('success', 'Compte réactivé avec succès.');
    }

    public function destroy(Request $request, User $user)
    {
        // Check authorization
        $this->authorize('delete', $user);

        if ($user->id === $request->user()->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 422);
            }
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        // Check if user has reservations (demandes)
        if ($user->demandes()->exists()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Impossible de supprimer ce compte : des réservations existent. Désactivez le compte à la place.'], 422);
            }
            return redirect()->back()->with('error', 'Impossible de supprimer ce compte : des réservations existent. Désactivez le compte à la place.');
        }

        // Prevent deleting the last remaining super_admin
        if ($user->hasRole('super_admin')) {
            $superCount = User::whereHas('roles', function ($q) {
                $q->where('name', 'super_admin');
            })->count();

            if ($superCount <= 1) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Impossible de supprimer le dernier Super Admin.'], 422);
                }
                return redirect()->back()->with('error', 'Impossible de supprimer le dernier Super Admin.');
            }
        }

        $user->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Utilisateur supprimé avec succès.']);
        }

        return redirect()->back()->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Show the reset password form for a user
     */
    public function showResetPasswordForm(User $user)
    {
        // Check authorization
        $this->authorize('resetPassword', $user);
        
        return view('utilisateurs.reset-password', compact('user'));
    }

    public function resetPassword(ResetPasswordRequest $request, User $user)
    {
        try {
            // Check authorization
            $this->authorize('resetPassword', $user);

            $validated = $request->validated();

            // Update password
            $user->update(['password' => Hash::make($validated['password'])]);

            // Check if this is a form submission or AJAX request
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mot de passe réinitialisé avec succès.'
                ]);
            }

            // Form submission - redirect with success message
            return redirect()->route('utilisateurs.index')
                ->with('success', "Mot de passe de {$user->name} réinitialisé avec succès.");
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à réinitialiser ce mot de passe.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à réinitialiser ce mot de passe.');
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la réinitialisation.'
                ], 500);
            }
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la réinitialisation.');
        }
    }
}
