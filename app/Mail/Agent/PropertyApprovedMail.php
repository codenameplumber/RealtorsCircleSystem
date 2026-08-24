<?php

namespace App\Mail\Agent;

use App\Models\PropertiesTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PropertiesTable $property) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Property Listing Approved — Realtors Circle');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agent.property-approved',
            with: ['property' => $this->property],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
