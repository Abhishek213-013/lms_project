<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_students_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('roll_number')->unique();
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('parent_contact');
            $table->string('country_code')->default('+880');
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->index(['user_id', 'class_id']);
            $table->index('roll_number');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}