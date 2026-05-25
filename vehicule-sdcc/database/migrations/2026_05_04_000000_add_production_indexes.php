<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add critical indexes to improve query performance.
     * These indexes are essential for production deployments.
     */
    public function up(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Index pour les requêtes filtrées par utilisateur et statut
            $table->index(['user_id', 'status'], 'idx_demandes_user_status');
            
            // Index pour les requêtes filtrées par voiture
            $table->index(['car_id'], 'idx_demandes_car_id');
            
            // Index pour les requêtes par date (calendrier, planification)
            $table->index(['start_date', 'end_date'], 'idx_demandes_dates');
            
            // Index pour le statut seul (filtrage global)
            $table->index(['status'], 'idx_demandes_status');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index pour les requêtes par zone de planification
            $table->index(['planning_zone_id'], 'idx_users_zone');
            
            // Index pour les utilisateurs actifs
            $table->index(['is_active'], 'idx_users_active');
        });

        Schema::table('cars', function (Blueprint $table) {
            // Index pour les requêtes filtrées par statut
            $table->index(['status'], 'idx_cars_status');
            
            // Index pour les requêtes filtrées par type de disponibilité
            $table->index(['availability_type'], 'idx_cars_availability_type');
        });

        // Index pour la table notifications (si elle existe)
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_id', 'notifiable_type'], 'idx_notifications_notifiable');
                $table->index(['read_at'], 'idx_notifications_read_at');
            });
        }
    }

    /**
     * Revert the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropIndex('idx_demandes_user_status');
            $table->dropIndex('idx_demandes_car_id');
            $table->dropIndex('idx_demandes_dates');
            $table->dropIndex('idx_demandes_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_zone');
            $table->dropIndex('idx_users_active');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->dropIndex('idx_cars_status');
            $table->dropIndex('idx_cars_availability_type');
        });

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('idx_notifications_notifiable');
                $table->dropIndex('idx_notifications_read_at');
            });
        }
    }
};
