<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealtorRating extends Model
{
    public $timestamps  = false;
    protected $table    = 'realtor_ratings';
    protected $fillable = ['realtor_id', 'rating', 'session_token', 'ip_hash'];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    // Never expose the dedupe fingerprints — they are internal anti-abuse
    // state, not part of a rating's public shape.
    protected $hidden = ['session_token', 'ip_hash'];

    public function realtor()
    {
        return $this->belongsTo(UsersTable::class, 'realtor_id');
    }
}
