<?php

namespace Tests\Unit;
use Tests\Testcase;
use HasFactory;
use App\Models\User;
use App\Models\Pupil;
use App\Models\Booking;
use App\Models\BookedPupil;
use App\Models\StaffAvailability;
use App\Models\StaffSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;

class StaffScheduleTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testStaffScheduleForOnePupilBookedIn() {
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '240', 'date' => '2021-04-26', 'pupils' => [$pupil->id]]);
        for($i=0;$i<5;$i++) {
            $this->actingAs(User::factory()->create(['clubstaff' => 1]));
            $this->post(route('staff-availability.store'), [
                'monday_available_from' => '15:30', 'monday_available_until' => '19:30',
                'tuesday_available_from' => '15:30', 'tuesday_available_until' => '19:30',
                'wednesday_available_from' => '15:30', 'wednesday_available_until' => '19:30',
                'thursday_available_from' => '15:30', 'thursday_available_until' => '19:30',
                'friday_available_from' => '15:30', 'friday_available_until' => '19:30',
                'max_hours' => 100
            ]);
        }
        $this->actingAs(User::factory()->create(['admin' => 1]));
        $this->post(route('staff-schedule.store', ['day' => 1]));
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '19:30:00')->get()) >= 2);
    }

    public function testStaffScheduleForFifteenPupilsBookedIn() {
        $this->actingAs(User::factory()->create());
        $pupils = array();
        for($i = 0;$i < 15;$i++) {
            array_push($pupils, Pupil::factory()->create(['parent_id' => Auth::id()]));
        }
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '240', 'date' => '2021-04-26', 
            'pupils' => [$pupils[0]->id, $pupils[1]->id, $pupils[2]->id, $pupils[3]->id]]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '180', 'date' => '2021-04-26', 
            'pupils' => [$pupils[4]->id, $pupils[5]->id]]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '60', 'date' => '2021-04-26', 
            'pupils' => [$pupils[6]->id, $pupils[7]->id, $pupils[8]->id, $pupils[9]->id, $pupils[10]->id]]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '30', 'date' => '2021-04-26', 
            'pupils' => [$pupils[11]->id]]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '90', 'date' => '2021-04-26', 
            'pupils' => [$pupils[12]->id, $pupils[13]->id, $pupils[14]->id]]);
        for($i=0;$i<5;$i++) {
            $this->actingAs(User::factory()->create(['clubstaff' => 1]));
            $this->post(route('staff-availability.store'), [
                'monday_available_from' => '15:30', 'monday_available_until' => '19:30',
                'tuesday_available_from' => '15:30', 'tuesday_available_until' => '19:30',
                'wednesday_available_from' => '15:30', 'wednesday_available_until' => '19:30',
                'thursday_available_from' => '15:30', 'thursday_available_until' => '19:30',
                'friday_available_from' => '15:30', 'friday_available_until' => '19:30',
                'max_hours' => 100
            ]);
        }
        $this->actingAs(User::factory()->create(['admin' => 1]));
        $this->post(route('staff-schedule.store', ['day' => 1]));
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '16:00:00')->get()) >= 3);
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '16:30:00')->get()) >= 3);
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '17:00:00')->get()) >= 2);
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '18:30:00')->get()) >= 2);
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '19:30:00')->get()) >= 2);
    }

    public function testStaffScheduleForThirtyPupilsBookedIn() {
        $this->actingAs(User::factory()->create());
        $pupils = array();
        for($i = 0;$i < 30;$i++) {
            array_push($pupils, Pupil::factory()->create(['parent_id' => Auth::id()]));
        }
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '90', 'date' => '2021-04-26', 
            'pupils' => [$pupils[0]->id, $pupils[1]->id, $pupils[2]->id, $pupils[3]->id]]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '180', 'date' => '2021-04-26', 
            'pupils' => [
                $pupils[4]->id, $pupils[5]->id, 
                $pupils[6]->id, $pupils[7]->id, 
                $pupils[8]->id, $pupils[9]->id, 
                $pupils[10]->id, $pupils[11]->id, 
                $pupils[12]->id, $pupils[13]->id, 
                $pupils[14]->id, $pupils[15]->id
            ]]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '210', 'date' => '2021-04-26', 
            'pupils' => [$pupils[16]->id, $pupils[17]->id]]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '120', 'date' => '2021-04-26', 
            'pupils' => [$pupils[18]->id, $pupils[19]->id]]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '60', 'date' => '2021-04-26', 
            'pupils' => [$pupils[20]->id, $pupils[21]->id, $pupils[22]->id, $pupils[23]->id]]);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '240', 'date' => '2021-04-26', 
            'pupils' => [$pupils[24]->id, $pupils[25]->id, $pupils[26]->id, $pupils[27]->id, $pupils[28]->id, $pupils[29]->id]]);
        for($i=0;$i<5;$i++) {
            $this->actingAs(User::factory()->create(['clubstaff' => 1]));
            $this->post(route('staff-availability.store'), [
                'monday_available_from' => '15:30', 'monday_available_until' => '19:30',
                'tuesday_available_from' => '15:30', 'tuesday_available_until' => '19:30',
                'wednesday_available_from' => '15:30', 'wednesday_available_until' => '19:30',
                'thursday_available_from' => '15:30', 'thursday_available_until' => '19:30',
                'friday_available_from' => '15:30', 'friday_available_until' => '19:30',
                'max_hours' => 100
            ]);
        }
        $this->actingAs(User::factory()->create(['admin' => 1]));
        $this->post(route('staff-schedule.store', ['day' => 1]));
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '16:30:00')->get()) >= 2);
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '17:00:00')->get()) >= 2);
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '17:30:00')->get()) >= 2);
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '18:30:00')->get()) >= 2);
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '19:00:00')->get()) >= 2);
        $this->assertTrue(sizeof(StaffSchedule::where('working_until', '>=', '19:30:00')->get()) >= 2);

    }
}
