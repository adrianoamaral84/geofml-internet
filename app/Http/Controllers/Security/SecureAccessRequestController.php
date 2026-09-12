<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class SecureAccessRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->merge([
            'cpf' => preg_replace('/\D+/', '', (string) $request->input('cpf')),
            'celular' => preg_replace('/\D+/', '', (string) $request->input('celular')),
        ]);

        $validated = $request->validate([
            'nome' => 'required|string|min:2|max:100',
            'email' => 'required|email|max:100',
            'cpf' => 'required|digits:11',
            'celular' => 'required|digits_between:10,11',
        ]);

        if (!$this->cpfValido($validated['cpf'])) {
            return back()->withInput()->withErrors(['cpf' => 'CPF inválido.']);
        }

        if (User::where('cpf', $validated['cpf'])->exists()) {
            return back()->withInput()->withErrors(['cpf' => 'Este CPF já está cadastrado no sistema.']);
        }

        if (User::where('email', $validated['email'])->exists()) {
            return back()->withInput()->withErrors(['email' => 'Este E-mail já está cadastrado no sistema.']);
        }

        try {
            $status = DB::transaction(function () use ($validated) {
                $usuario = new User();
                $usuario->name = strtoupper($validated['nome']);
                $usuario->email = $validated['email'];
                $usuario->cpf = $validated['cpf'];
                $usuario->telefone = $validated['celular'];
                $usuario->status = 5;
                $usuario->perfil_id = 5;

                // Nunca utiliza CPF ou outro dado pessoal como senha temporária.
                // O valor é desconhecido até pelo usuário; ele definirá a senha
                // por meio do fluxo oficial de reset do Laravel.
                $usuario->password = Hash::make(Str::random(64));
                $usuario->save();
                $usuario->syncRoles(['5']);

                return Password::broker()->sendResetLink([
                    'email' => $usuario->email,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Falha ao criar solicitação de acesso segura.', [
                'email' => $validated['email'],
                'erro' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'email' => 'Não foi possível enviar o link para definição da senha. Tente novamente.',
            ]);
        }

        if ($status !== Password::RESET_LINK_SENT) {
            Log::warning('Password broker não enviou link após solicitação de acesso.', [
                'email' => $validated['email'],
                'status' => $status,
            ]);

            return back()->withInput()->withErrors([
                'email' => 'Não foi possível enviar o link para definição da senha. Tente novamente.',
            ]);
        }

        \Session::flash('message', [
            'msg' => 'Solicitação recebida. Enviamos um e-mail para você definir sua senha de acesso.',
            'class' => 'success',
        ]);

        return redirect('/solicitaacesso');
    }

    private function cpfValido($cpf)
    {
        $cpf = preg_replace('/\D+/', '', (string) $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;

            for ($c = 0; $c < $t; $c++) {
                $soma += (int) $cpf[$c] * (($t + 1) - $c);
            }

            $digito = ((10 * $soma) % 11) % 10;

            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }
}
