<?php

namespace App\Mail\Agent;

use App\Models\CommissionTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DealLoggedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CommissionTable $commission,
        public string $recipientName,
        public string $role, // 'listing' | 'referring'
        public float $shareAmount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Deal Logged — Commission Recorded — Realtors Circle');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agent.deal-logged',
            with: [
                'commission'    => $this->commission,
                'recipientName' => $this->recipientName,
                'role'          => $this->role,
                'shareAmount'   => $this->shareAmount,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
