<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->replyTo($this->data['reply_email'], $this->data['reply_email'])
                    ->to(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject("Contato - Site")
                    ->markdown('email.contact', [
                        'reply_name' => $this->data['reply_name'],
                        'reply_email' => $this->data['reply_email'],
                        'message' => $this->data['message']
                    ]);
    }
}
