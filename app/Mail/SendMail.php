<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $delivers_to;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $delivers_to)
    {
        $this->token = $token;
        $this->delivers_to = $delivers_to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(strtolower($this->delivers_to) == 'orca' ){
            return $this
                ->subject('Wijzig je wachtwoord')
                ->markdown('Email.resetPasswordOrca')->with([
                    'token' => $this->token,
                    'delivers_to' => $this->delivers_to,
                ]);
        }else{
            return $this
                ->subject('Wijzig je wachtwoord')
                ->markdown('Email.resetPassword')->with([
                    'token' => $this->token,
                    'delivers_to' => $this->delivers_to,
                ]);
        }

    }
}
