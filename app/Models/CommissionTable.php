<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionTable extends Model
{
    protected $table = 'commission_tables';
    /**
     * payment_token is the only credential on this row: whoever holds it can
     * open the payment page for this deal. It is minted explicitly, never from
     * request input.
     */
    protected $guarded = ['id', 'payment_token'];

    protected $casts = [
        'total_amount'             => 'decimal:2',
        'listing_realtor_amount'   => 'decimal:2',
        'referring_realtor_amount' => 'decimal:2',
        'agent_fee'                => 'decimal:2',
        'platform_fee'             => 'decimal:2',
        'agency_override_amount'   => 'decimal:2',
        'deal_date'                => 'date',
    ];

    public function property()         { return $this->belongsTo(PropertiesTable::class, 'property_id'); }
    public function listingRealtor()   { return $this->belongsTo(UsersTable::class, 'listing_realtor_id'); }
    public function referringRealtor() { return $this->belongsTo(UsersTable::class, 'referring_realtor_id'); }
    public function agent()            { return $this->belongsTo(AgentsTable::class, 'agent_id'); }
    public function agency()           { return $this->belongsTo(AgencyPartner::class, 'agency_id'); }

    /**
     * Model 1 always requires picking a referring realtor at logging time, so the
     * FK's presence reliably signals which split model produced this record —
     * no separate stored column needed.
     */
    public function getSplitModelAttribute(): int
    {
        return $this->referring_realtor_id ? 1 : 2;
    }

    public function getSplitModelLabelAttribute(): string
    {
        return $this->referring_realtor_id ? 'Model 1 — Standard' : 'Model 2 — Direct';
    }
}
