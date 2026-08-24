<?php

namespace App\Mail;

use App\Models\PropertiesTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingAcknowledgementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PropertiesTable $property,
        public string $bookerName,
        public string $preferredDate,
        public string $preferredTime,
        public string $visitType,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Viewing Request Received — Realtors Circle',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.booking-acknowledgement',
            with: [
                'property'      => $this->property,
                'bookerName'    => $this->bookerName,
                'preferredDate' => $this->preferredDate,
                'preferredTime' => $this->preferredTime,
                'visitType'     => $this->visitType,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
