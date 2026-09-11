<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Rules\ReCaptcha;
use App\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/admin';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        if (isset($request['cpf'])) {
            $request['cpf'] = str_replace([".", "-"], "", $request['cpf']);
        }

        return [
            'cpf' => $request->cpf,
            'password' => $request->password,
            'status' => [1, 3, 5, 6],
        ];
    }

    protected function validateLogin(Request $request)
    {
        $rules = [
            $this->username() => ['required', 'string'],
            'password' => ['required', 'string'],
        ];

        if (config('services.recaptcha.enabled')) {
            $rules['g-recaptcha-response'] = [
                'required',
                new ReCaptcha(),
            ];
        }

        $request->validate($rules, [
            'g-recaptcha-response.required' => 'Marque a opção “Não sou um robô”.',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        // Migra com segurança contas antigas que ainda utilizam o CPF como senha.
        if ($user->cpf && $this->passwordMatches($user->cpf, $user->password)) {
            // Invalida imediatamente a senha baseada em CPF para que ela não possa ser reutilizada.
            $user->password = Hash::make(\Illuminate\Support\Str::random(64));
            $user->save();

            if ($user->email) {
                try {
                    Password::broker()->sendResetLink(['email' => $user->email]);
                } catch (\Throwable $e) {
                    \Log::error('Erro ao enviar redefinição para usuário com senha legada.', [
                        'user_id' => $user->id,
                        'erro' => $e->getMessage(),
                    ]);
                }
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            \Session::flash('message', [
                'msg' => $user->email
                    ? 'Por segurança, sua senha antiga foi invalidada. Enviamos um link para o seu e-mail para criar uma nova senha.'
                    : 'Por segurança, sua senha antiga foi invalidada. Procure o administrador para redefinir seu acesso.',
                'class' => 'warning',
            ]);

            return redirect('/login');
        }

        // Bloqueia contas que ficaram mais de 90 dias sem login.
        if ($user->last_login_at && $user->last_login_at->lt(now()->subDays(90))) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            \Session::flash('message', [
                'msg' => 'Seu acesso foi bloqueado por inatividade superior a 90 dias. Procure o administrador do sistema para reativar sua conta.',
                'class' => 'warning',
            ]);

            return redirect('/login');
        }

        $user->last_login_at = now();
        $user->save();

        $hoje = date('Y-m-d');
        if ($user->indeterminado != 1) {
            if (strtotime($user->validade) < strtotime($hoje)) {
                \Session::flash('message', [
                    'msg' => 'Seu documento de identidade está com a data de validade vencida! Favor atualizar o documento para prosseguir',
                    'class' => 'danger',
                ]);
            }
        }

        if ($user->hasRole('hospede')) {
            return redirect('/hospede');
        }

        if ($user->hasRole('precadastro')) {
            return redirect('/precadastro');
        }

        Session::flush();
        Auth::logout();

        return redirect('login');
    }

    protected function passwordMatches($plain, $hash)
    {
        try {
            return Hash::check((string) $plain, (string) $hash);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
