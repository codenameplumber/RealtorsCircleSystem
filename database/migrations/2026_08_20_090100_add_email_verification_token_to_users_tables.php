<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The API registration flow issued a random verification token but never
 * stored it, and the verify endpoint matched on the ?email= query string
 * alone — so any address could be verified by anyone. Give the token somewhere
 * to live so it can actually be checked.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users_tables', function (Blueprint $table) {
            $table->string('email_verification_token', 64)->nullable()->after('email_verified_at');
            $table->timestamp('email_verification_expires_at')->nullable()->after('email_verification_token');
        });
    }

    public function down(): void
    {
        Schema::table('users_tables', function (Blueprint $table) {
            $table->dropColumn(['email_verification_token', 'email_verification_expires_at']);
        });
    }
};
