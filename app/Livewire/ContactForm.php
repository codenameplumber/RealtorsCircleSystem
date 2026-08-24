<?php

namespace App\Livewire;

use App\Livewire\Concerns\ThrottlesAttempts;
use App\Models\ContactMessage;
use Livewire\Component;

class ContactForm extends Component
{
    use ThrottlesAttempts;

    public string $name    = '';
    public string $email   = '';
    public string $phone   = '';
    public string $subject = '';
    public string $body    = '';   // renamed from $message to avoid Blade @error conflict
    public bool   $sent    = false;

    protected array $rules = [
        'name'    => 'required|string|max:100',
        'email'   => 'required|email',
        'phone'   => 'nullable|string|max:20',
        'subject' => 'required|string|max:150',
        'body'    => 'required|string|min:20|max:2000',
    ];

    protected array $messages = [
        'body.required' => 'Please write a message.',
        'body.min'      => 'Message must be at least 20 characters.',
    ];

    public function send()
    {
        $this->validate();

        // Unauthenticated and writes a row per submission - throttle so the
        // admin inbox cannot be flooded from a script.
        if (! $this->passesRateLimit('contact-form', $this->email, 5, 3600, 'email', 15)) {
            return;
        }

        ContactMessage::create([
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'subject' => $this->subject,
            'message' => $this->body,
        ]);

        $this->reset(['name', 'email', 'phone', 'subject', 'body']);
        $this->sent = true;
    }

    public function resetForm()
    {
        $this->sent = false;
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
