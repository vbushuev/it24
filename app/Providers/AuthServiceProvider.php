<?php

namespace App\Providers;

use Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('uploads', function ($user) {
            Log::debug($user->name." role:".$user->role." for uploads return:".(2 == $user->role));
            return ("admin" == $user->role);
        });
        Gate::define('users', function ($user) {
            Log::debug($user->name." role:".$user->rolw." for users return:".(2 == $user->role));
            return ("admin" == $user->role);
        });
        Gate::define('suppliers', function ($user) {
            Log::debug($user->name." role:".$user->rolw." for suppliers return:".(2 == $user->role));
            return ("admin" == $user->role);
        });
        Gate::define('schedules', function ($user) {
            Log::debug($user->name." role:".$user->role." for suppliers return:".(2 == $user->role));
            return ( in_array($user->role,["admin","client"]));
        });
    }
}
