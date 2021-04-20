<?php

namespace Tests\Feature;
use HasFactory;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @return void
     */
    
    public function testRoot()
    {
        $response = $this->get('');

        $response->assertStatus(200);
    }
    /* public function testUnauthenticatedUserAccessingHomePage() {
        $response = $this->get('/home')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingHomePage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/home')->assertOk();
    }

    public function testUnauthenticatedUserAccessingBookingsPage() {
        $response = $this->get('/bookings')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingBookingsPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/bookings')->assertOk();
    }

    public function testUnauthenticatedUserAccessingRepeatBookingsPage() {
        $response = $this->get('/repeat-bookings')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingRepeatBookingsPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/repeat-bookings')->assertOk();
    }

    public function testUnauthenticatedUserAccessingPupilsPage() {
        $response = $this->get('/pupils')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingPupilsPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/pupils')->assertOk();
    }

    public function testUnauthenticatedUserAccessingPupilRegisterPage() {
        $response = $this->get('/pupil-register')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingPupilRegisterPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/pupil-register')->assertOk();
    }

    public function testUnauthenticatedUserAccessingInjuriesPage() {
        $response = $this->get('/injuries')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingInjuriesPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/injuries')->assertOk();
    }

    public function testUnauthenticatedUserAccessingSettingsPage() {
        $response = $this->get('/settings')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingSettingsPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/settings')->assertOk();
    }

    
    public function testUnauthenticatedUserAccessingRulesPage() {
        $response = $this->get('/rules')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingRulesPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/rules')->assertOk();
    }

    
    public function testUnauthenticatedUserAccessingActivityLogPage() {
        $response = $this->get('/activity-log')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingActivityLogPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/activity-log')->assertOk();
    }

    public function testUnauthenticatedUserAccessingControlPanelPage() {
        $response = $this->get('/rules')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingControlPanelPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/rules')->assertOk();
    }

    public function testUnauthenticatedUserAccessingStaffAvailabilityPage() {
        $response = $this->get('/staff-availability')->assertRedirect('/login');

    }

    public function testAuthenticatedUserAccessingStaffAvailabilityPage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/staff-availability')->assertOk();

    }

    public function testUnauthenticatedUserAccessingStaffSchedulePage() {
        $response = $this->get('/staff-schedule')->assertRedirect('/login');
    }

    public function testAuthenticatedUserAccessingStaffSchedulePage() {
        $this->actingAs(User::factory()->make());
        $response = $this->get('/staff-schedule')->assertOk();
    } */
}
