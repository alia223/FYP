<?php

namespace Tests\Unit;
use Tests\Testcase;
use HasFactory;
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class SingleBookingTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSingleBookingCreation()
    {
        $this->withoutMiddleware();
        $this->actingAs(User::factory()->create());

        $student1 = Student::factory()->create(['parentid' => Auth::id()]);
        $student2 = Student::factory()->create(['parentid' => Auth::id()]);
        $response = $this->post(route('bookings.store'), [
            'userid' => Auth::id(),
            'booking_length' => '90',
            'date' => '2021-04-13',
            'students' => [$student1->id, $student2->id]
        ]);
        $this->assertDatabaseHas('bookings',[
            'id' => 1,
            'userid' => Auth::id(),
            'booking_date' => '2021-04-13',
            'booking_day' => 2,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'duration' => '90',
            'eventid' => 1
        ]);
        $this->assertDatabaseCount('booked_students', 2);
        $this->assertDatabaseHas('booked_students', [
            'id' => 1,
            'parentid' => Auth::id(),
            'bookingid' => 1,
            'eventid' => 1,
            'studentid' => $student1->id,
            'booking_date' => '2021-04-13',
            'booking_day' => 2,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00'
        ]);

        $this->assertDatabaseHas('booked_students', [
            'id' => 2,
            'parentid' => Auth::id(),
            'bookingid' => 1,
            'eventid' => 1,
            'studentid' => $student2->id,
            'booking_date' => '2021-04-13',
            'booking_day' => 2,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00'
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSingleBookingUpdate()
    {
        $this->withoutMiddleware();
        $this->actingAs(User::factory()->create());
        $student = Student::factory()->create(['parentid' => Auth::id()]);
        $response = $this->post(route('bookings.store'), [
            'userid' => Auth::id(),
            'booking_length' => '90',
            'date' => '2021-04-13',
            'students' => [$student->id]
        ]);

        $this->assertDatabaseHas('bookings',[
            'id' => 2,
            'userid' => Auth::id(),
            'booking_date' => '2021-04-13',
            'booking_day' => 2,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'duration' => '90',
            'eventid' => 1
        ]);

        $response = $this->put(route('bookings.update', 2), [
            'userid' => Auth::id(),
            'booking_length' => '60',
            'date' => '2021-04-13',
            'students' => [$student->id]
        ]);

        $this->assertDatabaseHas('bookings',[
            'id' => 2,
            'userid' => Auth::id(),
            'booking_date' => '2021-04-13',
            'booking_day' => 2,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
            'eventid' => 1
        ]);

        $this->assertDatabaseHas('booked_students',[
            'id' => 4,
            'parentid' => Auth::id(),
            'bookingid' => 2,
            'eventid' => 1,
            'studentid' => $student->id,
            'booking_date' => '2021-04-13',
            'booking_day' => 2,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00'
        ]);
    }

            /**
     * A basic test example.
     *
     * @return void
     */
    public function testSingleBookingDelete()
    {
        $this->withoutMiddleware();
        $this->actingAs(User::factory()->create());
        $student = Student::factory()->create(['parentid' => Auth::id()]);
        $this->post(route('bookings.store'), [
            'userid' => Auth::id(),
            'booking_length' => '90',
            'date' => '2021-04-13',
            'students' => [$student->id]
        ]);

        $this->assertDatabaseHas('bookings',[
            'id' => 3,
        ]);

        $this->delete('/bookings/3');

        $this->assertSoftDeleted('bookings', [
            'id' => 3
        ]);
    }
}
