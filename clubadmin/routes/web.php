<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', function () {
    return view('./auth/login');
});

/* Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/past-bookings', [App\Http\Controllers\PastBookingsController::class, 'index'])->name('past-bookings');
Route::get('/activity-log', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log');
Route::get('/control-panel', [App\Http\Controllers\ControlPanelController::class, 'index'])->name('control-panel');
Route::get('/rules', [App\Http\Controllers\RuleController::class, 'index'])->name('rules');
Route::get('/club-students', [App\Http\Controllers\StudentController::class, 'index'])->name('club-students');
Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
Route::get('/student-register', [App\Http\Controllers\StudentRegisterController::class, 'index'])->name('student-register');
Route::get('/student-injury-record', [App\Http\Controllers\StudentInjuryController::class, 'index'])->name('student-injury-record');
Route::get('/student-behaviour-record', [App\Http\Controllers\StudentBehaviourController::class, 'index'])->name('student-behaviour-record');
Route::get('/club-rooms', [App\Http\Controllers\RoomController::class, 'index'])->name('club-rooms'); */

Route::resource('home', App\Http\Controllers\HomeController::class);
Route::resource('bookings', App\Http\Controllers\BookingController::class);
Route::resource('repeat-bookings', App\Http\Controllers\RepeatBookingController::class);
Route::resource('create-bookings', App\Http\Controllers\CreateBookingsController::class);
Route::resource('past-bookings', App\Http\Controllers\PastBookingsController::class);
Route::resource('students', App\Http\Controllers\StudentController::class);
Route::resource('booked_students', App\Http\Controllers\BookedStudentController::class);
Route::resource('student-register', App\Http\Controllers\StudentRegisterController::class);
Route::resource('student-register-undo', App\Http\Controllers\StudentRegisterUndoController::class);
Route::resource('injuries', App\Http\Controllers\StudentInjuryController::class);
Route::resource('settings', App\Http\Controllers\SettingsController::class);
Route::resource('rules', App\Http\Controllers\RuleController::class);
Route::resource('activity-log', App\Http\Controllers\ActivityLogController::class);
Route::resource('control-panel', App\Http\Controllers\ControlPanelController::class);
Route::resource('staff-availability', App\Http\Controllers\StaffAvailabilityController::class);
Route::resource('staff-schedule', App\Http\Controllers\StaffScheduleController::class);


//Route::post('/create', [App\Http\Controllers\BookingController::class,'store']);


