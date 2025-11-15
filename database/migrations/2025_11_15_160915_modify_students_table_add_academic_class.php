<?php
// database/migrations/2025_11_15_xxxxxx_modify_students_table_add_academic_class.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Add academic_class_id first
            $table->foreignId('academic_class_id')->nullable()->constrained('academic_classes');
            
            // We'll keep class_id for now during transition
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['academic_class_id']);
            $table->dropColumn('academic_class_id');
        });
    }
};