<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_schedules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('duration');
            $table->enum('type', ['regular', 'extra', 'revision', 'test'])->default('regular');
            $table->enum('status', ['scheduled', 'cancelled', 'completed'])->default('scheduled');
            $table->boolean('recurring')->default(false);
            $table->string('recurrence_pattern')->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->integer('students_attending')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};