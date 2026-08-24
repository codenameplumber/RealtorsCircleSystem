<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('property_inquiries', function (Blueprint $table) {
            $table->unsignedBigInteger('booker_user_id')->nullable()->after('property_id');
            $table->foreign('booker_user_id')->references('id')->on('users_tables')->nullOnDelete();
            $table->string('whatsapp')->nullable()->after('phone');
            $table->boolean('inspection_fee_acknowledged')->default(false)->after('status');
            $table->date('reschedule_date')->nullable()->after('inspection_fee_acknowledged');
            $table->string('reschedule_time')->nullable()->after('reschedule_date');
            $table->text('cancel_reason')->nullable()->after('reschedule_time');
        });
    }

    public function down(): void
    {
        Schema::table('property_inquiries', function (Blueprint $table) {
            $table->dropForeign(['booker_user_id']);
            $table->dropColumn([
                'booker_user_id', 'whatsapp', 'inspection_fee_acknowledged',
                'reschedule_date', 'reschedule_time', 'cancel_reason',
            ]);
        });
    }
};
