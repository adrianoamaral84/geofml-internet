<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Throwable;

class ReCaptcha implements Rule
{
    public function passes($attribute, $value)
    {
        if (!config('services.recaptcha.enabled')) {
            return true;
        }

        $secret = config('services.recaptcha.secret_key');

        if (empty($secret) || empty($value)) {
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            if (!$response->successful()) {
                return false;
            }

            return (bool) data_get(
                $response->json(),
                'success',
                false
            );
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    public function message()
    {
        return 'Confirme o CAPTCHA para continuar.';
    }
}