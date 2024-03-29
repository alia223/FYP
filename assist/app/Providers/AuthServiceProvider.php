<?php

namespace App\Providers;

use App\Models\Account;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
    public function registerPolicies()
    {
        Gate::define('admin', function($user) {
            return $user->admin;
        });
        Gate::define('clubstaff', function($user) {
            return $user->clubstaff;
        });
    }
}
