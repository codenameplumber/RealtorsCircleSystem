<?php

namespace App\Mail\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\UsersTable; // Import your User model

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;
    public $subject;
    public $bodyMessage;

    /**
     * Create a new message instance.
     *
     * @param UsersTable $user
     * @param string $resetUrl The URL with the token for password reset.
     * @param string $subjectLine The subject for the email.
     * @param string $bodyMessage The main body content for the email.
     */
    public function __construct(UsersTable $user, string $resetUrl, string $subject, string $bodyMessage)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
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
            view: 'mail.users.password-reset-mail',
            with: [
                'user'        => $this->user,
                'resetUrl'    => $this->resetUrl,
                'bodyMessage' => $this->bodyMessage,
            ],
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