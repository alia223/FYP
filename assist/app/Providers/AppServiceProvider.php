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
            $rules = Rule::all()->first();
            $view->with('rules', $rules);
        });
    }
}
