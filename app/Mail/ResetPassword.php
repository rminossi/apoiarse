<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->replyTo('contato@apoiar-se.online', 'Contato')
                    ->to($this->user->email, $this->user->name)
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject("Apoiar-se Online - Redefinir senha")
                    ->markdown('email.reset-password', [
                        'name' => $this->user->name,
                        'url' => route('sessao.resetPasswordForm', ['token' => $this->user->password_reset_token]),
                    ]);
    }
}
