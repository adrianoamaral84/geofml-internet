<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the Internet portal.
     *
     * This application does not expose an operational API, so the default
     * routes/api.php scaffold is intentionally not registered.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
    }

    /**
     * Define the web routes used by the Internet portal.
     *
     * The historical routes/web.php contains administrative and attendant
     * routes copied from the intranet/admin application, including references
     * to controllers that do not exist in this project. It remains in the
     * repository only as legacy reference and is no longer registered.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/internet.php'));

        // Hardening is loaded last so the protected replacements keep
        // precedence over compatibility/legacy paths.
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/security_hardening.php'));
    }
}
