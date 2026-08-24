<?php

namespace App\Mail\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;
    public $subject;
    public $bodyMessage; // Optional custom message for different stages

    /**
     * Create a new message instance.
     */
    public function __construct($user, $verificationUrl = null, $subject = 'Email Verification', $bodyMessage = null)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
        $this->subject = $subject;
        $this->bodyMessage = $bodyMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.users.email-verification-mail',
            with: [
                'user'            => $this->user,
                'verificationUrl' => $this->verificationUrl,
                'bodyMessage'     => $this->bodyMessage,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

