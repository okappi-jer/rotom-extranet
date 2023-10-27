<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;


class SendCsv extends Mailable
{
    use Queueable, SerializesModels;

    public $csv;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($csv)
    {
        $this->csv = $csv;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $file = public_path('/partijreg/exports/' . $this->csv . '.csv');

        return $this
            ->subject('Extranet: Nieuwe Levering aangemeld.')
            ->attach($file)
            ->markdown('Email.newCsv');
    }
}
