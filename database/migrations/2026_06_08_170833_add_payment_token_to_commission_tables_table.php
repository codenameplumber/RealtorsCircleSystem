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
            $table->string('payment_token')->nullable()->unique()->after('deal_reference');
            $table->timestamp('payment_link_generated_at')->nullable()->after('payment_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commission_tables', function (Blueprint $table) {
            $table->dropColumn(['payment_token', 'payment_link_generated_at']);
        });
    }
};
