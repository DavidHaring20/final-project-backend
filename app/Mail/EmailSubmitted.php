<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    protected $passcode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($passcode)
    {
        $this->passcode = $passcode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME'])
                    ->view('email', ['passcode' => $this->passcode]);
    }
}
