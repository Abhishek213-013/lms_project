<?php
// database/migrations/YYYY_MM_DD_create_academic_classes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('academic_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Class 1, Class 2, Class 3, etc.
            $table->integer('grade'); // 1, 2, 3, etc.
            $table->string('section')->nullable(); // A, B, C (if needed)
            $table->text('description')->nullable();
            $table->integer('capacity')->default(30);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_classes');
    }
};