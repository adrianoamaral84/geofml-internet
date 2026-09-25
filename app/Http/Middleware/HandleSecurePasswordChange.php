<?php

namespace App\Http\Middleware;

use App\Rules\StrongPassword;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HandleSecurePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') && $request->route() && $request->route()->getName() === 'editarSenha') {
            $validated = $request->validate([
                'senhaAtual' => ['required', 'string', 'max:64'],
                'novaSenha' => ['required', 'string', new StrongPassword(), 'different:senhaAtual'],
                'novaSenha_confirmation' => ['required', 'string', 'max:64', 'same:novaSenha'],
            ], [
                'senhaAtual.required' => 'Campo obrigatório',
                'senhaAtual.max' => 'A senha atual deve ter no máximo 64 caracteres',
                'novaSenha.required' => 'Campo obrigatório',
                'novaSenha.different' => 'A nova senha deve ser diferente da senha atual',
                'novaSenha_confirmation.required' => 'Campo obrigatório',
                'novaSenha_confirmation.max' => 'A confirmação da senha deve ter no máximo 64 caracteres',
                'novaSenha_confirmation.same' => 'A confirmação da senha não confere',
            ]);

            $usuario = Auth::user();

            if (!$usuario || !Hash::check($validated['senhaAtual'], $usuario->password)) {
                return redirect()->route('senha')->with('message', [
                    'msg' => 'Senha atual incorreta.',
                    'class' => 'warning',
                ]);
            }

            $usuario->password = Hash::make($validated['novaSenha']);
            $usuario->save();

            return redirect()->route('senha')->with('message', [
                'msg' => 'Senha alterada com sucesso.',
                'class' => 'success',
            ]);
        }

        return $next($request);
    }
}
