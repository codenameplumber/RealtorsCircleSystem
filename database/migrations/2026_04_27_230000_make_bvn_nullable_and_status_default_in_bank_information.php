<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_information', function (Blueprint $table) {
            $table->string('Bvn')->nullable()->change();
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bank_information', function (Blueprint $table) {
            $table->string('Bvn')->nullable(false)->change();
            $table->string('status')->default(null)->change();
        });
    }
};
