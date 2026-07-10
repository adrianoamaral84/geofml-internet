<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;


class ReCaptcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
{

dd('Entrou no passes');
    // Em ambiente local, sempre valida como verdadeiro
    if (app()->environment('local')) {
        return true;
    }

    $http = Http::timeout(10);

    if (env('HTTP_PROXY')) {
        $http = $http->withOptions([
            'proxy' => env('HTTP_PROXY'),
        ]);
    }

    $response = $http->asForm()->post(
        'https://www.google.com/recaptcha/api/siteverify',
        [
            'secret'   => env('GOOGLE_RECAPTCHA_SECRET'),
            'response' => $value,
        ]
    );

    return data_get($response->json(), 'success', false);




        /*
            $response = Http::get("https://www.google.com/recaptcha/api/siteverify",[

            'secret' => env('GOOGLE_RECAPTCHA_SECRET'),

	    'response' => $value
            
            $response = Http::withOptions(['proxy' => 'http://10.42.130.22:3128'])
            ->get("https://www.google.com/recaptcha/api/siteverify",[
            'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
            'response' => $value
    
        ]);

          
        //dd($response);
        return $response->json()["success"];
    
    */
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
         return 'The google recaptcha is required.';
    }
}
