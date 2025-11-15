<?php
// database/migrations/YYYY_MM_DD_remove_class_id_from_students_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['class_id']);
            // Then drop the column
            $table->dropColumn('class_id');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->constrained('classes');
        });
    }
};