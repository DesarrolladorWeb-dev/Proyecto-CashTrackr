<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    use Queueable;

   
// y nos aparece en que via vas a enviar tu notificacion
// y viene aqui mail,tambien puedes colocar que se envie una notificacion a la base de datos :
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        //esto crea un url temporal
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify', // para que el boton de "Confirmar Cuenta" lo envie a "verification.verify"
            // y va a vencer en cinco minutos (tiene 1h para verif su cuenta)
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(), //le enviamos el token (estaran los datos para identificar al usuario)
                'hash' => sha1($notifiable->getEmailForVerification()), //tambien el email para la verificacion
            ]
        );
        return (new MailMessage) //utilizara MailMessage para enviar el email - usara las variables de entorno
            ->subject('Confirma tu cuenta en CashTrackr')
            ->greeting('!Hola¡')
            ->line('Gracias por registrarte en CashTrackr, tu cuenta ya esta lista solo debes confirmarla')
            ->action('Confirmar Cuenta',$verificationUrl)
            ->line('Si no creaste esta cuenta, puedes ignorar este mensaje')
            ->salutation('Saludos','CashTrackr');
    }

   
}
