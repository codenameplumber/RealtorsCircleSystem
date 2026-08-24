<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UsersTable extends \Illuminate\Foundation\Auth\User implements CanResetPassword
{
    use HasFactory, Notifiable, CanResetPasswordTrait;

    protected $table = 'users_tables';

    /**
     * Columns that must never be set from request input. Everything else stays
     * mass-assignable because the registration paths legitimately write most of
     * this table in one create() call — but these decide who you are and what
     * you may do, so they are set explicitly or not at all.
     */
    protected $guarded = [
        'id',
        'api_token',
        'remember_token',
        'email_verified_at',
        'email_verification_token',
        'email_verification_expires_at',
    ];

    /**
     * Hidden from JSON output.
     *
     * NOTE: the column is `Password` with a capital P (see the
     * create_users_tables_table migration). A lowercase 'password' entry here
     * matches nothing, which is how the bcrypt hash was previously serialised
     * into API login responses. Both spellings are listed so a rename in
     * either direction cannot silently re-expose it.
     */
    protected $hidden = [
        'Password',
        'password',
        'api_token', // Hide the hashed token
        'remember_token', // If you add this for standard Laravel auth features
        'email_verification_token',
    ];

    protected $casts = [
        'email_verified_at'             => 'datetime',
        'email_verification_expires_at' => 'datetime',
    ];

    public function agency()
    {
        return $this->belongsTo(AgencyPartner::class, 'agency_id');
    }

    public function bankInformation()
    {
        return $this->hasOne(BankInformation::class, 'user_id', 'id');
    }

    public function properties()
    {
        return $this->hasMany(PropertiesTable::class, 'user_id', 'id');
    }

    public function ratings()
    {
        return $this->hasMany(RealtorRating::class, 'realtor_id', 'id');
    }

    public function listingCommissions()
    {
        return $this->hasMany(CommissionTable::class, 'listing_realtor_id');
    }

    public function referringCommissions()
    {
        return $this->hasMany(CommissionTable::class, 'referring_realtor_id');
    }

    // -- KYC documents --------------------------------------------------------

    /*
     * KYC scans are government ID documents. They used to be written to S3 as
     * public objects and rendered straight from their permanent URL, which made
     * every realtor's ID readable by anyone who had (or guessed) the link.
     * The disk is now private and these accessors mint a short-lived presigned
     * URL at render time instead.
     *
     * Both accessors tolerate the legacy rows that still hold a full public URL
     * as well as the bare object keys written from now on.
     */

    public function getKycFrontLinkAttribute(): ?string
    {
        return $this->kycTemporaryUrl($this->kyc_front_url);
    }

    public function getKycBackLinkAttribute(): ?string
    {
        return $this->kycTemporaryUrl($this->kyc_back_url);
    }

    protected function kycTemporaryUrl(?string $stored, int $minutes = 15): ?string
    {
        $key = $this->kycObjectKey($stored);

        if (! $key) {
            return null;
        }

        try {
            return Storage::disk('kycaws')->temporaryUrl($key, now()->addMinutes($minutes));
        } catch (\Throwable $e) {
            Log::error("Could not sign KYC document for user #{$this->id}: " . $e->getMessage());

            return null;
        }
    }

    /**
     * Reduce whatever is stored to an object key relative to the disk root.
     */
    protected function kycObjectKey(?string $stored): ?string
    {
        $stored = trim((string) $stored);

        if ($stored === '') {
            return null;
        }

        // Written since the disk went private: already a bare key.
        if (! Str::startsWith($stored, ['http://', 'https://'])) {
            return ltrim($stored, '/') ?: null;
        }

        $path = ltrim((string) parse_url($stored, PHP_URL_PATH), '/');

        // Path-style endpoints put the bucket in the path; virtual-host style
        // does not. Strip it only when present.
        $bucket = (string) config('filesystems.disks.kycaws.bucket');
        if ($bucket !== '' && Str::startsWith($path, $bucket . '/')) {
            $path = Str::after($path, $bucket . '/');
        }

        // The disk root is prepended again by the driver, so remove it here.
        $root = trim((string) config('filesystems.disks.kycaws.root', ''), '/');
        if ($root !== '' && Str::startsWith($path, $root . '/')) {
            $path = Str::after($path, $root . '/');
        }

        return $path !== '' ? $path : null;
    }

    // -- Email verification --------------------------------------------------

    /**
     * Issue a single-use verification token, returning the plaintext to embed
     * in the emailed link. Only the hash is persisted, so a leaked database
     * snapshot does not hand out verified accounts.
     */
    public function generateEmailVerificationToken(int $ttlHours = 48): string
    {
        $plain = Str::random(64);

        $this->forceFill([
            'email_verification_token'      => Hash::make($plain),
            'email_verification_expires_at' => now()->addHours($ttlHours),
        ])->save();

        return $plain;
    }

    public function isValidEmailVerificationToken(?string $plain): bool
    {
        if (! $plain || ! $this->email_verification_token || ! $this->email_verification_expires_at) {
            return false;
        }

        if ($this->email_verification_expires_at->isPast()) {
            return false;
        }

        return Hash::check($plain, $this->email_verification_token);
    }

    /**
     * Mark verified and burn the token so the link cannot be replayed.
     */
    public function markEmailVerified(): void
    {
        $this->forceFill([
            'email_verified_at'             => now(),
            'email_verification_token'      => null,
            'email_verification_expires_at' => null,
        ])->save();
    }

    // Route mail notifications to the correct email column
    public function routeNotificationForMail($notification = null): string
    {
        return $this->Email;
    }

    // Required by CanResetPassword — use Email column
    public function getEmailForPasswordReset(): string
    {
        return $this->Email;
    }

    /**
     * Auth identifier for session login.
     *
     * Read from the raw attribute array and accept either spelling. The
     * create_users_tables_table migration declares `Password`, but the live
     * database was built from an older dump whose column is lowercase
     * `password`. MySQL resolves column names case-insensitively on write, so
     * both spellings persist fine - but a result set comes back keyed by the
     * column's *declared* case, so `$this->Password` is null on one of the two
     * schemas. That made this method throw a TypeError and took Auth::attempt
     * (the whole API login) down with it.
     */
    public function getAuthPassword(): string
    {
        $attributes = $this->getAttributes();

        return (string) ($attributes['Password'] ?? $attributes['password'] ?? '');
    }

    // Route the password reset through our custom mailable instead of the default Laravel notification
    public function sendPasswordResetNotification($token): void
    {
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => encrypt($this->getEmailForPasswordReset()),
        ], false));

        \Illuminate\Support\Facades\Mail::to($this->Email)->send(
            new \App\Mail\Users\PasswordResetMail(
                $this,
                $resetUrl,
                'Reset Your Password — Realtors Circle',
                'You are receiving this email because we received a password reset request for your Realtors Circle account.'
            )
        );
    }
}