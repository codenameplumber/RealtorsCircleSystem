<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('commission_settings', function (Blueprint $table) {
            // Percentage of the platform fee routed to the agency as acquisition override
            $table->unsignedTinyInteger('agency_override_pct')->default(20)->after('m2_platform_pct');
        });
    }

    public function down(): void
    {
        Schema::table('commission_settings', function (Blueprint $table) {
            $table->dropColumn('agency_override_pct');
        });
    }
};
