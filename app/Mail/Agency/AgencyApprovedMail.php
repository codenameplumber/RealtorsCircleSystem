<?php

namespace App\Mail\Agency;

use App\Models\AgencyPartner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgencyApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AgencyPartner $agency) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Agency Partner Account Approved — Realtors Circle');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agency.agency-approved',
            with: ['agency' => $this->agency],
        );
    }

    public function attachments(): array { return []; }
}
