<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    public $timestamps  = false;
    protected $table    = 'admin_audit_logs';
    protected $fillable = ['admin_id', 'admin_name', 'action', 'subject_type', 'subject_id', 'description', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function record(string $action, ?string $subjectType = null, ?int $subjectId = null, ?string $description = null): self
    {
        return static::create([
            'admin_id'     => session('admin_id'),
            'admin_name'   => session('admin_name'),
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'description'  => $description,
            'created_at'   => now(),
        ]);
    }
}
