<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // Restringe tentativas tanto por endereço quanto por IP.
        $email = mb_strtolower(trim($request->input('email')));
        $emailKey = 'password-reset:email:' . hash('sha256', $email);
        $ipKey = 'password-reset:ip:' . hash('sha256', (string) $request->ip());

        if (RateLimiter::tooManyAttempts($emailKey, 1) ||
            RateLimiter::tooManyAttempts($ipKey, 5)) {
            return back()->with('status',
                'Se o e-mail estiver cadastrado, você receberá as instruções. Aguarde antes de solicitar novamente.');
        }

        RateLimiter::hit($emailKey, 60);
        RateLimiter::hit($ipKey, 60);

        // Mantém a restrição existente para usuários inativos/bloqueados.
        $user = User::where('email', $email)->first();
        if ($user && in_array((int) $user->status, [2, 6], true)) {
            return redirect()->route('login')->with(
                'erro', 'Favor entrar em contato com a Administração!'
            );
        }
        if ($user) {
            $this->broker()->sendResetLink($this->credentials($request));
        }

        // Resposta uniforme: não revela se o endereço existe no sistema.
        return back()->with('status',
            'Se o e-mail estiver cadastrado, você receberá um link para redefinir sua senha.');
    }
}
