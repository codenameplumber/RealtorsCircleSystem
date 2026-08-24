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
        Schema::create('commission_settings', function (Blueprint $table) {
            $table->id();
            // Model 1 — Standard deal (listing realtor + referring realtor + agent)
            $table->unsignedTinyInteger('m1_listing_pct')->default(40);
            $table->unsignedTinyInteger('m1_referring_pct')->default(40);
            $table->unsignedTinyInteger('m1_agent_pct')->default(10);
            $table->unsignedTinyInteger('m1_platform_pct')->default(10);
            // Model 2 — Direct/self-sourced deal (listing realtor + agent only)
            $table->unsignedTinyInteger('m2_listing_pct')->default(60);
            $table->unsignedTinyInteger('m2_agent_pct')->default(20);
            $table->unsignedTinyInteger('m2_platform_pct')->default(20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_settings');
    }
};
