<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable();
            $table->integer('time_spent_minutes')->default(0);
            $table->boolean('completed')->default(false);
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('accessed_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['user_id', 'class_id']);
            $table->index(['accessed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_progress');
    }
};