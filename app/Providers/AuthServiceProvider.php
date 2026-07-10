<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
        //dd($user);

        // Check for Administrator - Serviço Militar
        // Return true if auth user type is SU
        $gate->define('isAdmEsp', function($user){
            return $user->perfil_id == 1;
        });

        // Check for Operador CS
        // Return true if auth user type is Presidente CS
        $gate->define('isAdmGeral', function($user){
            //dd('opeardor');
            return $user->perfil_id == 2;
        });

        // Check for Operador CS
        // Return true if auth user type is Presidente CS
        $gate->define('isAtendente', function($user){
            //dd('opeardor');
            return $user->perfil_id == 3;
        });

        // Check for Operador CS
        // Return true if auth user type is Presidente CS
        $gate->define('isHospede', function($user){
            //dd('opeardor');
            return $user->perfil_id == 4;
        });
        
    }
}
