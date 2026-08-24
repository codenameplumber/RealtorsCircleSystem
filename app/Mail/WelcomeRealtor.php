<?php

namespace App\Mail;

use App\Models\UsersTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeRealtor extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public UsersTable $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Realtors Circle — Account Under Review',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.welcome-realtor',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
