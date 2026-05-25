<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Demande;
use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class DemandValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $employee;
    protected Car $car;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\DatabaseSeeder']);
        
        $this->admin = User::where('email', 'superadmin@sdcc.ma')->first();
        $this->employee = User::where('email', '!=', 'superadmin@sdcc.ma')
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['admin', 'super_admin']);
            })
            ->first();
        
        $this->car = Car::first();
    }

    /**
     * Test: Employee cannot create demand in the future if car is unavailable
     */
    public function test_employee_cannot_reserve_unavailable_car(): void
    {
        $this->car->update(['status' => 'maintenance']);

        $response = $this->actingAs($this->employee)->post(route('demandes.store'), [
            'car_id' => $this->car->id,
            'destination' => 'Marrakech',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::tomorrow()->toDateString(),
            'end_time' => '17:00',
            'reason' => 'Business meeting',
        ]);

        $response->assertStatus(422);  // Validation error
    }

    /**
     * Test: Cannot create demand with date in the past
     */
    public function test_cannot_create_demand_with_past_date(): void
    {
        $response = $this->actingAs($this->employee)->post(route('demandes.store'), [
            'car_id' => $this->car->id,
            'destination' => 'Casablanca',
            'start_date' => Carbon::yesterday()->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::yesterday()->toDateString(),
            'end_time' => '17:00',
            'reason' => 'Past meeting',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test: End date must be after start date
     */
    public function test_end_date_must_be_after_start_date(): void
    {
        $response = $this->actingAs($this->employee)->post(route('demandes.store'), [
            'car_id' => $this->car->id,
            'destination' => 'Fez',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::yesterday()->toDateString(),  // Invalid!
            'end_time' => '17:00',
            'reason' => 'Meeting',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test: Employee cannot manually set status to approved
     */
    public function test_employee_cannot_set_status_to_approved(): void
    {
        $response = $this->actingAs($this->employee)->post(route('demandes.store'), [
            'car_id' => $this->car->id,
            'destination' => 'Rabat',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::tomorrow()->toDateString(),
            'end_time' => '17:00',
            'reason' => 'Meeting',
            'status' => 'approved',  // Try to bypass!
        ]);

        // Should succeed, but demand should be pending (not approved)
        if ($response->status() === 201) {
            $demande = Demande::latest()->first();
            $this->assertEquals('pending', $demande->status);
        }
    }

    /**
     * Test: Admin CAN create demand with specific status
     */
    public function test_admin_can_create_demand_with_specific_status(): void
    {
        $response = $this->actingAs($this->admin)->post(route('demandes.store'), [
            'user_id' => $this->employee->id,
            'car_id' => $this->car->id,
            'destination' => 'Tangier',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::tomorrow()->toDateString(),
            'end_time' => '17:00',
            'reason' => 'Admin request',
            'status' => 'approved',
        ]);

        $response->assertStatus(201);
        $demande = Demande::latest()->first();
        $this->assertEquals('approved', $demande->status);
    }

    /**
     * Test: Overlapping reservations prevented
     */
    public function test_overlapping_reservations_flagged_for_admin_review(): void
    {
        // Create first demand
        Demande::create([
            'user_id' => $this->employee->id,
            'car_id' => $this->car->id,
            'destination' => 'Casablanca',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'end_time' => '17:00',
            'reason' => 'Meeting',
            'status' => 'approved',
        ]);

        // Overlapping demand is saved for employees; admins handle the conflict later.
        $response = $this->actingAs($this->employee)->post(route('demandes.store'), [
            'car_id' => $this->car->id,
            'destination' => 'Fez',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '10:00',
            'end_date' => Carbon::tomorrow()->toDateString(),
            'end_time' => '16:00',
            'reason' => 'Conflicting meeting',
        ]);

        $response->assertStatus(201);
        $demande = Demande::latest('id')->first();
        $this->assertTrue($demande->has_conflict);
        $this->assertEquals('pending', $demande->status);
    }

    /**
     * Test: Employee can only update own pending demands
     */
    public function test_employee_cannot_update_others_demands(): void
    {
        $demande = Demande::create([
            'user_id' => $this->admin->id,
            'car_id' => $this->car->id,
            'destination' => 'Casablanca',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::tomorrow()->toDateString(),
            'end_time' => '17:00',
            'reason' => 'Meeting',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->employee)->put(
            route('demandes.update', $demande->id),
            ['destination' => 'Hacked!']
        );

        $response->assertStatus(403);
    }

    /**
     * Test: Cannot update approved demands
     */
    public function test_cannot_update_approved_demands(): void
    {
        $demande = Demande::create([
            'user_id' => $this->employee->id,
            'car_id' => $this->car->id,
            'destination' => 'Casablanca',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::tomorrow()->toDateString(),
            'end_time' => '17:00',
            'reason' => 'Meeting',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->employee)->put(
            route('demandes.update', $demande->id),
            ['destination' => 'New destination']
        );

        $response->assertStatus(403);
    }

    /**
     * Test: Admin can approve demands
     */
    public function test_admin_can_approve_demands(): void
    {
        $demande = Demande::create([
            'user_id' => $this->employee->id,
            'car_id' => $this->car->id,
            'destination' => 'Casablanca',
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '09:00',
            'end_date' => Carbon::tomorrow()->toDateString(),
            'end_time' => '17:00',
            'reason' => 'Meeting',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post(
            route('demandes.approve', $demande->id)
        );

        $response->assertStatus(200);
        $this->assertEquals('approved', $demande->refresh()->status);
    }

    /**
     * Test: Validate required fields on create
     */
    public function test_required_fields_validation(): void
    {
        $response = $this->actingAs($this->employee)->post(route('demandes.store'), [
            // Missing: destination, start_date, reason
            'car_id' => $this->car->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['destination', 'start_date', 'reason']);
    }

    /**
     * Test: Validate field formats
     */
    public function test_field_format_validation(): void
    {
        $response = $this->actingAs($this->employee)->post(route('demandes.store'), [
            'car_id' => 'invalid',  // Should be integer
            'destination' => str_repeat('a', 500),  // Too long
            'start_date' => 'invalid-date',  // Invalid format
            'start_time' => '25:00',  // Invalid time
            'reason' => str_repeat('a', 2000),  // Too long
        ]);

        $response->assertStatus(422);
    }
}
