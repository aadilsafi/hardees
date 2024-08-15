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
        // check if schema exists then do the renaming and the new name should also not exist
        if (Schema::hasTable('users') && !Schema::hasTable('tblUsers')) {
            Schema::rename('users', 'tblUsers');
        }
        if (Schema::hasTable('tblscheduleapproval') && !Schema::hasTable('tblScheduleApproval')) {
            Schema::rename('tblscheduleapproval', 'tblScheduleApproval');
        }
        if (Schema::hasTable('tblstores') && !Schema::hasTable('tblStores')) {
            Schema::rename('tblstores', 'tblStores');
        }
        if (Schema::hasTable('tblusers')){
            Schema::dropIfExists('tblusers');
        }
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
