<?php

namespace App\Mail\Admin;

use App\Models\AdminsTable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdminsTable $admin,
        public string $resetUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reset Your Password — Realtors Circle Admin Portal');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.admin.password-reset',
            with: ['admin' => $this->admin, 'resetUrl' => $this->resetUrl],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
