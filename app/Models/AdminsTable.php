<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminsTable extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins_tables';
    /**
     * Credential and privilege columns are never set from request input; the
     * flows that legitimately write them use forceFill.
     */
    protected $guarded = [
        'id',
        'api_token',
        'Role',
        'remember_token',
        'password_reset_token',
        'password_reset_expires_at',
    ];
    protected $hidden  = ['Password', 'api_token', 'remember_token', 'password_reset_token'];

    protected $casts = [
        'password_reset_expires_at' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return $this->Password;
    }

    public function routeNotificationForMail($notification = null): string
    {
        return $this->Email;
    }

    public function generateResetToken(): string
    {
        $plain = Str::random(64);

        $this->forceFill([
            'password_reset_token'      => Hash::make($plain),
            'password_reset_expires_at' => now()->addHour(),
        ])->save();

        return $plain;
    }

    public function isValidResetToken(string $plain): bool
    {
        if (! $this->password_reset_token || ! $this->password_reset_expires_at) {
            return false;
        }

        if ($this->password_reset_expires_at->isPast()) {
            return false;
        }

        return Hash::check($plain, $this->password_reset_token);
    }

    public function clearResetToken(): void
    {
        $this->update([
            'password_reset_token'      => null,
            'password_reset_expires_at' => null,
        ]);
    }
}
