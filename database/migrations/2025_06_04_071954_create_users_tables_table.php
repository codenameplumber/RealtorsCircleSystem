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
        Schema::create('users_tables', function (Blueprint $table) {
            $table->id();
            $table->string('First_name');
            $table->string('Last_name');
            $table->string('Email')->unique();
            $table->string('Phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('Password');
            $table->longText('Bio')->nullable();
            $table->longText('Address')->nullable();
            $table->string('Specialization')->nullable();
            $table->string('Yrs_experience')->nullable();
            $table->json('Notification')->nullable();
            $table->string('2fa');
            $table->string('Status');
            $table->string('State')->nullable();
            $table->string('Local_area')->nullable();
            $table->string('Country')->nullable();
            $table->string('steps_signup');
            $table->string('api_token', 80)->unique()->nullable()->default(null); // Add this line
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_tables');
    }
};
