<?php

namespace App\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers as LaravelAuthenticatesUsers;
use App\Rules\ReCaptcha;

trait AuthenticatesUsers
{
    use LaravelAuthenticatesUsers {
        validateLogin as protected laravelValidateLogin;
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

     

        if (app()->environment('production')) {
    $rules['g-recaptcha-response'] = [
        'required',
        new ReCaptcha
    ];
}

        $request->validate($rules);
    }
}