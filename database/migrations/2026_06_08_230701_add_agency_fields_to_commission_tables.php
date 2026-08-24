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
        Schema::table('commission_tables', function (Blueprint $table) {
            $table->unsignedBigInteger('agency_id')->nullable()->after('agent_id');
            $table->decimal('agency_override_amount', 15, 2)->default(0)->after('agency_id');
            $table->foreign('agency_id')->references('id')->on('agency_partners')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('commission_tables', function (Blueprint $table) {
            $table->dropForeign(['agency_id']);
            $table->dropColumn(['agency_id', 'agency_override_amount']);
        });
    }
};
