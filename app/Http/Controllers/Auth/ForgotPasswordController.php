<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Envia o link de redefinição de senha.
     */
    public function sendResetLinkEmail(Request $request)
    {
        /*
         * Valida o campo de e-mail.
         */
        $this->validateEmail($request);

        /*
         * Localiza o usuário pelo e-mail.
         */
        $user = User::where('email', $request->email)->first();

        /*
         * Evita informar se o e-mail existe no sistema.
         */
        if (!$user) {
            return redirect()
                ->back()
                ->with(
                    'status',
                    'Caso o e-mail esteja cadastrado, você receberá as instruções para redefinir sua senha.'
                );
        }

        /*
         * Bloqueia recuperação de senha para usuários
         * com status 2 ou 6.
         */
        if (in_array((int) $user->status, [2, 6], true)) {
            return redirect()
                ->route('login')
                ->with(
                    'erro',
                    'Favor entrar em contato com a Administração!'
                );
        }

        /*
         * Solicita o envio do link de redefinição.
         */
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        /*
         * Retorna a resposta do Laravel.
         */
        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }
}