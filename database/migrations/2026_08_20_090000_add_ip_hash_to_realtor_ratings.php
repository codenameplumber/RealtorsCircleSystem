<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ratings previously deduplicated on the session id alone, which a visitor
 * defeats by clearing cookies. Store a salted hash of the client IP alongside
 * it so repeat votes are caught across sessions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('realtor_ratings', function (Blueprint $table) {
            $table->string('ip_hash', 64)->nullable()->after('session_token');
            $table->index(['realtor_id', 'ip_hash']);
        });
    }

    public function down(): void
    {
        Schema::table('realtor_ratings', function (Blueprint $table) {
            $table->dropIndex(['realtor_id', 'ip_hash']);
            $table->dropColumn('ip_hash');
        });
    }
};
