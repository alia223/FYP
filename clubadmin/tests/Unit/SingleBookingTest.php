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

class SingleBookingTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testCreateASingleBookingWithoutSelectingATime() {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Ensure no records in databse prior to test
        $this->assertDatabaseCount('bookings', 0);
        //Attempt to create a booking without seleting a time -> should return an error because a time for the booking must be selected
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'date' => '2021-04-13', 'pupils' => [$pupil->id]])->assertSessionHasErrors(["booking_length"]);
    }

    public function testCreateASingleBookingWithoutSelectingAPupil() {
        //Set up authenticated user and pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Ensure no records in database prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '90', 'date' => '2021-04-13'])->assertSessionHasErrors(["pupils"]);
    }

    public function testCreateASingleBookingWithDurationLongerMaximumClubDuration() {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Ensure no records in database prior to test
        $this->assertDatabaseCount('bookings', 0);
        //Attempt to create a booking with a duration longer than the length of the club (default club time is 15:30 - 19:30 i.e. 4 hours or 240 minutes)
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '500', 'date' => '2021-04-13', 'pupils' => [$pupil->id]])->assertSessionHasErrors();
    }

    /**
     * @return void
     */
    public function testCreateASingleBookingForThirtyMinutes()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Ensure no records in database prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a 30 minute booking
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '30', 'date' => '2021-04-13', 'pupils' => [$pupil->id]]);
        //Chceck that this booking exists in database
        $this->assertDatabaseHas('bookings',['parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00',
            'duration' => 30]);
        $this->assertDatabaseHas('booked_pupils', ['parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',
            'end_time' => '16:00:00']);
    }

    /**
     * @return void
     */
    public function testCreateASingleBookingForTwoHours()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Ensure no records in database prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a two hour booking
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '120', 'date' => '2021-04-13',
            'pupils' => [$pupil->id]]);
        //Chceck that this booking exists in database
        $this->assertDatabaseHas('bookings',['parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '17:30:00',
            'duration' => 120]);
        $this->assertDatabaseHas('booked_pupils', ['parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',
            'end_time' => '17:30:00']);
    }

    /**
     * @return void
     */
    public function testCreateASingleBookingForFourHours()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Ensure no records in database prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a four hour booking
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '240', 'date' => '2021-04-13',
            'pupils' => [$pupil->id]]);
        //Chceck that this booking exists in database
        $this->assertDatabaseHas('bookings',['parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '19:30:00',
            'duration' => 240]);
        $this->assertDatabaseHas('booked_pupils', ['parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',
            'end_time' => '19:30:00']);
    }

    /**
     * @return void
     */
    public function testCreateASingleBookingWithThreePupils()
    {
        //Set up authenticated user(parent) and create 3 pupils that are a children of this user
        $this->actingAs(User::factory()->create());
        $pupil1 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil2 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil3 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Ensure no records in database prior to test
        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booked_pupils', 0);
        //Attempt to create a booking that has 3 children (of one parent) being booked at the same time
        $this->post(route('bookings.store'), ['parent_id' => Auth::id(), 'booking_length' => '30', 'date' => '2021-04-13',
            'pupils' => [$pupil1->id, $pupil2->id, $pupil3->id]]);
        //Chceck that this booking exists
        $this->assertDatabaseHas('bookings',['parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00',
            'duration' => 30]);
        $this->assertDatabaseHas('booked_pupils', ['parent_id' => Auth::id(), 'pupil_id' => $pupil1->id, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',
            'end_time' => '16:00:00']);
        $this->assertDatabaseHas('booked_pupils', ['parent_id' => Auth::id(), 'pupil_id' => $pupil2->id, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',
            'end_time' => '16:00:00']);
        $this->assertDatabaseHas('booked_pupils', ['parent_id' => Auth::id(), 'pupil_id' => $pupil3->id, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',
            'end_time' => '16:00:00']);
    }


     /**
     * @return void
     */
    public function testSingleBookingUpdateWithNoChange()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user and also make a booking for this pupil
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $booking = Booking::factory()->create(['parent_id' => Auth::id(), 'event_id' => 1, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',  
        'end_time' => '16:00:00', 'duration' => 30]);
        $booked_pupil = BookedPupil::factory()->create(['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 
        'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        //Chceck that this booking exists in database
        $this->assertDatabaseHas('bookings',['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2, 
        'start_time' => '15:30:00', 'end_time' => '16:00:00', 'duration' => 30]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        //Attempt to send an update booking request but do not actually change any values
        $this->put(route('bookings.update',$booking->id), ['booking_length' => '30', 'date' => '2021-04-13', 'pupils' => [$pupil->id]]);
        //Chceck that this booking exists in database and remains unchanged
        $this->assertDatabaseHas('bookings',['id' => $booking->id,'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2, 
        'start_time' => '15:30:00', 'end_time' => '16:00:00', 'duration' => 30]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']); 
    }

     /**
     * @return void
     */
    public function testSingleBookingUpdateFromOnePupilToNoPupils()
    {
        //Set up authenticated user(parent) and create a pupil that is a child of this user and also make a booking for this pupil
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $booking = Booking::factory()->create(['parent_id' => Auth::id(), 'event_id' => 1, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 
            'end_time' => '16:00:00', 'duration' => 30]);
        $booked_pupil = BookedPupil::factory()->create(['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        //Chceck that this booking exists in database
        $this->assertDatabaseHas('bookings',['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2,
            'start_time' => '15:30:00', 'end_time' => '16:00:00', 'duration' => 30]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        //Attempt to update booking by unselecting the one student that was previously selected and booked in -> should return an error
        $this->put(route('bookings.update',$booking->id), ['booking_length' => '30', 'date' => '2021-04-13'])->assertSessionHasErrors(["pupils"]);
    }

    /**
     * @return void
     */
    public function testSingleBookingUpdateFromOnePupilToThreePupils()
    {
        //Set up authenticated user(parent) and create 3 pupils that are a children of this user
        $this->actingAs(User::factory()->create());
        $pupil1 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil2 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil3 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        //Ensure no records in database prior to test
        $this->assertDatabaseCount('booked_pupils', 0);
        //Set up authenticated user(parent) and create a pupil that is a child of this user and also make a booking for this pupil
        $booking = Booking::factory()->create(['parent_id' => Auth::id(), 'event_id' => 1, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',  'end_time' => '16:00:00', 
            'duration' => 30]);
        $booked_pupil = BookedPupil::factory()->create(['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil1->id, 
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        //Check that only 1 pupil is booked in currently
        $this->assertDatabaseCount('booked_pupils', 1);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil1->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        //update booking from one pupil to now the same booking but instead with three pupils
        $this->put(route('bookings.update',$booking->id), ['booking_length' => '30', 'date' => '2021-04-13', 'pupils' => [$pupil1->id, $pupil2->id, $pupil3->id]]);
        //Check that the 3 pupils of this parent are now booked in at this time, on this date, isntead of only 1 pupil 
        $this->assertDatabaseCount('booked_pupils', 3);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil1->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil2->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil3->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
    }

        /**
     * @return void
     */
    public function testSingleBookingUpdateFromThreePupilsToOnePupil()
    {
        //Set up authenticated user(parent) and create 3 pupils that are a children of this user
        $this->actingAs(User::factory()->create());
        $pupil1 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil2 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil3 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupils = [$pupil1->id, $pupil2->id, $pupil3->id];
        //Ensure no records in database prior to test
        $this->assertDatabaseCount('booked_pupils', 0);
        //Create a booking with 3 pupils of this user/parent booked in
        $booking = Booking::factory()->create(['parent_id' => Auth::id(), 'event_id' => 1, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',  'end_time' => '16:00:00', 
            'duration' => 30]);
        for($i = 0;$i < 3;$i++) {
            BookedPupil::factory()->create(['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupils[$i], 
                'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        }
        //Check that the bookings for these 3 pupils now exist in the database
        $this->assertDatabaseCount('booked_pupils', 3);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil1->id,
        'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil2->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil3->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        //Attempt to update booking from three of this parent's children being booked in, to only one pupil
        $this->put(route('bookings.update',$booking->id), ['booking_length' => '30', 'date' => '2021-04-13', 'pupils' => [$pupil1->id]]);
        //Check that only 1 pupil is associated with this booking instead of 3 pupils
        $this->assertDatabaseCount('booked_pupils', 1);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil1->id,
        'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
    }

    /**
     * @return void
     */
    public function testSingleBookingUpdateFromThirtyMinutesToFourHours()
    {
        //Set up authenticated user(parent) and create a 30 minute booking
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $booking = Booking::factory()->create(['parent_id' => Auth::id(), 'event_id' => 1, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',  'end_time' => '16:00:00', 
            'duration' => 30]);
        $booked_pupil = BookedPupil::factory()->create(['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id, 
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',  'end_time' => '16:00:00']);
        //Check that database has this 30 minute booking stored
        $this->assertDatabaseHas('bookings',['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2,
            'start_time' => '15:30:00', 'end_time' => '16:00:00', 'duration' => 30]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
        //Attempt to update booking from 30 minutes to 4 hours
        $this->put(route('bookings.update',$booking->id), ['booking_length' => '240', 'date' => '2021-04-13', 'pupils' => [$pupil->id]]);
        //Check that booking is now 4 hours long instead of 30 minutes
        $this->assertDatabaseHas('bookings',['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13',
            'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '19:30:00', 'duration' => 240]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'parent_id' => Auth::id(), 'event_id' => $booking->event_id, 'pupil_id' => $pupil->id, 
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '19:30:00']);
    }

    /**
     * @return void
     */
    public function testSingleBookingUpdateFromFourHoursToThirtyMinutes()
    {
        //Set up authenticated user(parent) and create a 4 hour booking
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $booking = Booking::factory()->create(['parent_id' => Auth::id(), 'event_id' => 1, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',  'end_time' => '19:30:00', 
            'duration' => 240]);
        $booked_pupil = BookedPupil::factory()->create(['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',  'end_time' => '19:30:00']);
        //Check that database has this 4 hour booking stored
        $this->assertDatabaseHas('bookings',['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2,
            'start_time' => '15:30:00', 'end_time' => '19:30:00', 'duration' => 240]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '19:30:00']);
        //Attempt to update booking from 4 hours to 30 minutes
        $this->put(route('bookings.update',$booking->id), ['booking_length' => '30', 'date' => '2021-04-13', 'pupils' => [$pupil->id]]);
        //Check that booking is now 30 minutes long instead of 4 hours
        $this->assertDatabaseHas('bookings',['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13',
            'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00', 'duration' => 30]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'parent_id' => Auth::id(), 'event_id' => $booking->event_id, 'pupil_id' => $pupil->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:00:00']);
    }

    /**
     * @return void
     */
    public function testSingleBookingUpdateFromOneToTwoHours()
    {
        //Set up authenticated user(parent) and create a 1 hour booking
        $this->actingAs(User::factory()->create());
        $pupil = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $booking = Booking::factory()->create(['parent_id' => Auth::id(), 'event_id' => 1, 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 
            'end_time' => '16:30:00',  'duration' => 60]);
        $booked_pupil = BookedPupil::factory()->create(['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',  'end_time' => '16:30:00']);
        //Check that database has this 1 hour booking stored
        $this->assertDatabaseHas('bookings',['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2,
            'start_time' => '15:30:00', 'end_time' => '16:30:00', 'duration' => 60]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '16:30:00']);
        //Attempt to update booking from 1 hour to 2 hours
        $this->put(route('bookings.update',$booking->id), ['booking_length' => '120','date' => '2021-04-13','pupils' => [$pupil->id]]);
        //Check that booking is now 2 hours long instead of 1
        $this->assertDatabaseHas('bookings',['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2,
            'start_time' => '15:30:00', 'end_time' => '17:30:00', 'duration' => 120]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'parent_id' => Auth::id(), 'event_id' => $booking->event_id, 'pupil_id' => $pupil->id, 
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '17:30:00',]);
    }

    /**
     * @return void
     */
    public function testSingleBookingDelete()
    {
        //Set up authenticated user(parent) and create a a booking with multiple pupils (2 in this case)
        $this->actingAs(User::factory()->create());
        $pupil1 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $pupil2 = Pupil::factory()->create(['parent_id' => Auth::id()]);
        $booking = Booking::factory()->create(['event_id' => 1, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 
            'end_time' => '17:00:00',  'duration' => 90, ]);
        $booked_pupil = BookedPupil::factory()->create(['parent_id' => Auth::id(), 'booking_id' => $booking->id, 'event_id' => $booking->event_id, 'pupil_id' => $pupil1->id, 
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',  'end_time' => '17:00:00', ]);
        $booked_pupil = BookedPupil::factory()->create(['parent_id' => Auth::id(), 'booking_id' => $booking->id, 'event_id' => $booking->event_id, 'pupil_id' => $pupil2->id, 
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00',   'end_time' => '17:00:00', ]);
        //Check that these 2 pupils are booked in
        $this->assertDatabaseHas('bookings',['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13', 'booking_day' => 2,
            'start_time' => '15:30:00', 'end_time' => '17:00:00', 'duration' => 90]);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'parent_id' => Auth::id(), 'event_id' => $booking->event_id, 'pupil_id' => $pupil1->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '17:00:00']);
        $this->assertDatabaseHas('booked_pupils',['booking_id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'pupil_id' => $pupil2->id,
            'booking_date' => '2021-04-13', 'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '17:00:00']);

        $this->delete('/bookings/'.$booking->id);
        //Check that these 2 pupils are no longer booked in (because a non-admin user is logged in, bookings are marked as deleted in database but not permanently deleted)
        $this->assertSoftDeleted('bookings', ['id' => $booking->id, 'event_id' => $booking->event_id, 'parent_id' => Auth::id(), 'booking_date' => '2021-04-13',
            'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '17:00:00', 'duration' => 90
        ]);
        $this->assertSoftDeleted('booked_pupils',['parent_id' => Auth::id(), 'event_id' => $booking->event_id, 'pupil_id' => $pupil1->id, 'booking_date' => '2021-04-13',
            'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '17:00:00']);
        $this->assertSoftDeleted('booked_pupils',['parent_id' => Auth::id(), 'event_id' => $booking->event_id, 'pupil_id' => $pupil2->id, 'booking_date' => '2021-04-13', #
            'booking_day' => 2, 'start_time' => '15:30:00', 'end_time' => '17:00:00']);
    }
}
