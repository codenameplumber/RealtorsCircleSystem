<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $table = 'commission_settings';
    protected $guarded = [];

    /**
     * Singleton accessor — the platform has exactly one active split configuration.
     */
    public static function current(): self
    {
        $settings = static::query()->firstOrCreate([]);

        // On first-ever creation, Eloquent doesn't pull back the DB-level column
        // defaults (m1_listing_pct, etc.) into the in-memory model — refresh once
        // so callers don't see null percentages on a fresh install.
        if ($settings->wasRecentlyCreated) {
            $settings->refresh();
        }

        return $settings;
    }
}
