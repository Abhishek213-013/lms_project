<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->enum('type', ['manual', 'auto_course', 'auto_teacher', 'auto_system'])->default('manual');
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('related_type')->nullable(); // 'course', 'teacher', 'system'
        });
    }

    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['type', 'related_id', 'related_type']);
        });
    }
};