<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyInquiry extends Model
{
    protected $table = 'property_inquiries';

    protected $fillable = [
        'property_id', 'booker_user_id',
        'name', 'email', 'phone', 'whatsapp',
        'preferred_date', 'preferred_time', 'visit_type', 'message', 'status',
        'inspection_fee_acknowledged',
        'reschedule_date', 'reschedule_time', 'cancel_reason',
    ];

    protected $casts = [
        'preferred_date'              => 'date',
        'reschedule_date'             => 'date',
        'inspection_fee_acknowledged' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(PropertiesTable::class, 'property_id');
    }

    public function booker()
    {
        return $this->belongsTo(UsersTable::class, 'booker_user_id');
    }
}
