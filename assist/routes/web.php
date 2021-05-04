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

Route::resource('home', App\Http\Controllers\HomeController::class);
Route::resource('bookings', App\Http\Controllers\BookingController::class);
Route::resource('repeat-bookings', App\Http\Controllers\RepeatBookingController::class);
Route::resource('create-bookings', App\Http\Controllers\CreateBookingsController::class);
Route::resource('pupils', App\Http\Controllers\PupilController::class);
Route::resource('pupil-dietary-requirements', App\Http\Controllers\PupilDietaryRequirementController::class);
Route::resource('booked-pupils', App\Http\Controllers\BookedPupilController::class);
Route::resource('pupil-register', App\Http\Controllers\PupilRegisterController::class);
Route::resource('pupil-register-undo', App\Http\Controllers\PupilRegisterUndoController::class);
Route::resource('injuries', App\Http\Controllers\PupilInjuryController::class);
Route::resource('settings', App\Http\Controllers\SettingsController::class);
Route::resource('rules', App\Http\Controllers\RuleController::class);
Route::resource('activity-log', App\Http\Controllers\ActivityLogController::class);
Route::resource('staff-availability', App\Http\Controllers\StaffAvailabilityController::class);
Route::resource('staff-schedule', App\Http\Controllers\StaffScheduleController::class);

