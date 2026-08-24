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
        Schema::create('waitlist_users', function (Blueprint $table) {
                $table->id();
                $table->string('full_name');
                $table->string('phone_number');
                $table->string('email')->unique();
                $table->text('home_address');
                $table->integer('years_experience')->default(0);
                $table->json('specializations'); // store as JSON array
                $table->string('other_specialization')->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('userwait_lists');
    }
};
