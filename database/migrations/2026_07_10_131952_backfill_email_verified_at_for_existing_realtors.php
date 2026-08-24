<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Grandfather in realtors who registered before the email-verification
     * gate existed — only newly registered accounts should be held to it.
     */
    public function up(): void
    {
        DB::table('users_tables')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        // Not reversible — we don't know which rows were backfilled vs. genuinely verified.
    }
};
