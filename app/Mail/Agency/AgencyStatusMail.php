<?php

namespace App\Mail\Agency;

use App\Models\AgencyPartner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgencyStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AgencyPartner $agency,
        public string $newStatus, // 'active' | 'suspended'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->newStatus === 'active'
            ? 'Your Agency Account Has Been Reactivated — Realtors Circle'
            : 'Your Agency Account Has Been Suspended — Realtors Circle';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agency.status-update',
            with: [
                'agency'    => $this->agency,
                'status'    => $this->newStatus,
                'loginUrl'  => url('/agency/login'),
            ],
        );
    }

    public function attachments(): array { return []; }
}
