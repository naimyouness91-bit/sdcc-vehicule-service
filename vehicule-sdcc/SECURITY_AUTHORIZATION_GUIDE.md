# Système de Rôles et Autorisation - Guide Complet

## Vue d'ensemble

Ce document décrit l'implémentation du système de rôles et d'autorisation pour la gestion des utilisateurs. L'architecture utilise les **Policies Laravel** pour une séparation claire des responsabilités et une sécurité robuste.

## Architecture

### Composants Clés

#### 1. **Model User** (`app/Models/User.php`)
- Utilise le trait `HasRoles` de Spatie Permission
- Méthodes helper : `isSuperAdmin()`, `isAdmin()`, `isEmployee()`

#### 2. **Policy UserPolicy** (`app/Policies/UserPolicy.php`)
Définit toutes les règles d'autorisation :
- `viewAny()` - Voir la liste des utilisateurs
- `view()` - Voir les détails d'un utilisateur
- `create()` - Créer un nouvel utilisateur
- `update()` - Modifier un utilisateur
- `delete()` - Supprimer un utilisateur
- `deactivate()` - Désactiver un utilisateur
- `reactivate()` - Réactiver un utilisateur
- `resetPassword()` - Réinitialiser le mot de passe

#### 3. **Controller UtilisateursController** (`app/Http/Controllers/UtilisateursController.php`)
- Utilise `$this->authorize()` pour vérifier les permissions
- Middleware : `role:super_admin` (protection en amont)
- Méthodes : `index()`, `store()`, `update()`, `destroy()`, `deactivate()`, `reactivate()`, `resetPassword()`

#### 4. **AuthServiceProvider** (`app/Providers/AuthServiceProvider.php`)
- Enregistre la policy `UserPolicy` pour le model `User`
- Contient les gates et règles globales d'autorisation

## Règles d'Autorisation

### Création d'utilisateur
- ✅ **Super Admin** : Peut créer n'importe quel rôle (admin, employee)
- ❌ **Admin** : Impossible
- ❌ **Employee** : Impossible

### Modification d'utilisateur
- ✅ **Super Admin** : Peut modifier n'importe quel utilisateur (y compris d'autres super admins)
- ❌ **Admin** : Impossible de modifier son propre compte
- ❌ **Employee** : Impossible de modifier son propre compte

### Suppression d'utilisateur
- ✅ **Super Admin** : Peut supprimer un admin ou un employee
- ❌ **Admin** : Impossible de supprimer quiconque
- ❌ **Super Admin** : ⚠️ NE PEUT PAS être supprimé (doit être désactivé)
- ❌ Utilisateurs avec réservations : NE PEUVENT PAS être supprimés (doit être désactivés)

### Désactivation d'utilisateur
- ✅ **Super Admin** : Peut désactiver n'importe quel utilisateur (sauf super admin)
- ❌ **Admin** : Impossible de désactiver un admin ou super admin
- ❌ Impossible de désactiver son propre compte

### Réactivation d'utilisateur
- ✅ **Super Admin** : Peut réactiver n'importe quel utilisateur (sauf super admin)
- ❌ **Admin** : Impossible de réactiver
- ❌ Impossible de réactiver un super admin

### Réinitialisation du mot de passe
- ✅ **Super Admin** : Peut réinitialiser le mot de passe de n'importe qui
- ❌ **Admin** : Impossible de réinitialiser le mot de passe d'un admin ou super admin
- ❌ Impossible de réinitialiser son propre mot de passe (utiliser le formulaire de changement)

## Implémentation dans le Controller

### Exemple : Autorisation à l'action de suppression

```php
public function destroy(Request $request, User $user)
{
    // Vérifie automatiquement la policy UserPolicy@delete()
    $this->authorize('delete', $user);
    
    // Si l'autorisation échoue, lance une AuthorizationException (403)
    
    // Vérifications métier supplémentaires
    if ($user->demandes()->exists()) {
        return back()->withErrors(['delete' => 'Utilisateur a des réservations']);
    }
    
    // Effectuer la suppression
    $user->delete();
    
    return back()->with('success', 'Utilisateur supprimé');
}
```

### Utilisation de `authorize()`

Laravel lance automatiquement une `AuthorizationException` si la policy retourne `false` :

```php
$this->authorize('delete', $user);
// Équivalent à :
if ($this->cannot('delete', $user)) {
    throw new AuthorizationException();
}
```

## Réponses de la Policy

Les policies peuvent retourner :

1. **Boolean** (`true` / `false`)
   ```php
   public function delete(User $user, User $target): bool
   {
       return $user->hasRole('super_admin');
   }
   ```

2. **Response Object** (avec messages personnalisés)
   ```php
   public function delete(User $user, User $target): Response|bool
   {
       if ($user->id === $target->id) {
           return Response::deny('Vous ne pouvez pas supprimer votre propre compte.');
       }
       return true;
   }
   ```

## Confirmation de Suppression

### Frontend - Ajouter une confirmation (Blade)

```blade
<!-- Dans le tableau d'utilisateurs -->
<form method="POST" action="{{ route('utilisateurs.destroy', $user) }}" 
      id="delete-form-{{ $user->id }}" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<button type="button" class="btn btn-danger btn-sm"
        onclick="confirmDelete('{{ $user->name }}', '{{ $user->id }}')">
    Supprimer
</button>

<!-- Script de confirmation -->
<script>
function confirmDelete(userName, userId) {
    const message = `Êtes-vous sûr de vouloir supprimer "${userName}" ?\n\n⚠️ Cette action est irréversible!`;
    
    if (confirm(message)) {
        document.getElementById(`delete-form-${userId}`).submit();
    }
}
</script>
```

### Frontend - Confirmation SweetAlert (recommandé)

```blade
<!-- Inclure SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<button type="button" class="btn btn-danger btn-sm"
        @click="deleteUser('{{ $user->id }}', '{{ $user->name }}')">
    Supprimer
</button>

<script>
function deleteUser(userId, userName) {
    Swal.fire({
        title: 'Confirmer la suppression',
        html: `Supprimer <strong>${userName}</strong> ?<br/><span style="color:red;">⚠️ Cette action est irréversible!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${userId}`).submit();
        }
    });
}
</script>
```

## Gestion des Comptes Super Admin

### Créer un Super Admin initial

Via le seeder `SuperAdminSeeder` :

```bash
php artisan db:seed --class=SuperAdminSeeder
```

Le seeder utilise les variables d'environnement du fichier `.env` :

```env
SUPER_ADMIN_NAME='Super Admin'
SUPER_ADMIN_EMAIL=superadmin@sdcc.ma
SUPER_ADMIN_PASSWORD=SuperAdmin123456
SUPER_ADMIN_SERVICE='Direction Generale'
```

### ⚠️ Remarques importantes

- **Pas de suppression de super admin** : Un super admin ne peut jamais être supprimé (même par un autre super admin)
- **Seulement la désactivation** : Utilisez la désactivation pour "supprimer" un super admin de l'action
- **Super admin par défaut** : Le seeder crée un compte super admin au démarrage avec les credentials du `.env`
- **Production** : Changez toujours le mot de passe du super admin par défaut dans `.env`

## Cas d'Usage - Matrice de Contrôle d'Accès

| Action | Super Admin | Admin | Employee |
|--------|-------------|-------|----------|
| Voir liste utilisateurs | ✅ | ❌ | ❌ |
| Créer utilisateur | ✅ | ❌ | ❌ |
| Modifier utilisateur | ✅ | ❌ | ❌ |
| Supprimer utilisateur | ✅* | ❌ | ❌ |
| Désactiver utilisateur | ✅ | ❌ | ❌ |
| Réactiver utilisateur | ✅ | ❌ | ❌ |
| Réinitialiser mot de passe | ✅ | ❌ | ❌ |

*Super admin ne peut pas être supprimé, seulement désactivé

## Tests d'Autorisation

### Utiliser Gate dans les vues

```blade
@can('delete', $user)
    <!-- Bouton de suppression visible -->
@else
    <!-- Bouton de suppression caché -->
@endcan

@cannot('delete', $user)
    <!-- Ceci s'exécute si l'utilisateur n'a pas la permission -->
@endcannot
```

### Tests dans le Controller

```php
if (auth()->user()->can('delete', $user)) {
    // Autorisation accordée
}

if (auth()->user()->cannot('delete', $user)) {
    // Autorisation refusée
}
```

## Gestion des Erreurs

### AuthorizationException (403)

```php
// Lance automatiquement si la policy retourne false ou Response::deny()
$this->authorize('delete', $user);

// Réponse : 403 Forbidden
// Affiche la page d'erreur 403
```

### Erreurs JSON (API)

```php
public function destroy(Request $request, User $user)
{
    $this->authorize('delete', $user);
    
    if ($request->expectsJson()) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 403);
    }
    
    abort(403);
}
```

## Sécurité - Bonnes Pratiques

✅ **À faire**
- Utiliser les policies pour toutes les autorisations
- Toujours valider les rôles côté serveur (ne pas faire confiance au client)
- Utiliser `$this->authorize()` au début de chaque action
- Hacher les mots de passe avec `Hash::make()`
- Utiliser des variables d'environnement pour les credentials
- Ajouter une confirmation avant les actions destructives
- Logger les actions sensibles (suppression, modification de rôle)

❌ **À éviter**
- Vérifier les rôles directement dans la vue (peut être contourné)
- Stocker les mots de passe en clair
- Commiter les credentials dans Git
- Sauter les vérifications côté serveur
- Permettre la suppression de super admin
- Gérer les autorisations uniquement côté frontend

## Audit et Logging

### Exemple : Logger les suppressions d'utilisateur

```php
public function destroy(Request $request, User $user)
{
    $this->authorize('delete', $user);
    
    Log::warning('User deletion', [
        'deleted_user_id' => $user->id,
        'deleted_user_email' => $user->email,
        'deleted_by' => auth()->id(),
        'timestamp' => now(),
    ]);
    
    $user->delete();
    
    return back()->with('success', 'Utilisateur supprimé');
}
```

## Dépannage

### Je ne vois pas le bouton de suppression

**Vérifier :**
1. Êtes-vous authentifié ?
2. Votre rôle est-il `super_admin` ?
3. La view utilise-t-elle `@can('delete', $user)` ?

```blade
<!-- Debug : Voir votre rôle actuel -->
{{ auth()->user()->roles->pluck('name') }}
```

### J'obtiens 403 Forbidden

**Vérifications :**
1. Vérifié que la policy retourne `true` ?
2. Le rôle est-il correctement assigné ?
3. Vérifier les logs Laravel : `storage/logs/laravel.log`

```php
// Debug dans le controller
\Log::info('User roles', ['roles' => auth()->user()->roles->pluck('name')]);
```

## Fichiers Modifiés

- ✅ `app/Policies/UserPolicy.php` - CRÉÉ (nouvelle policy)
- ✅ `app/Providers/AuthServiceProvider.php` - MODIFIÉ (enregistré la policy)
- ✅ `app/Http/Controllers/UtilisateursController.php` - MODIFIÉ (utilise authorize)

## Prochaines Étapes

1. **Ajouter la confirmation de suppression** dans la vue `utilisateurs.index`
2. **Implémenter l'audit** : Enregistrer toutes les actions sensibles
3. **Ajouter les notifications** : Notifier les utilisateurs après les actions
4. **Améliorer le logging** : Créer un historique des modifications d'utilisateurs
5. **Tester les cas limites** : Super admin supprimant un super admin, etc.

---

**Dernière mise à jour** : 4 mai 2026  
**Auteur** : GitHub Copilot  
**Version** : 1.0
