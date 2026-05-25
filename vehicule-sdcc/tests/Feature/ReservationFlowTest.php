<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Car;
use App\Models\Demande;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\DatabaseSeeder']);
    }

    /**
     * Test: Admin can create a reservation for an employee
     */
    public function test_admin_can_create_reservation(): void
    {
        $admin = User::where('email', 'superadmin@sdcc.ma')->first();
        $employee = User::where('email', '!=', 'superadmin@sdcc.ma')->first();
        $car = Car::first();

        $this->actingAs($admin)
            ->post(route('demandes.store'), [
                'user_id' => $employee->id,
                'car_id' => $car->id,
                'destination' => 'Direction',
                'start_date' => now()->addDay()->toDateString(),
                'start_time' => '09:00',
                'end_date' => now()->addDay()->toDateString(),
                'end_time' => '17:00',
                'reason' => 'Business meeting',
                'status' => 'pending',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('demandes', [
            'user_id' => $employee->id,
            'car_id' => $car->id,
        ]);
    }

    /**
     * Test: Employee cannot create reservation for another employee
     */
    public function test_employee_cannot_create_reservation_for_others(): void
    {
        $employee = User::where('email', '!=', 'superadmin@sdcc.ma')->first();
        $other = User::where('email', '!=', 'superadmin@sdcc.ma')
            ->where('id', '!=', $employee->id)
            ->first();

        if (!$other) {
            $this->markTestSkipped('Not enough users in database');
        }

        $car = Car::first();

        $this->actingAs($employee)
            ->post(route('demandes.store'), [
                'user_id' => $other->id,
                'car_id' => $car->id,
                'destination' => 'Direction',
                'start_date' => now()->addDay()->toDateString(),
                'start_time' => '09:00',
                'end_date' => now()->addDay()->toDateString(),
                'end_time' => '17:00',
                'reason' => 'Business meeting',
            ])
            ->assertStatus(403);
    }

    /**
     * Test: User must be logged in to create reservation
     */
    public function test_unauthenticated_user_cannot_create_reservation(): void
    {
        $employee = User::where('email', '!=', 'superadmin@sdcc.ma')->first();
        $car = Car::first();

        $this->post(route('demandes.store'), [
            'user_id' => $employee->id,
            'car_id' => $car->id,
            'destination' => 'Direction',
            'start_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_date' => now()->addDay()->toDateString(),
            'end_time' => '17:00',
            'reason' => 'Business meeting',
        ])->assertRedirect('/login');
    }

    /**
     * Test: Login with valid credentials
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::where('email', 'superadmin@sdcc.ma')->first();
        $this->assertNotNull($user, 'Superadmin user not found');
        // Ensure known password for test reliability
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make('password')]);

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.data-management'));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Login fails with invalid credentials
     */
    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->post(route('login.post'), [
            'email' => 'superadmin@sdcc.ma',
            'password' => 'wrongpassword',
        ])->assertRedirect()
            ->withErrors(['email']);
    }

    /**
     * Test: Deactivated user cannot login
     */
    public function test_deactivated_user_cannot_login(): void
    {
        $user = User::where('email', '!=', 'superadmin@sdcc.ma')->first();
        $user->update(['is_active' => false]);

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect()
            ->withErrors(['email' => 'Votre compte a été désactivé. Contactez votre administrateur.']);
    }

    /**
     * Test: Rate limiting on login endpoint
     */
    public function test_login_is_rate_limited(): void
    {
        // Make 6 failed login attempts (limit is 5 per minute)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('login.post'), [
                'email' => 'superadmin@sdcc.ma',
                'password' => 'wrongpassword',
            ]);

            if ($i < 5) {
                $this->assertEquals(302, $response->getStatusCode());
            } else {
                // 6th attempt should be rate limited
                $this->assertEquals(429, $response->getStatusCode());
            }
        }
    }
}
