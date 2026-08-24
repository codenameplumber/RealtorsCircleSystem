<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commission_tables', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('listing_realtor_id');
            $table->unsignedBigInteger('referring_realtor_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->decimal('listing_realtor_amount', 15, 2);
            $table->decimal('referring_realtor_amount', 15, 2)->default(0);
            $table->decimal('agent_fee', 15, 2)->default(0);
            $table->decimal('platform_fee', 15, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'paid', 'disputed'])->default('pending');
            $table->string('deal_reference')->unique()->nullable();
            $table->date('deal_date')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('property_id')->references('id')->on('properties_tables')->onDelete('cascade');
            $table->foreign('listing_realtor_id')->references('id')->on('users_tables')->onDelete('cascade');
            $table->foreign('referring_realtor_id')->references('id')->on('users_tables')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('commission_tables', function (Blueprint $table) {
            $table->dropForeign(['property_id', 'listing_realtor_id', 'referring_realtor_id']);
            $table->dropColumn(['property_id', 'listing_realtor_id', 'referring_realtor_id', 'agent_id', 'total_amount', 'listing_realtor_amount', 'referring_realtor_amount', 'agent_fee', 'platform_fee', 'status', 'deal_reference', 'deal_date', 'notes']);
        });
    }
};
