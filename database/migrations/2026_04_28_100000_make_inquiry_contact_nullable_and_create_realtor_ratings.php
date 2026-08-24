<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make property inquiry contact fields optional (booking no longer collects them)
        Schema::table('property_inquiries', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->text('message')->nullable()->change();
        });

        // Realtor ratings: one rating per session per realtor
        Schema::create('realtor_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('realtor_id');
            $table->tinyInteger('rating'); // 1–5
            $table->string('session_token', 64)->nullable();
            $table->foreign('realtor_id')->references('id')->on('users_tables')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realtor_ratings');
        Schema::table('property_inquiries', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
        });
    }
};
