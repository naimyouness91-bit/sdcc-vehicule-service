<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Crée ou met à jour un compte Super Admin avec les permissions appropriées.
     * Utilise updateOrCreate pour garantir l'idempotence (safe à rejouer).
     *
     * Security: Le mot de passe est hashé avec Hash::make() et ne devrait jamais
     * être stocké en clair. En production, utilisez une variable d'environnement.
     *
     * @return void
     */
    public function run(): void
    {
        // Récupérer le mot de passe depuis .env ou utiliser le mot de passe par défaut
        $password = env('SUPER_ADMIN_PASSWORD', 'ChangeMe@123456');
        
        // Valider que le mot de passe n'est pas vide
        if (empty($password)) {
            $this->command->error('❌ ERREUR: SUPER_ADMIN_PASSWORD n\'est pas défini dans .env');
            $this->command->info('Ajoutez cette ligne à votre fichier .env:');
            $this->command->line('SUPER_ADMIN_PASSWORD=YourSecurePassword123!');
            return;
        }

        // Créer ou mettre à jour le compte Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'admin@your-domain.ma')],  // Condition de recherche
            [
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'email' => env('SUPER_ADMIN_EMAIL', 'admin@your-domain.ma'),
                'password' => Hash::make($password),  // Hasher le mot de passe
                'email_verified_at' => now(),  // Vérifier automatiquement l'email
                'is_active' => true,  // Activer immédiatement
            ]
        );

        // Assigner le rôle 'super_admin' (assure que le rôle existe d'abord)
        if (!$superAdmin->hasRole('super_admin')) {
            try {
                $superAdmin->assignRole('super_admin');
                $this->command->info('✅ Rôle super_admin assigné');
            } catch (\Exception $e) {
                $this->command->warn('⚠️ Le rôle super_admin n\'existe pas encore');
                $this->command->info('Conseil: Exécutez d\'abord le RoleSeeder');
            }
        }

        // Donner toutes les permissions au Super Admin (optionnel, mais recommandé)
        try {
            // Récupérer toutes les permissions
            $allPermissions = \Spatie\Permission\Models\Permission::all();
            
            if ($allPermissions->isNotEmpty()) {
                $superAdmin->syncPermissions($allPermissions);
                $this->command->info('✅ Toutes les permissions assignées');
            }
        } catch (\Exception $e) {
            $this->command->warn('⚠️ Impossible d\'assigner les permissions: ' . $e->getMessage());
        }

        // Afficher le résumé
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('✅ SUPER ADMIN CRÉÉ/MISE À JOUR AVEC SUCCÈS');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->line('Email:    ' . $superAdmin->email);
        $this->command->line('Nom:      ' . $superAdmin->name);
        $this->command->line('Statut:   ✅ Actif');
        $this->command->line('Rôle:     super_admin');
        $this->command->line('Permissions: Toutes');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: Changez le mot de passe dès la première connexion!');
        $this->command->warn('⚠️  NE JAMAIS utiliser ChangeMe@123456 en production!');
        $this->command->info('');
    }
}
