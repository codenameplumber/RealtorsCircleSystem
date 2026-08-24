<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users_tables', function (Blueprint $table) {
            // KYC document fields
            $table->string('kyc_front_url')->nullable()->after('Country');
            $table->string('kyc_back_url')->nullable()->after('kyc_front_url');
            $table->enum('kyc_status', ['not_submitted', 'pending', 'approved', 'rejected'])
                  ->default('not_submitted')->after('kyc_back_url');
            $table->string('kyc_id_type')->nullable()->after('kyc_status');

            // Nigerian location fields
            $table->string('Lga')->nullable()->after('State');
        });
    }

    public function down(): void
    {
        Schema::table('users_tables', function (Blueprint $table) {
            $table->dropColumn(['kyc_front_url', 'kyc_back_url', 'kyc_status', 'kyc_id_type', 'Lga']);
        });
    }
};
