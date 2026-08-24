<?php

namespace App\Mail\Admin;

use App\Models\AgentsTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeAgentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AgentsTable $agent,
        public string      $plainPassword,
        public string      $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Realtors Circle — Your Agent Account is Ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.admin.welcome-agent',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
