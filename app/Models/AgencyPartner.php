<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class AgencyPartner extends Authenticatable
{
    use Notifiable;

    protected $table = 'agency_partners';
    /**
     * Credential columns are never set from request input; the reset flow uses
     * forceFill.
     *
     * referral_code is deliberately NOT guarded: it is assigned at creation
     * time from generateReferralCode() and is a public attribution code, not a
     * secret.
     */
    protected $guarded = [
        'id',
        'password_reset_token',
        'password_reset_expires_at',
    ];
    protected $hidden = ['password', 'password_reset_token'];
    protected $casts = [
        'password_reset_expires_at' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function realtors()
    {
        return $this->hasMany(UsersTable::class, 'agency_id');
    }

    public function commissions()
    {
        return $this->hasMany(CommissionTable::class, 'agency_id');
    }

    public function payoutRequests()
    {
        return $this->hasMany(PayoutRequest::class, 'agency_id');
    }

    public static function generateReferralCode(): string
    {
        do {
            $code = 'AGY-' . strtoupper(Str::random(8));
        } while (static::where('referral_code', $code)->exists());

        return $code;
    }
}
