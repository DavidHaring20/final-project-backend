<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        return $this->from('95f1301a34-4c4098@inbox.mailtrap.io', 'Mailtrap')
                    ->view('email', ['passcode' => $this->passcode]);
    }
}
