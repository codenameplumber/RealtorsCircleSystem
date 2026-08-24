<?php

namespace App\Mail;

use App\Models\PropertyInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PropertyInquiry $inquiry,
        public string $statusType, // 'confirmed' | 'rescheduled' | 'completed' | 'cancelled'
        public ?string $reason = null,
        public ?string $recipientName = null,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'confirmed'   => 'Inspection Confirmed — Realtors Circle',
            'rescheduled' => 'Inspection Rescheduled — Realtors Circle',
            'completed'   => 'Inspection Completed — Realtors Circle',
            'cancelled'   => 'Inspection Cancelled — Realtors Circle',
        ];

        return new Envelope(
            subject: $subjects[$this->statusType] ?? 'Appointment Update — Realtors Circle',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.appointment-status',
            with: [
                'inquiry'       => $this->inquiry,
                'statusType'    => $this->statusType,
                'reason'        => $this->reason,
                'recipientName' => $this->recipientName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
