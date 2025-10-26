<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_schedules_table.php

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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('duration'); // e.g., '60 minutes', '90 minutes'
            $table->enum('type', ['regular', 'extra', 'revision', 'test', 'meeting', 'workshop'])->default('regular');
            $table->enum('status', ['scheduled', 'cancelled', 'completed', 'postponed'])->default('scheduled');
            $table->boolean('recurring')->default(false);
            $table->enum('recurrence_pattern', ['daily', 'weekly', 'monthly'])->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->string('location')->nullable();
            $table->integer('max_attendees')->nullable();
            $table->integer('students_attending')->default(0);
            $table->timestamps();

            // Indexes for better performance
            $table->index(['class_id', 'date']);
            $table->index(['teacher_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};