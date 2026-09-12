<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrimeiroAcesso extends Notification
{
    use Queueable;

    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $expire = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Primeiro acesso ao Portal GEOFML')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Sua solicitação de acesso ao Portal GEOFML foi recebida.')
            ->line('Para continuar, defina agora a sua senha de primeiro acesso.')
            ->line('Seu login será o seu CPF cadastrado. Por segurança, nenhuma senha temporária é enviada por e-mail.')
            ->action('Definir senha de primeiro acesso', $url)
            ->line('Este link é pessoal e expira em ' . $expire . ' minutos.')
            ->line('Após definir a senha, acesse o Portal GEOFML e conclua o seu cadastro.')
            ->line('Se você não solicitou este acesso, desconsidere esta mensagem.')
            ->salutation("Atenciosamente,\nAdministração do Sistema GEOFML");
    }
}
