<?php

namespace App\Mail\Agency;

use App\Models\AgencyPartner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgencyPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AgencyPartner $agency,
        public string $resetUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reset Your Password — Realtors Circle Agency Portal');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agency.password-reset',
            with: ['agency' => $this->agency, 'resetUrl' => $this->resetUrl],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
