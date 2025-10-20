<?php
// database/migrations/0001_01_01_000000_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->date('dob')->nullable();
            $table->enum('education_qualification', ['HSC', 'BSC', 'BA', 'MA', 'MSC', 'PhD', 'Other']);
            $table->string('institute')->nullable();
            $table->string('password');
            $table->enum('role', ['super_admin', 'admin', 'teacher'])->default('teacher');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};