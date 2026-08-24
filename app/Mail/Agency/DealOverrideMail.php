<?php

namespace App\Mail\Agency;

use App\Models\AgencyPartner;
use App\Models\CommissionTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DealOverrideMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CommissionTable $commission,
        public AgencyPartner $agency,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Network Deal Closed — Override Earned — Realtors Circle');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agency.deal-override',
            with: [
                'commission' => $this->commission,
                'agency'     => $this->agency,
            ],
        );
    }

    public function attachments(): array { return []; }
}
