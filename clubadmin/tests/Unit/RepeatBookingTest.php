<?php

namespace Tests\Unit;
use Tests\Testcase;
use HasFactory;
use App\Models\User;
use App\Models\Pupil;
use App\Models\Booking;
use App\Models\BookedPupil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;

class RepeatBookingTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testCreateARepeatBookingWithoutSelectingATime() {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        //Attempt to create a repeat booking without selecting a time for the booking, error should be returned
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'pupils' => [$pupil->id], 'recursive_end_date' => '2021-05-31', 
            'recursive_days' => [1]])->assertSessionHasErrors(["booking_length"]);
    }

    public function testCreateARepeatBookingWithoutSelectingAPupil() {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        //Attempt to create a repeat booking without selecting any pupils for the booking, error should be returned
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '30', 'recursive_end_date' => '2021-05-31', 
            'recursive_days' => [1]])->assertSessionHasErrors(["pupils"]);
    }

    /**
     * @return void
     */
    public function testCreateARepeatBookingForThirtyMinutes()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a 30 minute repeat booking on mondays i.e. a booking that is set up for every monday from the date selected to the recursive_end_date
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '30', 'pupils' => [$pupil->id],
            'recursive_end_date' => '2021-05-31', 'recursive_days' => [1]]);
        //Check that a 30 minute booking exists every monday in database from selected date to recursive_end_date
        $expected_dates_of_booking = ['2021-05-03', '2021-05-10', '2021-05-17', '2021-05-24', '2021-05-31'];
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', ['parent_id' => Auth::id(), 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 'start_time' => '15:30:00', 
                'end_time' => '16:00:00', 'duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', ['parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00',  'end_time' => '16:00:00']);
        }
    }

    /**
     * @return void
     */
    public function testCreateARepeatBookingForTwoHours()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a 2 hour repeat booking on mondays i.e. a booking that is set up for every monday from the date selected to the recursive_end_date
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '120', 'pupils' => [$pupil->id], 
        'recursive_end_date' => '2021-05-31', 'recursive_days' => [1]]);
        //Check that a 2 hour booking exists every monday in database from selected date to recursive_end_date
        $expected_dates_of_booking = ['2021-05-03', '2021-05-10', '2021-05-17', '2021-05-24', '2021-05-31'];
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(), 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 'start_time' => '15:30:00', 
                'end_time' => '17:30:00', 'duration' => 120]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00', 'end_time' => '17:30:00']);
        }
    }

    /**
     * @return void
     */
    public function testCreateARepeatBookingForFourHours()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a 4 hour repeat booking on mondays i.e. a booking that is set up for every monday from the date selected to the recursive_end_date
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '240', 'pupils' => [$pupil->id], 
            'recursive_end_date' => '2021-05-31', 'recursive_days' => [1]]);
        //Check that a 4 hour booking exists every monday in database from selected date to recursive_end_date
        $expected_dates_of_booking = ['2021-05-03', '2021-05-10', '2021-05-17', '2021-05-24', '2021-05-31'];
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(), 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 'start_time' => '15:30:00', 
                'end_time' => '19:30:00', 'duration' => 240]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id,  'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00',  'end_time' => '19:30:00']);
        }
    }

    /**
     * @return void
     */
    public function testCreateARepeatBookingOnThreeDaysOfTheWeek()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a repeat booking on 3 days of the week i.e. in this scenario, a booking that is set up for every monday, tuesday and wednesday
        //from the date selected to the recursive_end_date
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '30', 'pupils' => [$pupil->id], 
            'recursive_end_date' => '2021-05-31', 'recursive_days' => [1,2,3]]);
        //Check that a 30 minute booking exists every monday, tuesday and wednesday in database from selected date to recursive_end_date
        $expected_dates_of_booking = ['2021-05-03', '2021-05-04', '2021-05-05', '2021-05-10', '2021-05-11', '2021-05-12', '2021-05-17', '2021-05-18', '2021-05-19', '2021-05-24', '2021-05-25', 
            '2021-05-26', '2021-05-31'];
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(), 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 'start_time' => '15:30:00', 
                'end_time' => '16:00:00', 'duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00',  'end_time' => '16:00:00',]);
        }
    }

     /**
     * @return void
     */
    public function testCreateARepeatBookingForEveryDayOfTheWeek()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a repeat booking on all days of the week i.e. in this scenario, a booking that is set up for every monday, tuesday, wednesday, thursday and friday
        // from the date selected to the recursive_end_date
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '30', 'pupils' => [$pupil->id], 'recursive_end_date' => '2021-05-31',
            'recursive_days' => [1,2,3,4,5]]);
        //Check that a 30 minute booking exists every monday, tuesday and wednesday in database from selected date to recursive_end_date
        $expected_dates_of_booking = ['2021-05-03', '2021-05-04', '2021-05-05', '2021-05-06', '2021-05-07', '2021-05-10', '2021-05-11', '2021-05-12', '2021-05-13', '2021-05-14', 
            '2021-05-17', '2021-05-18','2021-05-19', '2021-05-20', '2021-05-21', '2021-05-24', '2021-05-25', '2021-05-26', '2021-05-27', '2021-05-28', '2021-05-31'];
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(), 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 'start_time' => '15:30:00', 
                'end_time' => '16:00:00', 'duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        }
    }

    /**
     * @return void
     */
    public function testRepeatBookingUpdateWithNoChange()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Create a 30 minute repeat booking from date to recursive_end_date on Mondays, with one pupil
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '30', 'pupils' => [$pupil->id], 
            'recursive_end_date' => '2021-05-10', 'recursive_days' => [1]]);
        $expected_dates_of_booking = ['2021-05-03', '2021-05-10'];
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(), 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 'start_time' => '15:30:00', 
                'end_time' => '16:00:00', 'duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00',  'end_time' => '16:00:00',]);
        }
        //Get id of previous booking made and then request to update it but do not actually change any values. Booking should remain unchanged.
        $id_of_booking_to_update = Booking::where(['parent_id' => Auth::id(), 'booking_date' => '2021-05-03', 'duration' => '30'])->first()->id;
        $this->put(route('repeat-bookings.update', $id_of_booking_to_update), ['booking_length' => '30', 'date' => '2021-05-03', 'pupils' => [$pupil->id]]);
        //Check to see if booking remained unchanged
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(), 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 'start_time' => '15:30:00', 
                'end_time' => '16:00:00', 'duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id,  'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])),  
                'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        }
    }
    
    /**
     * @return void
     */
    public function testRepeatBookingUpdateFromOnePupilToThreePupils()
    {
        //Set up authenticated user(parent) and create 3 pupils that are children of this user
        $this->actingAs(User::factory()->create());
        $pupil1 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil2 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil3 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Create a 30 minute repeating booking with one pupil, on Mondays from date to recursive_end_date
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '30', 'pupils' => [$pupil1->id], 
            'recursive_end_date' => '2021-05-10', 'recursive_days' => [1]]);
        $expected_dates_of_booking = ['2021-05-03', '2021-05-10'];
        //Check to see that there is only a booking for one pupil (that is a child of this user) on Mondays from date to recursive_end_date
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(),  'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])),  'start_time' => '15:30:00', 
                'end_time' => '16:00:00','duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(), 'pupil_id' => $pupil1->id,  'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        }
        //Get id of previous booking and update it from a booking with one pupil to a booking with 3 pupils on each monday from date to recursive_end_date on Mondays
        $id_of_booking_to_update = Booking::where(['parent_id' => Auth::id(), 'booking_date' => '2021-05-03', 'duration' => '30'])->first()->id;
        $this->put(route('repeat-bookings.update',$id_of_booking_to_update), ['booking_length' => '30', 'date' => '2021-05-01', 'pupils' => [$pupil1->id, $pupil2->id, $pupil3->id]]);
        //Check to see that there is a booking for 3 pupils (that are children of this user) on Mondays from date to recursive_end_date
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', ['parent_id' => Auth::id(),  'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])),  'start_time' => '15:30:00', 
                'end_time' => '16:00:00', 'duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(),  'pupil_id' => $pupil1->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00',  'end_time' => '16:00:00',]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(),  'pupil_id' => $pupil2->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00',  'end_time' => '16:00:00']);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(),  'pupil_id' => $pupil2->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        }
    }

    /**
     * @return void
     */
    public function testRepeatBookingUpdateFromThreePupilsTOnePupil()
    {
        //Set up authenticated user(parent) and create 3 pupils that are children of this user
        $this->actingAs(User::factory()->create());
        $pupil1 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil2 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil3 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Create a 30 minute repeating booking with 3 pupils, on Mondays from date to recursive_end_date
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '30', 'pupils' => [$pupil1->id, $pupil2->id, $pupil3->id], 
            'recursive_end_date' => '2021-05-10', 'recursive_days' => [1]]);
        $expected_dates_of_booking = ['2021-05-03', '2021-05-10'];
        //Check to see that there is a booking for 3 pupils (that are children of this user) on Mondays from date to recursive_end_date
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(),  'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])),  'start_time' => '15:30:00', 
                'end_time' => '16:00:00','duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(),  'pupil_id' => $pupil1->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00', 'end_time' => '16:00:00']);
            $this->assertDatabaseHas('booked_pupils', [ 
                'parent_id' => Auth::id(),  'pupil_id' => $pupil2->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00', 'end_time' => '16:00:00',]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(),  'pupil_id' => $pupil2->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00',  'end_time' => '16:00:00',]);
        }
        //Get id of previous booking and update it from a booking with 3 pupils to a booking with one pupil on each monday from date to recursive_end_date on Mondays
        $id_of_booking_to_update = Booking::where(['parent_id' => Auth::id(), 'booking_date' => '2021-05-03', 'duration' => '30'])->first()->id;
        $this->put(route('repeat-bookings.update', $id_of_booking_to_update), ['booking_length' => '30', 'date' => '2021-05-01', 'pupils' => [$pupil1->id]]);
        //Check to see that there is only a booking for one pupil (that is a child of this user) on Mondays from date to recursive_end_date
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(),  'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])),  'start_time' => '15:30:00', 
                'end_time' => '16:00:00', 'duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(),  'pupil_id' => $pupil1->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00', 'end_time' => '16:00:00',]);
            $this->assertDatabaseMissing('booked_pupils', [ 'pupil_id' => $pupil2->id]);            
            $this->assertDatabaseMissing('booked_pupils', [ 'pupil_id' => $pupil3->id]);
        }
    }

        /**
     * @return void
     */
    public function testSingleBookingDelete()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Check database is empty prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a 30 minute repeat booking on mondays i.e. a booking that is set up for every monday from the date selected to the recursive_end_date
        $this->post(route('repeat-bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-05-01', 'booking_length' => '30', 'pupils' => [$pupil->id],
            'recursive_end_date' => '2021-05-31', 'recursive_days' => [1]]);
        //Check that a 30 minute booking exists every monday in database from selected date to recursive_end_date
        $expected_dates_of_booking = ['2021-05-03', '2021-05-10', '2021-05-17', '2021-05-24', '2021-05-31'];
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertDatabaseHas('bookings', [ 'parent_id' => Auth::id(),  'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])),  'start_time' => '15:30:00', 
                'end_time' => '16:00:00', 'duration' => 30]);
            $this->assertDatabaseHas('booked_pupils', [ 'parent_id' => Auth::id(),  'pupil_id' => $pupil->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        }
        //Get id of booking that has just been created and attempt to delete it
        $id_of_booking_to_update = Booking::where(['parent_id' => Auth::id(), 'booking_date' => '2021-05-03', 'duration' => '30'])->first()->id;
        $this->delete('/repeat-bookings/'.$id_of_booking_to_update);
        //Check that these this repeat-booking no longer exists i.e. it has been soft deleted (because user is logged in. Only admin can permanently delete, non-admins can only "mark as deleted")
        for($i = 0;$i < sizeof($expected_dates_of_booking);$i++) {
            $this->assertSoftDeleted('bookings', [ 'parent_id' => Auth::id(),  'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])),  'start_time' => '15:30:00', 
                'end_time' => '16:00:00', 'duration' => 30]);
            $this->assertSoftDeleted('booked_pupils', [ 'parent_id' => Auth::id(),  'pupil_id' => $pupil->id, 'booking_date' => $expected_dates_of_booking[$i], 'booking_day' => date('N', strtotime($expected_dates_of_booking[$i])), 
                'start_time' => '15:30:00',  'end_time' => '16:00:00']);
        }
    }
}
