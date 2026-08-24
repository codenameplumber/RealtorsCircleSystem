<?php

namespace App\Mail\Agent;

use App\Models\AgentsTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgentPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AgentsTable $agent,
        public string $resetUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reset Your Password — Realtors Circle Agent Portal');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agent.password-reset',
            with: ['agent' => $this->agent, 'resetUrl' => $this->resetUrl],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
