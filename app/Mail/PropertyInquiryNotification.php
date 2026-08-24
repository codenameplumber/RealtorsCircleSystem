<?php

namespace App\Mail;

use App\Models\PropertiesTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyInquiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PropertiesTable $property,
        public string          $preferredDate,
        public string          $preferredTime,
        public string          $visitType,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🏠 New Viewing Request for Your Property — Realtors Circle',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.property-inquiry-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
