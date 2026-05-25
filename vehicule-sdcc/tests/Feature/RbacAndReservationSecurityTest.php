<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RbacAndReservationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run database seeder to set up permissions, roles, and users
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\DatabaseSeeder']);
    }

    public function test_users_page_removed_and_inaccessible(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        // Users page has been removed from Data Management
        // Routes no longer exist and return 404
        $this->markTestSkipped('Users page removed from Data Management');
    }

    public function test_admin_cannot_create_admin_account(): void
    {
        // This test was for the removed Users management page
        // Users page has been removed from Data Management
        $this->markTestSkipped('Users page removed from Data Management');
    }

    public function test_employee_cannot_double_book_same_vehicle_and_day(): void
    {
        // Get an employee from the seeded data (should already be active and have permissions)
        $employee = User::where('email', '!=', 'superadmin@sdcc.ma')
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['admin', 'super_admin']);
            })
            ->first();
        
        $this->assertNotNull($employee, 'No employee user found in database');

        $car = Car::query()->create([
            'name' => 'Enterprise Car',
            'matricule' => 'ENT-001',
            'model' => 'Model X',
            'year' => 2024,
            'km' => 1000,
            'status' => 'disponible',
        ]);

        Demande::query()->create([
            'user_id' => $employee->id,
            'car_id' => $car->id,
            'destination' => 'Casablanca',
            'start_date' => '2026-04-20',
            'start_time' => '08:00',
            'end_date' => '2026-04-20',
            'end_time' => '17:00',
            'return_time' => '17:00',
            'kilometers' => 10,
            'reason' => 'Existing booking',
            'status' => Demande::STATUS_PENDING,
        ]);

        $response = $this->actingAs($employee)->post(route('mes-demandes.store'), [
            'destination' => 'Rabat',
            'start_date' => '2026-04-20',
            'start_time' => '09:00',
            'end_date' => '2026-04-20',
            'end_time' => '18:00',
            'return_time' => '18:00',
            'kilometers' => 40,
            'reason' => 'Second booking',
            'car_id' => $car->id,
        ]);

        if ($response->getStatusCode() === 422) {
            $response->assertJsonValidationErrors('start_date');
        } else {
            $response->assertSessionHasErrors('start_date');
        }
    }
}
