<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExternalVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    public function __construct($user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        return $this->subject('Verifikasi Email Anda')
                    ->withSwiftMessage(function ($message) {
                        $headers = $message->getHeaders();
                        // matikan tracking
                        $headers->addTextHeader('X-Mailin-Track', '0');
                        $headers->addTextHeader('X-Mailin-Track-Click', '0');
                        $headers->addTextHeader('X-Mailin-Track-Open', '0');
                    })
                    ->view('emails.external.verify');
    }

}
