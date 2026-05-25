# 🔐 GUIDE: CRÉER UN COMPTE SUPER ADMIN SÉCURISÉ

## 📋 RÉSUMÉ RAPIDE

```bash
# 1. Définir le mot de passe en production
export SUPERADMIN_PASSWORD="VotreMotDePasseSecurise123!"

# 2. Exécuter le seeder
php artisan db:seed --class=SuperAdminSeeder

# 3. Connexion
Email: superadmin@sdcc.ma
Password: VotreMotDePasseSecurise123!

# 4. ⚠️ IMPORTANT: Changez le mot de passe à la première connexion!
```

---

## 🚀 DÉMARRAGE (4 ÉTAPES)

### Étape 1: Vérifier la configuration de la base de données

```bash
# Assurez-vous que votre .env est correctement configuré
cat .env | grep DB_

# Résultat attendu:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=reservation_vehicule
# DB_USERNAME=root
# DB_PASSWORD=...
```

### Étape 2: Exécuter les migrations (si nécessaire)

```bash
# Initialiser la base de données
php artisan migrate

# Cela crée les tables: users, roles, permissions, etc.
```

### Étape 3: Exécuter le SuperAdminSeeder

**Option A: Avec mot de passe par défaut (DEVELOPMENT SEULEMENT)**
```bash
php artisan db:seed --class=SuperAdminSeeder
```

**Option B: Avec mot de passe personnalisé (RECOMMANDÉ)**
```bash
# Éditer .env d'abord
SUPERADMIN_PASSWORD="MonMotDePasseSecurise123!"

# Puis exécuter
php artisan db:seed --class=SuperAdminSeeder
```

**Option C: Mot de passe via ligne de commande**
```bash
# Linux/Mac
SUPERADMIN_PASSWORD="MonMotDePasseSecurise123!" php artisan db:seed --class=SuperAdminSeeder

# Windows PowerShell
$env:SUPERADMIN_PASSWORD="MonMotDePasseSecurise123!"; php artisan db:seed --class=SuperAdminSeeder
```

### Étape 4: Vérifier la création

```bash
# Vérifier en base de données
php artisan tinker

# Dans Tinker:
>>> \App\Models\User::where('email', 'superadmin@sdcc.ma')->first()

# Résultat attendu: User object avec email='superadmin@sdcc.ma'
>>> \App\Models\User::where('email', 'superadmin@sdcc.ma')->first()->roles

# Résultat attendu: Collection avec rôle 'super_admin'
```

---

## 🔒 CONFIGURATION DE SÉCURITÉ (PRODUCTION)

### 1. Définir les variables d'environnement

**Fichier: `.env.production`**
```env
# ⚠️ JAMAIS en Git!
SUPERADMIN_PASSWORD="UltraSecurePassword@2026#!"
SUPERADMIN_EMAIL="superadmin@sdcc.ma"
SUPERADMIN_NAME="Super Admin"
```

**Dans votre système CI/CD (GitHub Actions, GitLab CI, etc.):**
```yaml
# .github/workflows/deploy.yml
env:
  SUPERADMIN_PASSWORD: ${{ secrets.SUPERADMIN_PASSWORD }}
```

### 2. Fichier .gitignore

```
# S'assurer que .env n'est JAMAIS commité
.env
.env.production
.env.*.local
.env.production.local
```

**Vérifier:**
```bash
# S'assurer que .env est dans .gitignore
grep "^\.env" .gitignore
```

### 3. Permissions fichiers (Linux/Mac)

```bash
# Réduire les permissions du .env
chmod 600 .env
chmod 600 .env.production

# Vérifier
ls -la .env
# Résultat: -rw------- 1 user group
```

### 4. Chiffrer les secrets en production

**Avec Laravel Secrets (Laravel 11+):**
```bash
# Générer la clé de chiffrement
php artisan secrets:generate

# Définir le secret
php artisan secrets:set SUPERADMIN_PASSWORD

# Utiliser dans le code
env('SUPERADMIN_PASSWORD')  // Automatiquement déchiffré
```

**Sans Laravel Secrets (utiliser AWS Secrets Manager, HashiCorp Vault, etc.):**
```php
// Dans le seeder:
$password = \AWS\SecretsManager::getSecret('superadmin-password');
```

---

## 📝 MEILLEURES PRATIQUES DE SÉCURITÉ

### ✅ À FAIRE

| ✅ | Pratique | Exemple |
|---|----------|---------|
| ✅ | Utiliser un mot de passe fort | `MyP@ssw0rd2026!Secure#123` |
| ✅ | Stocker en variable d'env | `SUPERADMIN_PASSWORD=***` |
| ✅ | Hashé automatiquement | `Hash::make($password)` |
| ✅ | Forcer changement initial | Force reset on first login |
| ✅ | Utiliser 2FA | Google Authenticator |
| ✅ | Rotation trimestrielle | Changer tous les 3 mois |
| ✅ | Audit logs | Tracker tous les accès |
| ✅ | Alerter sur utilisation | Email/SMS sur login |

### ❌ À NE PAS FAIRE

| ❌ | Erreur | Risque |
|---|--------|--------|
| ❌ | `ChangeMe123!` en production | Accès par force-brute |
| ❌ | Stocker mot de passe en clair | Breach = totalement exposé |
| ❌ | Même mot de passe partout | Un breach = tout compromis |
| ❌ | Partager le mot de passe | Accountability impossible |
| ❌ | Ne jamais le changer | Vieux mot de passe compromis |
| ❌ | Stocker en code source | Git history = exposé forever |
| ❌ | Commiter .env | Leak des secrets |

---

## 🛡️ SÉCURISATION AVANT LA PRODUCTION

### Checklist de Sécurité

```bash
# 1. ✅ Vérifier que .env est JAMAIS commité
git ls-files | grep .env
# Résultat: Devrait être VIDE

# 2. ✅ Vérifier que le mot de passe est fort
# Critères:
#   - Minimum 12 caractères
#   - Mélange de majuscules, minuscules, chiffres, caractères spéciaux
#   - Pas de dictionnaire ou pattern simple

# 3. ✅ Vérifier que les permissions .env sont restrictives
stat .env | grep Access
# Résultat: (0600/-rw-------)

# 4. ✅ Vérifier que le hash est correctement appliqué
php artisan tinker
>>> \App\Models\User::find(1)->password
# Résultat: $2y$12$... (JAMAIS 'ChangeMe123!')

# 5. ✅ Vérifier que le rôle est assigné
php artisan tinker
>>> \App\Models\User::find(1)->roles->pluck('name')
# Résultat: ["super_admin"]

# 6. ✅ Vérifier que toutes les permissions sont assignées
php artisan tinker
>>> \App\Models\User::find(1)->permissions->count()
# Résultat: 8+ (ou votre nombre de permissions)
```

---

## 🔄 ROTATION DU MOT DE PASSE (Trimestriel)

### Option 1: Via l'Interface Admin

```
Dashboard → Admin → Users → superadmin@sdcc.ma → Change Password
```

### Option 2: Via Tinker

```bash
php artisan tinker

>>> $user = \App\Models\User::where('email', 'superadmin@sdcc.ma')->first()
>>> $user->update(['password' => \Illuminate\Support\Facades\Hash::make('NewPassword@2026!')])
>>> exit
```

### Option 3: Via Command Artisan (Recommandé)

```bash
# Créer une commande personnalisée
php artisan make:command UpdateSuperAdminPassword

# Dans la commande:
$user = User::where('email', 'superadmin@sdcc.ma')->first();
$newPassword = $this->secret('Enter new password');
$user->update(['password' => Hash::make($newPassword)]);
```

---

## 🔐 AUTHENTIFICATION MULTI-FACTEURS (2FA)

### Ajouter Google Authenticator

**1. Installer le package**
```bash
composer require pragmarx/google2fa-laravel
```

**2. Forcer 2FA pour Super Admin**
```php
// Dans SuperAdminSeeder.php après assignRole:
$superAdmin->update(['two_factor_secret' => encrypt(Google2FA::generateSecretKey())]);
```

**3. Ajouter middleware**
```php
// routes/web.php
Route::group(['middleware' => ['auth', '2fa']], function () {
    Route::resource('admin', AdminController::class);
});
```

---

## 🚨 INCIDENT: MOT DE PASSE COMPROMIS

**Si le super_admin est compromis:**

### Étape 1: Action Immédiate (5 min)
```bash
# 1. Désactiver le compte
php artisan tinker
>>> \App\Models\User::where('email', 'superadmin@sdcc.ma')->update(['is_active' => false])

# 2. Générer un nouveau mot de passe
>>> $newPassword = \Str::random(32)
>>> \App\Models\User::where('email', 'superadmin@sdcc.ma')->update(['password' => Hash::make($newPassword)])

# 3. Notifier les admins
>>> \Notification::send($admins, new SecurityAlert('Super Admin compromised'))
```

### Étape 2: Investigation (30 min)
```bash
# 1. Vérifier les logs d'accès
tail -f storage/logs/laravel.log | grep 'superadmin@sdcc.ma'

# 2. Vérifier les activités suspectes
SELECT * FROM activity_log WHERE causer_id = 1 ORDER BY created_at DESC LIMIT 20;

# 3. Vérifier les modifications de rôles/permissions
SELECT * FROM model_has_roles WHERE model_id = 1;
SELECT * FROM model_has_permissions WHERE model_id = 1;
```

### Étape 3: Récupération (1 heure)
```bash
# 1. Réinitialiser le mot de passe
SUPERADMIN_PASSWORD="NewSecurePassword123!" php artisan db:seed --class=SuperAdminSeeder

# 2. Activer le compte
php artisan tinker
>>> \App\Models\User::where('email', 'superadmin@sdcc.ma')->update(['is_active' => true])

# 3. Auditer tous les changements
SELECT * FROM audit_logs WHERE created_at > NOW() - INTERVAL 1 DAY;

# 4. Forcer réauthentification de tous les admins
```

---

## 📊 AUDIT DES ACCÈS

### Logger tous les accès du Super Admin

**Middleware personnalisé: `SuperAdminActivity.php`**
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            \Log::info('Super Admin Access', [
                'email' => auth()->user()->email,
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
            
            // Envoyer alerte si action sensible
            if ($this->isSensitiveAction($request)) {
                \Notification::send($admins, new SuspiciousActivity($request));
            }
        }
        
        return $next($request);
    }
}
```

**Enregistrer le middleware:**
```php
// app/Http/Kernel.php
protected $middleware = [
    // ...
    \App\Http\Middleware\SuperAdminActivity::class,
];
```

---

## 📧 NOTIFICATIONS D'ALERTE

### Notifier les administrateurs sur accès du Super Admin

```php
// Dans SuperAdminSeeder.php:
Mail::to(config('mail.admin_email'))->send(
    new SuperAdminCreated($superAdmin, $password)
);
```

**Notification Email:**
```php
// app/Mail/SuperAdminCreated.php
class SuperAdminCreated extends Mailable
{
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Compte Super Admin créé/mis à jour',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.super-admin-created',
            with: [
                'email' => $this->user->email,
                'name' => $this->user->name,
                'createdAt' => $this->user->created_at,
                'temporaryPassword' => $this->password,
            ],
        );
    }
}
```

---

## 🧪 TESTER LE SEEDER

### Test unitaire

```php
// tests/Feature/SuperAdminSeederTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;

class SuperAdminSeederTest extends TestCase
{
    /**
     * Test que le SuperAdminSeeder crée un compte avec succès
     */
    public function test_super_admin_seeder_creates_user(): void
    {
        // Exécuter le seeder
        $this->seed(SuperAdminSeeder::class);

        // Vérifier que l'utilisateur existe
        $this->assertDatabaseHas('users', [
            'email' => 'superadmin@sdcc.ma',
        ]);

        // Vérifier que l'utilisateur a le rôle super_admin
        $user = User::where('email', 'superadmin@sdcc.ma')->first();
        $this->assertTrue($user->hasRole('super_admin'));

        // Vérifier que le mot de passe est hashé (NOT plain text)
        $this->assertNotEquals('ChangeMe123!', $user->password);
    }

    /**
     * Test que le seeder est idempotent
     */
    public function test_super_admin_seeder_is_idempotent(): void
    {
        $this->seed(SuperAdminSeeder::class);
        $firstUser = User::where('email', 'superadmin@sdcc.ma')->first();

        $this->seed(SuperAdminSeeder::class);
        $secondUser = User::where('email', 'superadmin@sdcc.ma')->first();

        // Même utilisateur après rejouer le seeder
        $this->assertEquals($firstUser->id, $secondUser->id);
    }

    /**
     * Test que le mot de passe peut être customisé
     */
    public function test_super_admin_password_can_be_customized(): void
    {
        $this->app['config']['env'] = 'testing';
        putenv('SUPERADMIN_PASSWORD=CustomPassword123!');

        $this->seed(SuperAdminSeeder::class);
        
        $user = User::where('email', 'superadmin@sdcc.ma')->first();
        
        // Vérifier que le mot de passe customisé est utilisé
        $this->assertTrue(\Hash::check('CustomPassword123!', $user->password));
    }
}
```

**Exécuter les tests:**
```bash
php artisan test tests/Feature/SuperAdminSeederTest.php
```

---

## 🆘 TROUBLESHOOTING

### Problème: "SUPERADMIN_PASSWORD n'est pas défini"

**Cause:** Variable d'environnement manquante

**Solution:**
```bash
# Éditer .env
echo 'SUPERADMIN_PASSWORD=SecurePassword123!' >> .env

# Ou éditer manuellement .env
SUPERADMIN_PASSWORD=SecurePassword123!
```

### Problème: "Role super_admin does not exist"

**Cause:** Le rôle n'a pas été créé

**Solution:**
```bash
# Exécuter d'abord le DatabaseSeeder
php artisan db:seed

# Puis le SuperAdminSeeder
php artisan db:seed --class=SuperAdminSeeder
```

### Problème: "Illuminate\Database\QueryException: SQLSTATE[HY000]"

**Cause:** La base de données n'existe pas ou n'est pas connectée

**Solution:**
```bash
# Vérifier la configuration .env
cat .env | grep DB_

# Créer la base de données si elle n'existe pas
mysql -u root -p
> CREATE DATABASE reservation_vehicule CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Exécuter les migrations
php artisan migrate
```

### Problème: "User UPDATED instead of CREATED"

**Cause:** Normal! updateOrCreate met à jour si l'email existe

**Solution:** C'est voulu pour être idempotent:
```php
# Le seeder peut être rejoué plusieurs fois sans problème
php artisan db:seed --class=SuperAdminSeeder
php artisan db:seed --class=SuperAdminSeeder  # OK: met à jour seulement
```

---

## 🚀 AUTOMATISER EN PRODUCTION

### GitHub Actions

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches:
      - production

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      
      - name: Install dependencies
        run: composer install --no-interaction --prefer-dist
      
      - name: Run migrations
        run: php artisan migrate --force
        env:
          DB_CONNECTION: mysql
          DB_HOST: ${{ secrets.DB_HOST }}
          DB_DATABASE: ${{ secrets.DB_DATABASE }}
          DB_USERNAME: ${{ secrets.DB_USERNAME }}
          DB_PASSWORD: ${{ secrets.DB_PASSWORD }}
      
      - name: Seed Super Admin
        run: php artisan db:seed --class=SuperAdminSeeder --force
        env:
          SUPERADMIN_PASSWORD: ${{ secrets.SUPERADMIN_PASSWORD }}
          SUPERADMIN_EMAIL: ${{ secrets.SUPERADMIN_EMAIL }}
```

---

## ✅ CHECKLIST FINALE

Avant d'aller en production:

- [ ] SuperAdminSeeder créé et testé
- [ ] .env contient SUPERADMIN_PASSWORD sécurisé
- [ ] .env est dans .gitignore
- [ ] Permissions fichiers réduites (600)
- [ ] Tests passent: `php artisan test`
- [ ] Super Admin peut se connecter
- [ ] Rôle et permissions assignés
- [ ] 2FA configuré (optionnel mais recommandé)
- [ ] Audit logs activés
- [ ] Notifications d'alerte configurées
- [ ] Plan de rotation de mot de passe défini
- [ ] Plan d'incident documenté

---

## 📞 SUPPORT

### Besoin d'aide?

1. Vérifier les logs: `tail -f storage/logs/laravel.log`
2. Utiliser Tinker: `php artisan tinker`
3. Vérifier la documentation: `DOCUMENTATION_INDEX.md`
4. Contacter l'équipe DevOps

---

**🎉 Vous êtes maintenant prêt à créer un compte Super Admin sécurisé!**
