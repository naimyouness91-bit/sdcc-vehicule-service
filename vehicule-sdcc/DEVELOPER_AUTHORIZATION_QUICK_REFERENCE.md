# Système d'Autorisation - Guide Rapide pour Développeurs

## 🚀 Quick Start

### Vérifier une Permission dans le Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;

class MyController extends Controller
{
    public function deleteUser(User $user)
    {
        // Méthode 1 : Utiliser authorize() - Lance une AuthorizationException si refusé
        $this->authorize('delete', $user);
        
        // Méthode 2 : Utiliser can() - Retourne boolean
        if (auth()->user()->can('delete', $user)) {
            // L'utilisateur a la permission
        }
        
        // Méthode 3 : Utiliser cannot() - Retourne boolean
        if (auth()->user()->cannot('delete', $user)) {
            abort(403, 'Unauthorized');
        }
        
        // Votre logique...
    }
}
```

### Vérifier une Permission dans la Vue (Blade)

```blade
{{-- Méthode @can --}}
@can('delete', $user)
    <button class="btn btn-danger">Supprimer</button>
@else
    <p class="text-muted">Vous n'avez pas la permission de supprimer</p>
@endcan

{{-- Méthode @cannot --}}
@cannot('delete', $user)
    <p class="text-muted">Accès refusé</p>
@else
    <button class="btn btn-danger">Supprimer</button>
@endcannot

{{-- Vérifier directement --}}
@if(auth()->user()->can('delete', $user))
    <button class="btn btn-danger">Supprimer</button>
@endif
```

## 📋 Actions Disponibles dans UserPolicy

### 1. viewAny() - Voir la liste des utilisateurs

```php
// Controller
$this->authorize('viewAny', User::class);

// Vue
@can('viewAny', App\Models\User::class)
    <!-- Afficher la page de gestion -->
@endcan

// JavaScript/API
const canViewUsers = auth().user().can('viewAny', User);
```

**Qui peut?** : Super Admin uniquement

### 2. view() - Voir les détails d'un utilisateur

```php
$this->authorize('view', $user);

@can('view', $user)
    <p>Email: {{ $user->email }}</p>
@endcan
```

**Qui peut?** : Super Admin uniquement

### 3. create() - Créer un utilisateur

```php
// Dans le store() method
$this->authorize('create', User::class);

@can('create', App\Models\User::class)
    <button>Créer un utilisateur</button>
@endcan
```

**Qui peut?** : Super Admin uniquement

### 4. update() - Modifier un utilisateur

```php
// Dans le update() method
$this->authorize('update', $user);

// Vue - Afficher le formulaire seulement si autorisé
@can('update', $user)
    <form method="POST" action="{{ route('utilisateurs.update', $user) }}">
        <!-- Formulaire de modification -->
    </form>
@endcan
```

**Qui peut?** : Super Admin uniquement  
**Protection** : Impossible de modifier son propre compte

### 5. delete() - Supprimer un utilisateur

```php
// Dans le destroy() method
$this->authorize('delete', $user);

// Vue
@can('delete', $user)
    <button onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')">
        Supprimer
    </button>
@endcan
```

**Qui peut?** : Super Admin uniquement  
**Protection** :
- Impossible de supprimer son propre compte
- Impossible de supprimer un Super Admin (erreur 403)
- Impossible de supprimer un utilisateur avec réservations

### 6. deactivate() - Désactiver un utilisateur

```php
$this->authorize('deactivate', $user);

@can('deactivate', $user)
    <button class="btn btn-warning">Désactiver</button>
@endcan
```

**Qui peut?** : Super Admin uniquement  
**Protection** :
- Impossible de désactiver son propre compte
- Impossible de désactiver un Super Admin

### 7. reactivate() - Réactiver un utilisateur

```php
$this->authorize('reactivate', $user);

@can('reactivate', $user)
    <button class="btn btn-success">Réactiver</button>
@endcan
```

**Qui peut?** : Super Admin uniquement

### 8. resetPassword() - Réinitialiser le mot de passe

```php
$this->authorize('resetPassword', $user);

@can('resetPassword', $user)
    <button class="btn btn-info">Réinitialiser le mot de passe</button>
@endcan
```

**Qui peut?** : Super Admin uniquement  
**Protection** : Impossible de réinitialiser le mot de passe d'un Super Admin (sauf si vous êtes aussi Super Admin)

## 🛡️ Cas d'Usage Courants

### Cas 1 : Je veux vérifier si je suis Super Admin

```php
// Dans le controller
if (auth()->user()->isSuperAdmin()) {
    // Vous êtes Super Admin
}

// Dans la vue
@if(auth()->user()->isSuperAdmin())
    <!-- Options avancées -->
@endif

// Utiliser le rôle directement
if (auth()->user()->hasRole('super_admin')) {
    // Vous avez le rôle
}
```

### Cas 2 : Afficher un bouton d'action conditionnel

```blade
@can('delete', $user)
    <button class="btn btn-danger btn-sm" 
            onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}', '{{ $user->email }}')">
        <i class="fas fa-trash"></i> Supprimer
    </button>
@else
    <!-- Afficher un message ou rien du tout -->
    @if(auth()->user()->cannot('delete', $user))
        <span class="text-muted" title="Accès refusé">
            <i class="fas fa-lock"></i> Protégé
        </span>
    @endif
@endcan
```

### Cas 3 : Afficher différentes interfaces selon le rôle

```blade
<div class="user-card">
    <h3>{{ $user->name }}</h3>
    
    @if(auth()->user()->can('update', $user))
        {{-- Interface admin avec options d'édition --}}
        <div class="admin-controls">
            <button class="btn btn-primary">Modifier</button>
            <button class="btn btn-danger">Supprimer</button>
        </div>
    @else
        {{-- Interface utilisateur standard --}}
        <p class="text-muted">Affichage en lecture seule</p>
    @endif
</div>
```

### Cas 4 : Protéger une route dans le Controller

```php
class UtilisateursController extends Controller
{
    public function __construct()
    {
        // Option 1 : Protection globale par rôle (middleware)
        $this->middleware('role:super_admin');
    }
    
    public function destroy(User $user)
    {
        // Option 2 : Protection spécifique par action (policy)
        $this->authorize('delete', $user);
        
        // Suppression sécurisée
        $user->delete();
    }
}
```

### Cas 5 : Combiner plusieurs vérifications

```php
// Vérifier plusieurs permissions
if (auth()->user()->can('update', $user) && auth()->user()->can('delete', $user)) {
    // L'utilisateur peut modifier ET supprimer
}

// Utiliser des scopes
$managedUsers = User::where('is_active', true)
    ->whereDoesntHave('roles', function($query) {
        $query->where('name', 'super_admin');
    })
    ->get();

// Filtrer par permission sur collection
$deletableUsers = $users->filter(function($user) {
    return auth()->user()->can('delete', $user);
});
```

## ⚠️ Pièges Courants et Solutions

### ❌ Piège 1 : Vérifier seulement le rôle côté frontend

```blade
{{-- ❌ MAUVAIS - Peut être contourné --}}
@if(auth()->user()->hasRole('super_admin'))
    <button onclick="deleteUser()">Supprimer</button>
@endif
```

```blade
{{-- ✅ BON - Utiliser la policy --}}
@can('delete', $user)
    <button onclick="deleteUser()">Supprimer</button>
@endcan
```

### ❌ Piège 2 : Oublier la vérification côté serveur

```php
// ❌ MAUVAIS - Frontend peut être contourné
function deleteUser(userId) {
    axios.delete('/users/' + userId);
}
```

```php
// ✅ BON - Vérifier côté serveur
public function destroy(User $user)
{
    $this->authorize('delete', $user); // ← Essentiel!
    $user->delete();
}
```

### ❌ Piège 3 : Mélanger la logique dans le controller

```php
// ❌ MAUVAIS - Mélange avec la logique métier
public function destroy(User $user)
{
    if (!auth()->user()->hasRole('super_admin')) {
        abort(403);
    }
    
    if ($user->id === auth()->id()) {
        abort(403);
    }
    
    if ($user->hasRole('super_admin')) {
        abort(403);
    }
    
    if ($user->demandes()->exists()) {
        abort(422);
    }
    
    $user->delete();
}
```

```php
// ✅ BON - Déléguer à la policy
public function destroy(User $user)
{
    $this->authorize('delete', $user); // Politique centralisée
    
    if ($user->demandes()->exists()) {
        abort(422, 'User has reservations'); // Logique métier
    }
    
    $user->delete();
}
```

## 🔍 Debugging et Troubleshooting

### Voir mes permissions

```php
// Dans tinker ou un controller
$user = auth()->user();

// Afficher tous les rôles
$user->roles->pluck('name'); // ['super_admin']

// Afficher toutes les permissions
$user->permissions->pluck('name');

// Vérifier une permission spécifique
$user->can('delete', User::first());

// Vérifier directement dans la policy
resolve(\App\Policies\UserPolicy::class)->delete($user, User::first());
```

### Tester une policy

```php
// Dans le controller ou un test
$superAdmin = User::where('email', 'superadmin@sdcc.ma')->first();
$targetUser = User::find(10);

// Tester
$this->assertTrue($superAdmin->can('delete', $targetUser));
$this->assertFalse($targetUser->can('delete', $superAdmin));
```

### Vérifier les logs d'erreur

```bash
# Voir les dernières erreurs
tail -f storage/logs/laravel.log | grep "authorization\|denied\|403"

# Ou chercher spécifiquement
grep "AuthorizationException" storage/logs/laravel.log
```

## 📚 Ressources Complémentaires

### Fichiers Clés
- [Policy UserPolicy](app/Policies/UserPolicy.php) - Logique d'autorisation
- [Controller UtilisateursController](app/Http/Controllers/UtilisateursController.php) - Utilisation des policies
- [Model User](app/Models/User.php) - Rôles et permissions
- [AuthServiceProvider](app/Providers/AuthServiceProvider.php) - Configuration

### Documentation Officielle
- [Laravel Authorization](https://laravel.com/docs/authorization)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Policies](https://laravel.com/docs/authorization#creating-policies)

### Guides Complets
- [SECURITY_AUTHORIZATION_GUIDE.md](SECURITY_AUTHORIZATION_GUIDE.md) - Guide complet
- [ROLE_AUTHORIZATION_IMPLEMENTATION.md](ROLE_AUTHORIZATION_IMPLEMENTATION.md) - Checklist de mise en œuvre

## 💡 Conseils Pro

### Conseil 1 : Toujours utiliser authorize() au début de la méthode

```php
public function destroy(User $user)
{
    // ✅ Mettre la vérification en premier
    $this->authorize('delete', $user);
    
    // Puis la logique métier
    $user->delete();
}
```

### Conseil 2 : Utiliser @can dans les templates pour l'UX

```blade
{{-- Fournit une meilleure expérience utilisateur --}}
@can('delete', $user)
    <button class="btn btn-danger">Supprimer</button>
@else
    {{-- Montrer pourquoi c'est désactivé --}}
    <button class="btn btn-danger" disabled title="Vous n'avez pas la permission">
        Supprimer
    </button>
@endcan
```

### Conseil 3 : Ajouter des confirmations pour les actions destructives

```blade
<button onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')">
    Supprimer
</button>

<script>
function confirmDelete(userId, userName) {
    if (confirm(`Supprimer "${userName}" définitivement ?`)) {
        document.getElementById(`delete-form-${userId}`).submit();
    }
}
</script>
```

### Conseil 4 : Logger les actions sensibles

```php
public function destroy(User $user)
{
    $this->authorize('delete', $user);
    
    Log::warning('User deleted', [
        'deleted_user_id' => $user->id,
        'deleted_by' => auth()->id(),
        'timestamp' => now(),
    ]);
    
    $user->delete();
}
```

---

**Besoin d'aide?** Consultez [SECURITY_AUTHORIZATION_GUIDE.md](SECURITY_AUTHORIZATION_GUIDE.md) ou les fichiers source.

**Dernière mise à jour** : 4 mai 2026
