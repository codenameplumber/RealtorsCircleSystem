<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents_tables', function (Blueprint $table) {
            $table->string('State')->nullable()->after('Assigned_area');
            $table->string('password_reset_token')->nullable()->after('remember_token');
            $table->timestamp('password_reset_expires_at')->nullable()->after('password_reset_token');
            $table->string('bank_name')->nullable()->after('password_reset_expires_at');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->string('account_name')->nullable()->after('account_number');
        });
    }

    public function down(): void
    {
        Schema::table('agents_tables', function (Blueprint $table) {
            $table->dropColumn([
                'State',
                'password_reset_token',
                'password_reset_expires_at',
                'bank_name',
                'account_number',
                'account_name',
            ]);
        });
    }
};
