<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAnnouncementsTypeColumn extends Migration
{
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            // If it's an ENUM, change it to string
            $table->string('type', 20)->default('manual')->change();
        });
    }

    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Revert if needed
            $table->string('type', 10)->default('manual')->change();
        });
    }
}