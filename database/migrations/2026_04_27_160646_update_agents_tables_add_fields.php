<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents_tables', function (Blueprint $table) {
            $table->string('First_name');
            $table->string('Last_name');
            $table->string('Email')->unique();
            $table->string('Password');
            $table->string('Phone')->nullable();
            $table->string('Assigned_area')->nullable();
            $table->string('Specialization')->nullable();
            $table->enum('Status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::table('agents_tables', function (Blueprint $table) {
            $table->dropColumn(['First_name', 'Last_name', 'Email', 'Password', 'Phone', 'Assigned_area', 'Specialization', 'Status', 'api_token', 'remember_token']);
        });
    }
};
