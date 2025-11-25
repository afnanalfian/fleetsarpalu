<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $body;
    public $link;

    public function __construct($title, $body, $link)
    {
        $this->title = $title;
        $this->body = $body;
        $this->link = $link;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->withSwiftMessage(function ($message) {
                $headers = $message->getHeaders();
                // MATIKAN tracking Brevo
                $headers->addTextHeader('X-Mailin-Track', '0');
                $headers->addTextHeader('X-Mailin-Track-Click', '0');
                $headers->addTextHeader('X-Mailin-Track-Open', '0');
            })
            ->view('emails.user-notification');
    }
}
