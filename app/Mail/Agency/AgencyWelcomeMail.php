<?php

namespace App\Mail\Agency;

use App\Models\AgencyPartner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgencyWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AgencyPartner $agency,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to Realtors Circle — Your Agency Partner Account is Ready');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.agency.welcome',
            with: [
                'agency'         => $this->agency,
                'plainPassword'  => $this->plainPassword,
                'loginUrl'       => url('/agency/login'),
                'resetUrl'       => url('/agency/forgot-password'),
            ],
        );
    }

    public function attachments(): array { return []; }
}
