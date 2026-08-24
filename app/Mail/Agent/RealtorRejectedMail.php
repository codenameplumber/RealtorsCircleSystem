<?php

namespace App\Mail\Agent;

use App\Models\UsersTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RealtorRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public UsersTable $realtor,
        public string $reason,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Account Review Update — Realtors Circle');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agent.realtor-rejected',
            with: ['realtor' => $this->realtor, 'reason' => $this->reason],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
