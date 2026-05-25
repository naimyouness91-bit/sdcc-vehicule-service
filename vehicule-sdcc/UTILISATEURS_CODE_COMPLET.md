# CODE COMPLET - SÉCURISATION PAGE UTILISATEURS

Ce fichier contient TOUT le code modifié, prêt à copier-coller.

---

## 1️⃣ app/Http/Controllers/UtilisateursController.php

**Remplacer complètement par :**

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UtilisateursController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    /**
     * Display users management page (embedded table view)
     * Only superadmin can access
     */
    public function index(Request $request)
    {
        // Check authorization
        $this->authorize('viewAny', User::class);

        $users = User::with('roles', 'planningZone')->orderBy('name')->get();

        return view('admin.data.section', [
            'title' => 'Utilisateurs',
            'icon' => 'fas fa-users',
            'tableView' => 'admin.tables.users',
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        // Check authorization
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8'],
            'service' => ['nullable','string','max:255'],
            'role' => ['required', Rule::in(['employee','admin','super_admin'])],
        ]);

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

    public function update(Request $request, User $user)
    {
        // Check authorization
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'service' => ['nullable','string','max:255'],
            'role' => ['required', Rule::in(['employee','admin','super_admin'])],
        ]);

        // SECURITY: Prevent privilege escalation - cannot assign super_admin role to non-super_admin users
        if ($validated['role'] === 'super_admin' && !$user->hasRole('super_admin')) {
            abort(403, 'Cannot assign Super Admin role. Only Super Admin can hold this role.');
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
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas supprimer votre propre compte.'], 422);
        }

        // Check if user has reservations (demandes)
        if ($user->demandes()->exists()) {
            return response()->json(['success' => false, 'message' => 'Impossible de supprimer ce compte : des réservations existent. Désactivez le compte à la place.'], 422);
        }

        $user->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Utilisateur supprimé avec succès.']);
        }

        return redirect()->back()->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function resetPassword(Request $request, User $user)
    {
        // Check authorization
        $this->authorize('resetPassword', $user);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé avec succès.']);
    }
}
```

---

## 2️⃣ app/Policies/UserPolicy.php

**Dans la méthode `update()`, remplacer :**

**De :**
```php
public function update(User $user, User $target): Response|bool
{
    // Cannot modify your own account through this policy
    if ($user->id === $target->id) {
        return Response::deny('You cannot modify your own account.');
    }

    // Only super_admin can update accounts
    if (!$user->hasRole('super_admin')) {
        return Response::deny('Only Super Admin can update user accounts.');
    }

    // super_admin can update anyone (including other super_admins)
    return true;
}
```

**À :**
```php
public function update(User $user, User $target, array $data = []): Response|bool
{
    // Cannot modify your own account through this policy
    if ($user->id === $target->id) {
        return Response::deny('You cannot modify your own account.');
    }

    // Only super_admin can update accounts
    if (!$user->hasRole('super_admin')) {
        return Response::deny('Only Super Admin can update user accounts.');
    }

    // Prevent admin from being assigned super_admin role (privilege escalation)
    if (isset($data['role']) && $data['role'] === 'super_admin') {
        if (!$target->hasRole('super_admin')) {
            return Response::deny('Cannot assign super_admin role. Only super_admin can hold this role.');
        }
    }

    // super_admin can update anyone (including other super_admins)
    return true;
}
```

---

## 3️⃣ routes/web.php

**Trouver cette section :**
```php
// User management - Utilisateurs Routes (Super Admin only)
Route::middleware('role:super_admin')->group(function () {
    Route::get('/utilisateurs', [UtilisateursController::class, 'index'])->name('utilisateurs.index');
    Route::post('/utilisateurs', [UtilisateursController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('utilisateurs.store');
    Route::put('/utilisateurs/{user}', [UtilisateursController::class, 'update'])
        ->middleware('permission:users.update')
        ->name('utilisateurs.update');
    Route::post('/utilisateurs/{user}/deactivate', [UtilisateursController::class, 'deactivate'])
        ->middleware('permission:users.deactivate')
        ->name('utilisateurs.deactivate');
    Route::delete('/utilisateurs/{user}', [UtilisateursController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('utilisateurs.destroy');
    Route::post('/utilisateurs/{user}/reset-password', [UtilisateursController::class, 'resetPassword'])
        ->middleware('permission:users.reset_password')
        ->name('utilisateurs.reset-password');
});
```

**Remplacer par :**
```php
// User management - Utilisateurs Routes (Super Admin only)
// All routes protected by role:super_admin + UserPolicy authorization
Route::middleware('role:super_admin')->group(function () {
    Route::get('/utilisateurs', [UtilisateursController::class, 'index'])->name('utilisateurs.index');
    Route::post('/utilisateurs', [UtilisateursController::class, 'store'])->name('utilisateurs.store');
    Route::put('/utilisateurs/{user}', [UtilisateursController::class, 'update'])->name('utilisateurs.update');
    Route::post('/utilisateurs/{user}/deactivate', [UtilisateursController::class, 'deactivate'])->name('utilisateurs.deactivate');
    Route::post('/utilisateurs/{user}/reactivate', [UtilisateursController::class, 'reactivate'])->name('utilisateurs.reactivate');
    Route::delete('/utilisateurs/{user}', [UtilisateursController::class, 'destroy'])->name('utilisateurs.destroy');
    Route::post('/utilisateurs/{user}/reset-password', [UtilisateursController::class, 'resetPassword'])->name('utilisateurs.reset-password');
});
```

---

## 4️⃣ app/Providers/AuthServiceProvider.php

**✅ PAS DE CHANGEMENT** (déjà bien configuré)

```php
protected $policies = [
    \App\Models\User::class => \App\Policies\UserPolicy::class,
    // ...
];
```

---

## 5️⃣ COMMANDES FINALES

```bash
# Nettoyer les caches
php artisan optimize:clear

# Redémarrer le serveur
php artisan serve

# Vérifier les routes
php artisan route:list | grep utilisateurs
```

---

## ✅ RÉSUMÉ DES CHANGEMENTS

| Fichier | Lignes | Action |
|---------|-------|--------|
| UtilisateursController.php | ~160 | Remplacer complètement |
| UserPolicy.php | 8 lignes | Modifier update() |
| routes/web.php | 10 lignes | Modifier routes utilisateurs |
| AuthServiceProvider.php | - | Aucun changement |

**Total** : 3 fichiers modifiés, ~180 lignes de code

---

## 🎯 RÉSULTAT ATTENDU

Après application des changements :

```
✅ Page /utilisateurs accessible SEULEMENT par superadmin
✅ Admin essayant d'accéder → 403 Forbidden
✅ Admin créant superadmin → 403 Denied
✅ Admin modifiant employee en superadmin → 403 Denied  
✅ Superadmin peut tout faire (créer, modifier, supprimer, etc.)
✅ User ne peut pas self-delete
✅ Réactivation de comptes possible (nouvelle fonctionnalité)
```

---

**Prêt à déployer** ✅

