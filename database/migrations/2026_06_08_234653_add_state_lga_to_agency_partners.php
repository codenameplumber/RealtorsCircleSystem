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
        Schema::table('agency_partners', function (Blueprint $table) {
            $table->string('state')->nullable()->after('phone');
            $table->string('lga')->nullable()->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('agency_partners', function (Blueprint $table) {
            $table->dropColumn(['state', 'lga']);
        });
    }
};
