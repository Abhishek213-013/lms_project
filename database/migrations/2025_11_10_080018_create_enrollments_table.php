<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->integer('progress')->default(0);
            $table->string('last_activity_type')->nullable();
            $table->timestamp('last_accessed')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_id', 'class_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
};