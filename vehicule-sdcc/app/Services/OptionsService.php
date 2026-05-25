<?php

namespace App\Services;

class OptionsService
{
    public function vehicleStatuses(): array
    {
        return [
            'disponible' => 'Disponible',
            'maintenance' => 'Maintenance',
        ];
    }

    public function vehicleAvailabilityTypes(): array
    {
        return [
            'both' => 'Disponible toute la semaine',
            'weekend' => 'Week-end uniquement (Sam–Dim)',
            'unavailable' => 'Indisponible (ne peut pas être réservé)',
        ];
    }

    public function userAssignableRoles(bool $isSuperAdmin): array
    {
        $roles = [
            'employee' => 'Employé',
        ];

        if ($isSuperAdmin) {
            $roles = [
                'super_admin' => 'Super Admin',
                'admin' => 'Admin',
            ] + $roles;
        }

        return $roles;
    }

    public function userRoleFilters(): array
    {
        return [
            'all' => 'Tous les rôles',
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'employee' => 'Employé',
        ];
    }

    public function userServices(): array
    {
        return [
            // General Management Division
            'Secrétariat de Direction Générale' => 'Secrétariat de Direction Générale',
            'Audit Interne & Contrôle de Gestion' => 'Audit Interne & Contrôle de Gestion',
            'Affaires Juridiques & Contentieux' => 'Affaires Juridiques & Contentieux',
            
            // Commercial & Marketing Division
            'Support Commercial & Relations SNTL' => 'Support Commercial & Relations SNTL',
            'Gestion Réseau Propre et Partenaires' => 'Gestion Réseau Propre et Partenaires',
            'Gestion Grands Comptes Produits Blancs et Lubrifiants' => 'Gestion Grands Comptes Produits Blancs et Lubrifiants',
            'Business Unit Rihab Restauration & Shops' => 'Business Unit Rihab Restauration & Shops',
            
            // Supply Chain Division
            'Production Blending' => 'Production Blending',
            'Expéditions & Logistique' => 'Expéditions & Logistique',
            'Laboratoire' => 'Laboratoire',
            
            // Technical Division
            'Maintenance & HSE' => 'Maintenance & HSE',
            'Projets Stations-service' => 'Projets Stations-service',
            
            // Human Resources Division
            'Administration QHSE & Amélioration Continue' => 'Administration QHSE & Amélioration Continue',
            'Moyens Généraux' => 'Moyens Généraux',
            
            // Finance Division
            'Achats' => 'Achats',
            'Comptabilité' => 'Comptabilité',
            'Crédit Management & Administration des Ventes' => 'Crédit Management & Administration des Ventes',
            'Trésorerie' => 'Trésorerie',
            'Back Office Ventes monétiques' => 'Back Office Ventes monétiques',
            
            // Information Systems Division
            "Système d'Information" => "Système d'Information",
        ];
    }



    public function reservationStatuses(): array
    {
        return [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'cancelled' => 'Annulé',
        ];
    }

    public function reservationBlockingStatuses(): array
    {
        // Only active reservations should block new bookings.
        return ['pending', 'approved'];
    }

    public function reservationMessages(): array
    {
        return [
            'day_reserved' => 'This day is already reserved',
        ];
    }

    public function calendarOptions(): array
    {
        return [
            'all_vehicles_label' => 'Tous les véhicules',
            'reservation_messages' => $this->reservationMessages(),
            'legend' => [
                'approved' => 'Approuvé',
                'pending' => 'En attente',
                'cancelled' => 'Annulée',
                'available' => 'Libre',
                'today' => "Aujourd'hui",
            ],
            'day_status_labels' => [
                'available' => 'Disponible',
                'approved' => 'Approuve',
                'pending' => 'En attente',
                'cancelled' => 'Annulee',
            ],
            'tooltip_title' => 'Détails de la réservation',
            'tooltip_fields' => [
                'employee' => 'Employé',
                'vehicle' => 'Véhicule',
                'period' => 'Période',
                'date' => 'Jour affiché',
                'destination' => 'Destination',
                'start' => 'Heure départ',
                'end' => 'Heure retour',
            ],
            'status_labels_fr' => [
                'approved' => 'Approuvée',
                'pending' => 'En attente',
                'cancelled' => 'Annulée',
                'rejected' => 'Rejetée',
            ],
            'status_labels_en' => [
                'approved' => 'Approved',
                'pending' => 'Pending',
                'cancelled' => 'Cancelled',
                'rejected' => 'Cancelled',
            ],
        ];
    }

    public function planificationOptions(): array
    {
        return [
            'title' => 'Planification',
            'subtitle' => 'Interface de structure de planification (sans données de réservation)',
            'stats_labels' => [
                'zones' => 'Zones de planification',
                'resources' => 'Ressources planifiées',
                'active_windows' => 'Fenêtres actives',
                'planning_version' => 'Version de planning',
            ],
            'section_title' => 'Structure de planning',
            'table_headers' => [
                'block' => 'Bloc',
                'description' => 'Description',
                'status' => 'Statut',
            ],
            'rows' => [
                ['block' => 'Zone Nord', 'description' => 'Planification des activités de la zone nord', 'status' => 'Actif'],
                ['block' => 'Zone Sud', 'description' => 'Planification des activités de la zone sud', 'status' => 'Actif'],
                ['block' => 'Maintenance', 'description' => 'Planification des fenêtres techniques', 'status' => 'En cours'],
            ],
            'excel_button' => 'Excel Auto (Live)',
        ];
    }

    public function userRoleBadgeClasses(): array
    {
        return [
            'super_admin' => 'role-super-admin',
            'admin'       => 'role-admin',
            'employee'    => 'role-employee',
        ];
    }

    public function usersPageOptions(): array
    {
        return [
            'title'    => 'Gestion des Utilisateurs',
            'subtitle' => 'Gérez les comptes utilisateurs et leurs rôles',
            'stats'    => [
                'total'       => 'Total Utilisateurs',
                'admins'      => 'Admins',
                'employees'   => 'Employés',
                'super_admins'=> 'Super Admins',
            ],
            'table' => [
                'columns' => [
                    'name'    => 'Nom',
                    'email'   => 'Email',
                    'role'    => 'Rôle',
                    'service' => 'Service',
                    'actions' => 'Actions',
                ],
                'actions' => [
                    'update_role'              => 'Changer Rôle',
                    'reset_password'           => 'Réinitialiser',
                    'new_password_placeholder' => 'Nouveau mot de passe',
                    'confirmation_placeholder' => 'Confirmer mot de passe',
                ],
            ],
            'filters' => [
                'search_placeholder' => 'Rechercher un utilisateur...',
                'role_all'           => 'Tous les rôles',
            ],
            'create_form' => [
                'title'            => 'Créer un compte',
                'name_placeholder' => 'Nom complet',
                'submit_label'     => 'Créer',
            ],
        ];
    }
}
