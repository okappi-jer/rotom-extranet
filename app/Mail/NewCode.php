<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCode extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $location;
    public $email;
    public $code;
    public $validtill;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $location, $code, $email, $validtill)
    {
        $this->name = $name;
        $this->location = $location;
        $this->code = $code;
        $this->email = $email;
        $this->validtill = $validtill;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Nieuwe toegangscode poort')
            ->markdown('Email.newCode')->with([
                'name' => $this->name,
                'location' => $this->location,
                'code' => $this->code,
                'email' => $this->email,
                'validtill' => $this->validtill
            ]);
    }
}
