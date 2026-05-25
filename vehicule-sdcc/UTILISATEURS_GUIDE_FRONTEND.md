# GUIDE DE SÉCURITÉ FRONTEND - PAGE UTILISATEURS

## 🎯 OBJECTIF

Bien que la sécurité RÉELLE se trouve **côté backend**, le frontend doit refléter ces restrictions pour une meilleure UX.

---

## 📋 ÉLÉMENTS À PROTÉGER DANS LA VUE

### 1. **Sélecteur de Rôle** (Création)

**Situation** : Lors de la création d'un utilisateur, le superadmin ne doit voir que les rôles autorisés.

```html
<!-- ✅ BON - Dynamique selon l'utilisateur -->
<select name="role" id="role-select">
    <option value="employee">Employee</option>
    <option value="admin">Admin</option>
    @if(auth()->user()->hasRole('super_admin'))
        <option value="super_admin">Super Admin</option>
    @endif
</select>

<!-- ❌ MAUVAIS - Toujours montrer super_admin -->
<select name="role" id="role-select">
    <option value="employee">Employee</option>
    <option value="admin">Admin</option>
    <option value="super_admin">Super Admin</option>  <!-- Risqué -->
</select>
```

### 2. **Sélecteur de Rôle** (Modification)

**Situation** : Lors de la modification d'un employee, empêcher la "promotion" UI à super_admin.

```blade
<!-- ✅ BON - Restreindre les options selon le rôle actuel -->
<select name="role" id="role-select">
    <option value="employee" @selected(old('role', $user->primaryRole()) == 'employee')>Employee</option>
    <option value="admin" @selected(old('role', $user->primaryRole()) == 'admin')>Admin</option>
    
    <!-- Afficher super_admin SEULEMENT si l'utilisateur est déjà super_admin -->
    @if($user->hasRole('super_admin'))
        <option value="super_admin" @selected(old('role', $user->primaryRole()) == 'super_admin')>Super Admin</option>
    @endif
</select>

<!-- Avec texte explicatif -->
@if(!$user->hasRole('super_admin') && auth()->user()->hasRole('super_admin'))
    <small class="text-muted">
        <i class="fas fa-info-circle"></i>
        Super Admin ne peut être assigné qu'aux comptes existants avec ce rôle.
    </small>
@endif
```

### 3. **Boutons d'Action**

**Situation** : Certains boutons ne doivent pas s'afficher selon les restrictions.

```blade
<!-- Bouton ÉDITER -->
@can('update', $user)
    <button class="btn btn-sm btn-warning" data-action="edit">
        <i class="fas fa-edit"></i> Éditer
    </button>
@endcan

<!-- Bouton DÉSACTIVER (caché si super_admin ou si déjà inactif) -->
@can('deactivate', $user)
    @if($user->is_active && !$user->hasRole('super_admin'))
        <button class="btn btn-sm btn-danger" data-action="deactivate">
            <i class="fas fa-ban"></i> Désactiver
        </button>
    @endif
@endcan

<!-- Bouton RÉACTIVER (caché si actif ou si super_admin) -->
@can('reactivate', $user)
    @if(!$user->is_active && !$user->hasRole('super_admin'))
        <button class="btn btn-sm btn-success" data-action="reactivate">
            <i class="fas fa-check"></i> Réactiver
        </button>
    @endif
@endcan

<!-- Bouton SUPPRIMER (Caché si own account ou si a réservations) -->
@can('delete', $user)
    @if(auth()->id() !== $user->id)
        <button class="btn btn-sm btn-danger" data-action="delete">
            <i class="fas fa-trash"></i> Supprimer
        </button>
    @else
        <span class="text-muted" title="Cannot delete own account">
            <i class="fas fa-trash"></i> Supprimer (Verrouillé)
        </span>
    @endif
@endcan

<!-- Bouton RESET PASSWORD -->
@can('resetPassword', $user)
    <button class="btn btn-sm btn-info" data-action="reset-password">
        <i class="fas fa-key"></i> Réinitialiser Password
    </button>
@endcan
```

### 4. **Formulaire de Création**

```blade
<form action="{{ route('utilisateurs.store') }}" method="POST" id="user-create-form">
    @csrf
    
    <div class="form-group">
        <label for="name">Nom *</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" 
               name="name" id="name" required>
        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" id="email" required>
        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="password">Password *</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" 
               name="password" id="password" required>
        @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="role">Rôle *</label>
        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
            <option value="">-- Sélectionner --</option>
            <option value="employee" @selected(old('role') == 'employee')>Employee</option>
            <option value="admin" @selected(old('role') == 'admin')>Admin</option>
            @if(auth()->user()->hasRole('super_admin'))
                <option value="super_admin" @selected(old('role') == 'super_admin')>Super Admin</option>
            @endif
        </select>
        @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="service">Service</label>
        <input type="text" class="form-control @error('service') is-invalid @enderror" 
               name="service" id="service">
        @error('service') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-plus"></i> Créer Utilisateur
    </button>
</form>
```

### 5. **Formulaire de Modification**

```blade
<form action="{{ route('utilisateurs.update', $user) }}" method="POST" id="user-edit-form">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="name">Nom *</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" 
               name="name" id="name" value="{{ old('name', $user->name) }}" required>
        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" id="email" value="{{ old('email', $user->email) }}" required>
        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label for="role">Rôle *</label>
        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
            <option value="employee" @selected(old('role', $user->primaryRole()) == 'employee')>Employee</option>
            <option value="admin" @selected(old('role', $user->primaryRole()) == 'admin')>Admin</option>
            
            <!-- Super Admin : option désactivée et visible seulement pour les super_admin accounts -->
            @if($user->hasRole('super_admin'))
                <option value="super_admin" @selected(old('role', $user->primaryRole()) == 'super_admin')>Super Admin</option>
            @endif
        </select>
        @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
        
        <!-- Message informatif -->
        @if(!$user->hasRole('super_admin'))
            <small class="text-muted d-block mt-2">
                <i class="fas fa-lock"></i> Le rôle Super Admin ne peut être assigné que par copie de la base de données ou autorisation manuelle.
            </small>
        @endif
    </div>

    <div class="form-group">
        <label for="service">Service</label>
        <input type="text" class="form-control @error('service') is-invalid @enderror" 
               name="service" id="service" value="{{ old('service', $user->service) }}">
        @error('service') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Mettre à Jour
    </button>
    <a href="{{ route('utilisateurs.index') }}" class="btn btn-secondary">Annuler</a>
</form>
```

### 6. **Modal de Réinitialisation de Password**

```blade
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Réinitialiser mot de passe</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="resetPasswordForm">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="reset-user-id">
                    
                    <div class="form-group">
                        <label for="new-password">Nouveau mot de passe *</label>
                        <input type="password" class="form-control" 
                               name="password" id="new-password" required minlength="8">
                        <small class="form-text text-muted">Minimum 8 caractères</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Confirmer mot de passe *</label>
                        <input type="password" class="form-control" 
                               id="confirm-password" required minlength="8">
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Cette action ne peut pas être annulée.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-key"></i> Réinitialiser Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const pwd = document.getElementById('new-password').value;
    const confirm = document.getElementById('confirm-password').value;
    
    if (pwd !== confirm) {
        alert('Les mots de passe ne correspondent pas.');
        return;
    }
    
    // Soumettre au serveur
    const userId = document.getElementById('reset-user-id').value;
    const url = `/utilisateurs/${userId}/reset-password`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ password: pwd })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✓ Mot de passe réinitialisé avec succès.');
            $('#resetPasswordModal').modal('hide');
        } else {
            alert('✗ Erreur : ' + data.message);
        }
    });
});
</script>
```

---

## ✅ CHECKLIST FRONTEND

- [ ] Sélecteur de rôle : masquer super_admin pour non-superadmin
- [ ] Bouton Éditer : utiliser `@can('update', $user)`
- [ ] Bouton Désactiver : utiliser `@can('deactivate', $user)`
- [ ] Bouton Réactiver : utiliser `@can('reactivate', $user)` (NOUVEAU)
- [ ] Bouton Supprimer : utiliser `@can('delete', $user)`
- [ ] Formulaires : ajouter validations côté client + serveur
- [ ] Messages d'erreur : clairs mais sûrs
- [ ] CSRF tokens : présents dans tous les formulaires
- [ ] Tests : vérifier qu'admin ne peut PAS modifier en superadmin

---

## 🔒 SÉCURITÉ RÉELLE

**IMPORTANT** : Les protections frontend sont pour l'UX seulement.  
**La vraie sécurité** est **côté backend** (Policy + Controllers + Middleware).

```
Si quelqu'un contourne le frontend JavaScript :
- ✅ Les Routes restent protégées par middleware('role:super_admin')
- ✅ Les Policies vérifient chaque action
- ✅ Les Controllers valident les données
- ✅ Les Erreurs 403 s'affichent correctement
```

---

**Frontend + Backend = Sécurité Complète** ✅

