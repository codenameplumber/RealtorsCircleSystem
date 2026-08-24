<?php

namespace App\Mail\Agent;

use App\Models\UsersTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RealtorApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public UsersTable $realtor) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Account Approved — Realtors Circle');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agent.realtor-approved',
            with: ['realtor' => $this->realtor],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
