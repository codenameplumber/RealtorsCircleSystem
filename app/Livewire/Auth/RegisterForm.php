<?php

namespace App\Livewire\Auth;

use App\Livewire\Concerns\ThrottlesAttempts;
use App\Mail\Users\EmailVerificationMail;
use App\Mail\WelcomeRealtor;
use App\Models\AgencyPartner;
use App\Models\UsersTable;
use App\Support\EmailThrottle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Component;

class RegisterForm extends Component
{
    use ThrottlesAttempts;

    public string  $first_name            = '';
    public string  $last_name             = '';
    public string  $email                 = '';
    public string  $phone                 = '';
    public string  $password              = '';
    public string  $password_confirmation = '';
    public string  $specialization        = '';
    public string  $years_experience      = '';
    public bool    $terms_agreed          = false;
    public ?int    $agencyId              = null;
    public ?string $agencyName            = null;

    protected array $rules = [
        'first_name'            => 'required|string|max:100',
        'last_name'             => 'required|string|max:100',
        'email'                 => 'required|email|unique:users_tables,Email',
        'phone'                 => 'required|string|min:10|max:15|unique:users_tables,Phone',
        'password'              => 'required|min:8|confirmed',
        'password_confirmation' => 'required',
        'specialization'        => 'required|string',
        'years_experience'      => 'required|string',
        'terms_agreed'          => 'accepted',
    ];

    protected array $messages = [
        'email.unique'          => 'This email address is already registered.',
        'phone.unique'          => 'This phone number is already registered.',
        'password.confirmed'    => 'Passwords do not match.',
        'password.min'          => 'Password must be at least 8 characters.',
        'terms_agreed.accepted' => 'You must agree to the Terms of Service and Privacy Policy.',
    ];

    public function mount(): void
    {
        $ref = request()->query('ref');
        if ($ref) {
            $agency = AgencyPartner::where('referral_code', $ref)->where('status', 'active')->first();
            if ($agency) {
                $this->agencyId   = $agency->id;
                $this->agencyName = $agency->company_name;
            }
        }
    }

    public function register()
    {
        $this->validate();

        // Bounds automated signup floods, which would otherwise both fill the
        // approvals queue and send unbounded mail.
        if (! $this->passesRateLimit('realtor-register', $this->email, 5, 3600, 'email', 10)) {
            return;
        }

        $user = UsersTable::create([
            'First_name'     => $this->first_name,
            'Last_name'      => $this->last_name,
            'Email'          => $this->email,
            'Phone'          => $this->phone,
            'Password'       => Hash::make($this->password),
            'Specialization' => $this->specialization,
            'Yrs_experience' => $this->years_experience,
            'Status'         => 'pending',
            '2fa'            => 'disabled',
            'steps_signup'   => 'complete',
            'agency_id'      => $this->agencyId,
        ]);

        // api_token is guarded against mass assignment - set it explicitly.
        $user->forceFill(['api_token' => hash('sha256', Str::random(60))])->save();

        // Send welcome email
        try {
            if (EmailThrottle::allows($user->Email, 'welcome')) {
                Mail::to($user->Email)->send(new WelcomeRealtor($user));
            }
        } catch (\Throwable $e) {
            // Log silently — don't block registration if mail fails
            logger()->error('Welcome email failed: ' . $e->getMessage());
        }

        // Send verification email — required before the account can sign in
        try {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addHours(48),
                ['id' => $user->id, 'hash' => sha1($user->Email)]
            );

            if (EmailThrottle::allows($user->Email, 'verification')) {
                Mail::to($user->Email)->send(new EmailVerificationMail($user, $verificationUrl));
            }
        } catch (\Throwable $e) {
            logger()->error('Verification email failed: ' . $e->getMessage());
        }

        session()->flash('success', 'Account created! Check your email to verify your address — you\'ll need to confirm it before signing in. You\'ll also be notified once an admin approves your account.');

        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register-form');
    }
}
