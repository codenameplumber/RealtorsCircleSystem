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
        Schema::create('properties_tables', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('title');
            $table->string('type');
            $table->longText('description');
            $table->string('listing_type');
            $table->string('street');
            $table->string('neigborhood');
            $table->string('city');
            $table->string('state');
            $table->string('localarea');
            $table->string('country');
            $table->json('amenities');
            $table->string('price');
            $table->json('photos');
            $table->json('videos');
            $table->string('listing_status');
            $table->string('approval_status');
            $table->string('views');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties_tables');
    }
};
