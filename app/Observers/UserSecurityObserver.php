<?php

namespace App\Observers;

use App\Rules\StrongPassword;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserSecurityObserver
{
    protected static $passwordSetupRequired = [];

    public function saving($user)
    {
        if (!$user->isDirty('password')) {
            return;
        }

        $passwordHash = (string) $user->password;
        $cpf = preg_replace('/\D+/', '', (string) $user->cpf);

        if ($cpf && $this->hashMatches($cpf, $passwordHash)) {
            $key = spl_object_hash($user);
            self::$passwordSetupRequired[$key] = [
                'new_user' => !$user->exists,
            ];

            $user->password = Hash::make(Str::random(64));

            if ($user->exists && Schema::hasColumn($user->getTable(), 'last_login_at')) {
                $user->last_login_at = now();
            }

            return;
        }

        if (app()->runningInConsole() || !app()->bound('request')) {
            return;
        }

        $request = request();
        $plainPassword = null;

        if ($request->filled('novaSenha')) {
            $plainPassword = $request->input('novaSenha');
        } elseif ($request->filled('password')) {
            $plainPassword = $request->input('password');
        }

        if ($plainPassword !== null && $this->hashMatches($plainPassword, $passwordHash)) {
            Validator::make(
                ['password' => $plainPassword],
                ['password' => ['required', new StrongPassword()]]
            )->validate();

            return;
        }

        if ($user->exists && !$request->filled('password') && !$request->filled('novaSenha')) {
            $originalPassword = $user->getOriginal('password');

            if ($originalPassword) {
                $user->password = $originalPassword;
            }
        }
    }

    public function saved($user)
    {
        $key = spl_object_hash($user);

        if (!isset(self::$passwordSetupRequired[$key])) {
            return;
        }

        $context = self::$passwordSetupRequired[$key];
        unset(self::$passwordSetupRequired[$key]);

        if (!$user->email) {
            $this->setNotice(
                'A senha anterior foi invalidada, mas o usuário não possui e-mail para receber o link de definição da nova senha.',
                'warning'
            );
            return;
        }

        try {
            $status = Password::broker()->sendResetLink(['email' => $user->email]);

            if ($status === PasswordBroker::RESET_LINK_SENT) {
                $message = $context['new_user']
                    ? 'Usuário cadastrado com segurança. Foi enviado um link por e-mail para que ele defina a própria senha.'
                    : 'Senha redefinida com segurança. A senha antiga foi invalidada e um link para criação da nova senha foi enviado ao e-mail do usuário.';

                $this->setNotice($message, 'success');
                return;
            }

            Log::warning('Não foi possível enviar o link de definição de senha.', [
                'user_id' => $user->id,
                'status' => $status,
            ]);

            $this->setNotice(
                'A senha antiga foi invalidada, mas não foi possível enviar o link para criação da nova senha. Verifique o e-mail do usuário e tente o reset novamente.',
                'warning'
            );
        } catch (\Throwable $e) {
            Log::error('Erro ao enviar link seguro de definição de senha.', [
                'user_id' => $user->id,
                'erro' => $e->getMessage(),
            ]);

            $this->setNotice(
                'A senha antiga foi invalidada, mas ocorreu um erro ao enviar o link para criação da nova senha. Tente o reset novamente.',
                'warning'
            );
        }
    }

    protected function hashMatches($plain, $hash)
    {
        try {
            return Hash::check((string) $plain, (string) $hash);
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function setNotice($message, $class)
    {
        if (app()->bound('session')) {
            session()->put('_password_security_notice', [
                'msg' => $message,
                'class' => $class,
            ]);
        }
    }
}
