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
        Schema::table('users', function (Blueprint $table) {
            Schema::rename('users', 'tblUsers');
        });
        Schema::table('tblscheduleapproval', function (Blueprint $table) {
            Schema::rename('tblscheduleapproval', 'tblScheduleApproval');
        });
        Schema::table('tblstores', function (Blueprint $table) {
            Schema::rename('tblstores', 'tblStores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::rename('tblUsers', 'users');
        });
        Schema::table('tblScheduleApproval', function (Blueprint $table) {
            Schema::rename('tblScheduleApproval', 'scheduleapproval');
        });
        Schema::table('tblStores', function (Blueprint $table) {
            Schema::rename('tblStores', 'tblstores');
        });
    }
};
