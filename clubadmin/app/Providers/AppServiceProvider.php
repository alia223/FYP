<?php

namespace App\Providers;
use View;
use Illuminate\Support\ServiceProvider;
use App\Models\Rule;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        View::composer('*', function($view){
            $rules = Rule::all();
            $brand_colour = "";
            $text_colour = "";
            $club_start = "";
            $club_end = "";
            $club_duration_step = "";
            $booking_interval = "";
            $room_capacity = "";
            $brand_logo = "";
            $student_ratio = "";
            foreach($rules as $r) {
                $brand_colour = $r->brand_colour;
                $text_colour = $r->text_colour;
                $club_start = $r->club_start;
                $club_end = $r->club_end;
                $club_duration_step = $r->club_duration_step;
                $booking_interval = $r->booking_interval;
                $room_capacity = $r->room_capacity;
                $brand_logo = $r->brand_logo;
                $student_ratio = $r->student_ratio;
            }
            $rules = array();
            array_push($rules, $brand_colour);
            array_push($rules, $text_colour);
            array_push($rules, $club_start);
            array_push($rules, $club_end);
            array_push($rules, $club_duration_step);
            array_push($rules, $booking_interval);
            array_push($rules, $room_capacity);
            array_push($rules, $brand_logo);
            array_push($rules, $student_ratio);
            $view->with('rules', $rules);
        });
    }
}
