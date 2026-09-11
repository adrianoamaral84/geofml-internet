<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    protected $message = 'A senha informada não atende aos requisitos de segurança.';

    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            $this->message = 'A senha deve ser um texto válido.';
            return false;
        }

        $length = mb_strlen($value);

        if ($length < 8 || $length > 64) {
            $this->message = 'A senha deve ter entre 8 e 64 caracteres.';
            return false;
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $this->message = 'A senha deve conter pelo menos uma letra maiúscula.';
            return false;
        }

        if (!preg_match('/[a-z]/', $value)) {
            $this->message = 'A senha deve conter pelo menos uma letra minúscula.';
            return false;
        }

        if (!preg_match('/[0-9]/', $value)) {
            $this->message = 'A senha deve conter pelo menos um número.';
            return false;
        }

        if (!preg_match('/[^A-Za-z0-9\s]/', $value)) {
            $this->message = 'A senha deve conter pelo menos um símbolo.';
            return false;
        }

        $normalized = mb_strtolower(trim($value), 'UTF-8');
        $blocklist = array_map(function ($password) {
            return mb_strtolower(trim($password), 'UTF-8');
        }, config('password_security.blocklist', []));

        if (in_array($normalized, $blocklist, true)) {
            $this->message = 'Esta senha é muito comum ou já apareceu em listas de senhas comprometidas. Escolha outra senha.';
            return false;
        }

        $compactPassword = preg_replace('/[^a-z0-9]/i', '', $normalized);
        $request = request();
        $user = auth()->user();

        if (!$user && $request->filled('email')) {
            $user = \App\User::where('email', $request->input('email'))->first();
        }

        $cpf = preg_replace('/\D+/', '', (string) ($request->input('cpf') ?: optional($user)->cpf));
        if (strlen($cpf) >= 6 && strpos($compactPassword, $cpf) !== false) {
            $this->message = 'A senha não pode conter o CPF do usuário.';
            return false;
        }

        $email = (string) ($request->input('email') ?: optional($user)->email);
        if ($email && strpos($email, '@') !== false) {
            $emailName = mb_strtolower(explode('@', $email)[0], 'UTF-8');
            $emailName = preg_replace('/[^a-z0-9]/i', '', $emailName);

            if (strlen($emailName) >= 4 && strpos($compactPassword, $emailName) !== false) {
                $this->message = 'A senha não pode conter uma parte significativa do e-mail do usuário.';
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
