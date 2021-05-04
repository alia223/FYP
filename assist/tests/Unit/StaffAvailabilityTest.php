<?php

namespace Tests\Unit;
use Tests\Testcase;
use HasFactory;
use App\Models\User;
use App\Models\StaffAvailability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;

class StaffAvailabilityTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testAvailableFromLowerBoundary() {
        $this->actingAs(User::factory()->create());
        $this->post(route('staff-availability.store'), [
            'monday_available_from' => '15:30', 'monday_available_until' => '19:30',
            'tuesday_available_from' => '15:30', 'tuesday_available_until' => '19:30',
            'wednesday_available_from' => '15:30', 'wednesday_available_until' => '19:30',
            'thursday_available_from' => '15:30', 'thursday_available_until' => '19:30',
            'friday_available_from' => '15:30', 'friday_available_until' => '19:30',
            'max_hours' => 10
        ]);
        for($i = 1;$i <= 5;$i++) {
            $this->assertDatabaseHas('staff_availabilities', [
                'day' => $i, 
                'available_from' => '15:30',
                'available_until' => '19:30'
            ]);
        }
    }

    public function testAvailableFromUpperBoundary() {
        $this->actingAs(User::factory()->create());
        $this->post(route('staff-availability.store'), [
            'monday_available_from' => '19:00', 'monday_available_until' => '19:30',
            'tuesday_available_from' => '19:00', 'tuesday_available_until' => '19:30',
            'wednesday_available_from' => '19:00', 'wednesday_available_until' => '19:30',
            'thursday_available_from' => '19:00', 'thursday_available_until' => '19:30',
            'friday_available_from' => '19:00', 'friday_available_until' => '19:30',
            'max_hours' => 10
        ]);
        for($i = 1;$i <= 5;$i++) {
            $this->assertDatabaseHas('staff_availabilities', [
                'day' => $i, 
                'available_from' => '19:00',
                'available_until' => '19:30'
            ]);
        }
    }

    public function testAvailableFromMiddleBoundary() {
        $this->actingAs(User::factory()->create());
        $this->post(route('staff-availability.store'), [
            'monday_available_from' => '17:30', 'monday_available_until' => '19:30',
            'tuesday_available_from' => '17:30', 'tuesday_available_until' => '19:30',
            'wednesday_available_from' => '17:30', 'wednesday_available_until' => '19:30',
            'thursday_available_from' => '17:30', 'thursday_available_until' => '19:30',
            'friday_available_from' => '17:30', 'friday_available_until' => '19:30',
            'max_hours' => 10
        ]);
        for($i = 1;$i <= 5;$i++) {
            $this->assertDatabaseHas('staff_availabilities', [
                'day' => $i, 
                'available_from' => '17:30',
                'available_until' => '19:30'
            ]);
        }   
    }

    public function testInvalidAvailableFrom() {
        $this->actingAs(User::factory()->create());
        $this->post(route('staff-availability.store'), [
            'monday_available_from' => '14:30', 'monday_available_until' => '19:30',
            'max_hours' => 10
        ])->assertSessionHasErrors();
    }

    public function testAvailableUntilLowerBoundary() {
        $this->actingAs(User::factory()->create());
        $this->post(route('staff-availability.store'), [
            'monday_available_from' => '15:30', 'monday_available_until' => '16:00',
            'tuesday_available_from' => '15:30', 'tuesday_available_until' => '16:00',
            'wednesday_available_from' => '15:30', 'wednesday_available_until' => '16:00',
            'thursday_available_from' => '15:30', 'thursday_available_until' => '16:00',
            'friday_available_from' => '15:30', 'friday_available_until' => '16:00',
            'max_hours' => 10
        ]);
        for($i = 1;$i <= 5;$i++) {
            $this->assertDatabaseHas('staff_availabilities', [
                'day' => $i, 
                'available_from' => '15:30',
                'available_until' => '16:00'
            ]);
        }
    }

    public function testAvailableUntilMiddleBoundary() {
        $this->actingAs(User::factory()->create());
        $this->post(route('staff-availability.store'), [
            'monday_available_from' => '15:30', 'monday_available_until' => '17:30',
            'tuesday_available_from' => '15:30', 'tuesday_available_until' => '17:30',
            'wednesday_available_from' => '15:30', 'wednesday_available_until' => '17:30',
            'thursday_available_from' => '15:30', 'thursday_available_until' => '17:30',
            'friday_available_from' => '15:30', 'friday_available_until' => '17:30',
            'max_hours' => 10
        ]);
        for($i = 1;$i <= 5;$i++) {
            $this->assertDatabaseHas('staff_availabilities', [
                'day' => $i, 
                'available_from' => '15:30',
                'available_until' => '17:30'
            ]);
        }   
    }

    public function testInvalidAvailableUntil() {
        $this->actingAs(User::factory()->create());
        $this->post(route('staff-availability.store'), [
            'monday_available_from' => '14:30', 'monday_available_until' => '19:30',
            'max_hours' => 10
        ])->assertSessionHasErrors();
    }
}
