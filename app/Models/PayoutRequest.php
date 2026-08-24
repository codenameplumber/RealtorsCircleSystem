<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutRequest extends Model
{
    protected $table = 'payout_requests';

    protected $guarded = [];

    protected $casts = [
        'amount'       => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function realtor(): BelongsTo
    {
        return $this->belongsTo(UsersTable::class, 'realtor_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AgentsTable::class, 'agent_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(AgencyPartner::class, 'agency_id');
    }

    public function getRequesterNameAttribute(): string
    {
        if ($this->realtor_id && $this->realtor) {
            return trim("{$this->realtor->First_name} {$this->realtor->Last_name}");
        }
        if ($this->agent_id && $this->agent) {
            return trim("{$this->agent->First_name} {$this->agent->Last_name}");
        }
        if ($this->agency_id && $this->agency) {
            return $this->agency->company_name;
        }
        return '—';
    }

    public function getRequesterTypeAttribute(): string
    {
        if ($this->agency_id) return 'Agency';
        return $this->realtor_id ? 'Realtor' : 'Agent';
    }
}
