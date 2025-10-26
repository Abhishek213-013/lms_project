<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_assignments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('points')->default(100);
            $table->timestamp('due_date');
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('active');
            $table->json('attachments')->nullable();
            $table->timestamps();
            
            $table->index(['class_id', 'teacher_id']);
            $table->index(['due_date', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignments');
    }
}