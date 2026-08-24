<?php

namespace App\Livewire;

use App\Livewire\Concerns\ThrottlesAttempts;
use App\Mail\BookingAcknowledgementMail;
use App\Mail\PropertyInquiryNotification;
use App\Models\PropertiesTable;
use App\Models\PropertyInquiry;
use App\Support\EmailThrottle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class PropertyBooking extends Component
{
    use ThrottlesAttempts;

    public int    $propertyId;
    public string $preferred_date  = '';
    public string $preferred_time  = '';
    public string $visit_type      = 'in_person';
    public string $message         = '';
    public bool   $submitted       = false;
    public bool   $fee_acknowledged = false;

    // Client info — pre-filled if logged in, otherwise entered manually
    public string $client_name     = '';
    public string $client_email    = '';
    public string $client_phone    = '';
    public string $client_whatsapp = '';
    public bool   $is_logged_in    = false;

    public function mount(int $propertyId): void
    {
        $this->propertyId     = $propertyId;
        $this->preferred_date = now()->addDay()->format('Y-m-d');

        if (auth()->check()) {
            $user                  = auth()->user();
            $this->is_logged_in    = true;
            $this->client_name     = trim($user->First_name . ' ' . $user->Last_name);
            $this->client_email    = $user->Email ?? '';
            $this->client_phone    = $user->Phone ?? '';
        }
    }

    protected function rules(): array
    {
        $guestRules = $this->is_logged_in ? [] : [
            'client_name'     => 'required|string|max:100',
            'client_email'    => 'required|email|max:150',
            'client_phone'    => 'required|string|max:20',
            'client_whatsapp' => 'nullable|string|max:20',
        ];

        return array_merge($guestRules, [
            'preferred_date'   => 'required|date|after:today',
            'preferred_time'   => 'required|string',
            'visit_type'       => 'required|in:in_person,virtual',
            'message'          => 'nullable|string|max:500',
            'fee_acknowledged' => 'accepted',
        ]);
    }

    protected array $messages = [
        'preferred_date.after'      => 'Please choose a future date.',
        'fee_acknowledged.accepted' => 'You must acknowledge the ₦10,000 inspection fee to proceed.',
        'client_name.required'      => 'Your name is required.',
        'client_email.required'     => 'Your email address is required.',
        'client_phone.required'     => 'Your phone number is required.',
    ];

    public function book(): void
    {
        $this->validate();

        // A guest booking mails both the realtor and the address typed into
        // the form, so an unthrottled submit is two emails per request to an
        // attacker-chosen recipient.
        if (! $this->passesRateLimit('property-booking', $this->client_email, 5, 3600, 'client_email', 15)) {
            return;
        }

        $inquiry = PropertyInquiry::create([
            'property_id'                 => $this->propertyId,
            'booker_user_id'              => auth()->check() ? auth()->id() : null,
            'name'                        => $this->client_name,
            'email'                       => $this->client_email,
            'phone'                       => $this->client_phone,
            'whatsapp'                    => $this->client_whatsapp ?: null,
            'preferred_date'              => $this->preferred_date,
            'preferred_time'              => $this->preferred_time,
            'visit_type'                  => $this->visit_type,
            'message'                     => $this->message ?: null,
            'inspection_fee_acknowledged' => true,
            'status'                      => 'pending',
        ]);

        try {
            $property = PropertiesTable::with('user')->find($this->propertyId);

            // Notify listing realtor (name only — no contact details)
            if ($property && $property->user?->Email) {
                Mail::to($property->user->Email)->send(
                    new PropertyInquiryNotification(
                        property:      $property,
                        preferredDate: $this->preferred_date,
                        preferredTime: $this->preferred_time,
                        visitType:     $this->visit_type,
                    )
                );
            }

            // Acknowledge to the booker. The address is whatever the guest
            // typed, so it gets a recipient budget of its own.
            if ($this->client_email && $property && EmailThrottle::allows($this->client_email, 'booking')) {
                Mail::to($this->client_email)->send(
                    new BookingAcknowledgementMail(
                        property:      $property,
                        bookerName:    $this->client_name,
                        preferredDate: $this->preferred_date,
                        preferredTime: $this->preferred_time,
                        visitType:     $this->visit_type,
                    )
                );
            }
        } catch (\Throwable $e) {
            Log::error('PropertyBooking mail failed: ' . $e->getMessage());
        }

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.property-booking');
    }
}
