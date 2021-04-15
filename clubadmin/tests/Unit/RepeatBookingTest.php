<?php

namespace Tests\Unit;
use Tests\Testcase;
use HasFactory;
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class RepeatBookingTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRepeatBookingCreation()
    {
        $this->withoutMiddleware();
        $this->actingAs(User::factory()->create());

        $student1 = Student::factory()->create(['parentid' => Auth::id()]);
        $student2 = Student::factory()->create(['parentid' => Auth::id()]);
        $response = $this->post(route('repeat-bookings.store'), [
            'userid' => Auth::id(),
            'date' => '2021-04-13',
            'booking_length' => '60',
            'students' => [$student1->id, $student2->id],
            'recursive_end_date' => '2021-04-30',
            'recursive_days' => [4,5]
        ]);
        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-15',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-16',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-22',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-23',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-29',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-30',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseMissing('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-05-06',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseMissing('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-05-07',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);
    }

     /**
     * A basic test example.
     *
     * @return void
     */
    public function testRepeatBookingUpdate()
    {
        $this->withoutMiddleware();
        $this->actingAs(User::factory()->create());
        $student = Student::factory()->create(['parentid' => Auth::id()]);
        $response = $this->post(route('repeat-bookings.store'), [
            'userid' => Auth::id(),
            'date' => '2021-04-13',
            'booking_length' => '60',
            'students' => [$student->id],
            'recursive_end_date' => '2021-04-30',
            'recursive_days' => [4,5]
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-15',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-16',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-22',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-23',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-29',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-30',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->put(route('repeat-bookings.update', 7), [
            'userid' => Auth::id(),
            'date' => '2021-04-13',
            'booking_length' => '90',
            'students' => [$student->id],
        ]);

        $this->assertDatabaseMissing('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-15',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseMissing('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-16',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseMissing('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-22',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseMissing('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-23',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseMissing('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-29',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseMissing('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-30',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-15',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'duration' => '90',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-16',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'duration' => '90',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-22',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'duration' => '90',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-23',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'duration' => '90',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-29',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'duration' => '90',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-30',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '17:00:00',
            'duration' => '90',
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRepeatBookingDelete()
    {
        $this->withoutMiddleware();
        $this->actingAs(User::factory()->create());
        $student = Student::factory()->create(['parentid' => Auth::id()]);
        $response = $this->post(route('repeat-bookings.store'), [
            'userid' => Auth::id(),
            'date' => '2021-04-13',
            'booking_length' => '60',
            'students' => [$student->id],
            'recursive_end_date' => '2021-04-30',
            'recursive_days' => [4,5]
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-15',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-16',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-22',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-23',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-29',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertDatabaseHas('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-30',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->delete('/repeat-bookings/13');

        $this->assertSoftDeleted('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-15',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertSoftDeleted('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-16',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertSoftDeleted('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-22',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertSoftDeleted('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-23',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertSoftDeleted('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-29',
            'booking_day' => 4,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);

        $this->assertSoftDeleted('bookings',[
            'userid' => Auth::id(),
            'booking_date' => '2021-04-30',
            'booking_day' => 5,
            'start_time' => '15:30:00',
            'end_time' => '16:30:00',
            'duration' => '60',
        ]);
    }

}
