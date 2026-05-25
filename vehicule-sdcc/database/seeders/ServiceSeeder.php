<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing services
        Service::truncate();

        // Define the organizational structure
        $services = [
            // Direction Générale
            [
                'name' => 'general_management_secretariat',
                'display_name' => 'General Management Secretariat',
                'department' => 'General Management',
                'sort_order' => 1,
            ],
            [
                'name' => 'internal_audit',
                'display_name' => 'Internal Audit & Management Control',
                'department' => 'General Management',
                'sort_order' => 2,
            ],
            [
                'name' => 'legal_affairs',
                'display_name' => 'Legal Affairs & Litigation',
                'department' => 'General Management',
                'sort_order' => 3,
            ],

            // Commercial & Marketing
            [
                'name' => 'commercial_support',
                'display_name' => 'Commercial Support & SNTL Relations',
                'department' => 'Commercial & Marketing',
                'sort_order' => 1,
            ],
            [
                'name' => 'network_management',
                'display_name' => 'Network Management (Owned & Partners)',
                'department' => 'Commercial & Marketing',
                'sort_order' => 2,
            ],
            [
                'name' => 'key_accounts',
                'display_name' => 'Key Accounts Management (White Products & Lubricants)',
                'department' => 'Commercial & Marketing',
                'sort_order' => 3,
            ],
            [
                'name' => 'business_unit_rihab',
                'display_name' => 'Business Unit Rihab Restoration & Shops',
                'department' => 'Commercial & Marketing',
                'sort_order' => 4,
            ],

            // Supply Chain
            [
                'name' => 'production_blending',
                'display_name' => 'Production Blending',
                'department' => 'Supply Chain',
                'sort_order' => 1,
            ],
            [
                'name' => 'shipping_logistics',
                'display_name' => 'Shipping & Logistics',
                'department' => 'Supply Chain',
                'sort_order' => 2,
            ],
            [
                'name' => 'laboratory',
                'display_name' => 'Laboratory',
                'department' => 'Supply Chain',
                'sort_order' => 3,
            ],

            // Technical, Maintenance & HSE
            [
                'name' => 'maintenance_hse',
                'display_name' => 'Maintenance & HSE',
                'department' => 'Technical, Maintenance & HSE',
                'sort_order' => 1,
            ],
            [
                'name' => 'service_station_projects',
                'display_name' => 'Service Station Projects',
                'department' => 'Technical, Maintenance & HSE',
                'sort_order' => 2,
            ],

            // Human Resources
            [
                'name' => 'qhse_administration',
                'display_name' => 'QHSE Administration & Continuous Improvement',
                'department' => 'Human Resources',
                'sort_order' => 1,
            ],
            [
                'name' => 'general_services',
                'display_name' => 'General Services',
                'department' => 'Human Resources',
                'sort_order' => 2,
            ],
            [
                'name' => 'procurement',
                'display_name' => 'Procurement',
                'department' => 'Human Resources',
                'sort_order' => 3,
            ],

            // Finance
            [
                'name' => 'accounting',
                'display_name' => 'Accounting',
                'department' => 'Finance',
                'sort_order' => 1,
            ],
            [
                'name' => 'credit_management',
                'display_name' => 'Credit Management & Sales Administration',
                'department' => 'Finance',
                'sort_order' => 2,
            ],
            [
                'name' => 'treasury',
                'display_name' => 'Treasury',
                'department' => 'Finance',
                'sort_order' => 3,
            ],
            [
                'name' => 'back_office_payments',
                'display_name' => 'Back Office (Electronic Payments)',
                'department' => 'Finance',
                'sort_order' => 4,
            ],

            // Information Systems
            [
                'name' => 'information_systems',
                'display_name' => 'Information Systems',
                'department' => 'Information Systems',
                'sort_order' => 1,
            ],
        ];

        foreach ($services as $service) {
            Service::create(array_merge($service, ['is_active' => true]));
        }
    }
}
