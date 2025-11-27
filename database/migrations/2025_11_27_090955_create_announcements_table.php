<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->text('content');
            $table->text('content_bn')->nullable();
            $table->string('image')->nullable();
            $table->date('date');
            $table->string('date_bn')->nullable();
            $table->enum('type', ['manual', 'auto_course', 'auto_teacher', 'auto_system'])->default('manual');
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('related_type')->nullable(); // 'course', 'teacher', 'system'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('announcements');
    }
};