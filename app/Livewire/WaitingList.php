<?php

namespace App\Livewire;

use Livewire\Component;
use App\Livewire\Concerns\ThrottlesAttempts;
use App\Models\UserwaitList;
use App\Support\EmailThrottle;
use App\Mail\WaitListEmail;
use Illuminate\Support\Facades\Mail;

class WaitingList extends Component
{
    use ThrottlesAttempts;

    public $full_name, $phone_number, $email, $home_address, $years_experience;
    public $specializations;
    public $other_specialization;

    protected $rules = [
        'full_name' => 'required|string|max:255',
        'phone_number' => 'required|string|max:20',
        'email' => 'required|email|unique:waitlist_users,email',
        'home_address' => 'required|string|max:500',
        'years_experience' => 'required|integer|min:0|max:50',
        'specializations' => 'required|not_in:0',
    ];

    public function submit()
    {
        #dd("Hello");
        $validated = $this->validate();

        if (! $this->passesRateLimit('waitlist', $this->email, 5, 3600, 'email', 10)) {
            return;
        }

     
        #Send email
        try {

                $user = UserwaitList::create($validated);

                if (EmailThrottle::allows($this->email, 'waitlist')) {
                    Mail::to($this->email)->send(new WaitListEmail($user));
                }

                 #Reset form
                $this->reset();

                #Emit event to show success modal (frontend)
                $this->dispatch('waitlist-success');

        } 
        catch (\Exception $e) {
            
            session()->flash('error', 'Form saved, but email could not be sent.');
        }

       
    }

    public function render()
    {
        return view('livewire.waiting-list');
    }
}
