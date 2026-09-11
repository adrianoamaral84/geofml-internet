<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;

class SecureAdminActionsController extends Controller
{
    public function toggleUserStatus($id)
    {
        $this->authorizeUserAdministration();

        $user = User::findOrFail($id);

        if ((int) $user->id === (int) Auth::id()) {
            abort(403, 'Você não pode alterar o status da própria conta.');
        }

        User::changeStatus($user->id);
        $user->refresh();

        if ((int) $user->status === 1 && Schema::hasColumn('user', 'last_login_at')) {
            $user->last_login_at = now();
            $user->save();
        }

        \Session::flash('message', [
            'msg' => ((int) $user->status === 1 ? 'Usuário ativado' : 'Usuário inativado') . ' com sucesso.',
            'class' => 'success',
        ]);

        return redirect()->route('user.index');
    }

    public function sendPasswordReset($id)
    {
        $this->authorizePasswordReset();

        $userId = Crypt::decrypt($id);
        $user = User::findOrFail($userId);

        if (!$user->email) {
            return redirect()->back()->withErrors([
                'email' => 'O usuário não possui e-mail cadastrado para redefinição de senha.',
            ]);
        }

        if (Schema::hasColumn('user', 'last_login_at')) {
            $user->last_login_at = now();
            $user->save();
        }

        $status = Password::broker()->sendResetLink([
            'email' => $user->email,
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            return redirect()->back()->withErrors([
                'email' => 'Não foi possível enviar o link de redefinição de senha. Tente novamente.',
            ]);
        }

        \Session::flash('message', [
            'msg' => 'Link seguro para redefinição de senha enviado ao e-mail do usuário.',
            'class' => 'success',
        ]);

        return redirect()->back();
    }

    private function authorizeUserAdministration()
    {
        $user = Auth::user();

        if (!$user || !(
            $user->hasRole('administrador_geral') ||
            $user->hasRole('administrador_especial') ||
            $user->hasRole('administrador') ||
            $user->hasRole('auxiliar_administrador_geral')
        )) {
            abort(403, 'Você não tem autorização para administrar usuários.');
        }
    }

    private function authorizePasswordReset()
    {
        $user = Auth::user();

        if (!$user || !(
            $user->hasRole('administrador_geral') ||
            $user->hasRole('auxiliar_administrador_geral')
        )) {
            abort(403, 'Você não tem autorização para redefinir senhas de usuários.');
        }
    }
}
