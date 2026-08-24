<?php

namespace App\Mail\Agent;

use App\Models\PropertiesTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PropertiesTable $property,
        public string $reason,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Property Listing Update — Realtors Circle');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agent.property-rejected',
            with: ['property' => $this->property, 'reason' => $this->reason],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
