<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertiesTable extends Model
{
    //
    use HasFactory,SoftDeletes; 

    protected $table = 'properties_tables';

    protected $guarded = [];

    protected $casts = [
        'amenities' => 'array',
        'photos' => 'array',
        'videos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(UsersTable::class, 'user_id', 'id');
    }

    /**
     * Returns price ready for display.
     * Handles both raw numbers (45000000) and already-formatted strings ("45,000,000/yr").
     */
    public function getPriceDisplayAttribute(): string
    {
        $raw = (string) ($this->attributes['price'] ?? '');

        // Already formatted or has a suffix — return as-is
        if (str_contains($raw, ',') || str_contains($raw, '/')) {
            return $raw;
        }

        // Raw numeric string
        if (is_numeric($raw)) {
            return number_format((float) $raw);
        }

        return $raw;
    }
}
