<?php

namespace App\Providers;

use App\Observers\UserSecurityObserver;
use App\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        User::observe(UserSecurityObserver::class);

        // Compatibility with Laratrust Blade directives used by existing menus.
        Blade::if('role', function ($roles) {
            $user = auth()->user();
            if (!$user) {
                return false;
            }
            $names = is_array($roles) ? $roles : preg_split('/[|,]/', (string) $roles);
            foreach ($names as $name) {
                if ($user->hasRole(trim((string) $name))) {
                    return true;
                }
            }
            return false;
        });
    }
}
