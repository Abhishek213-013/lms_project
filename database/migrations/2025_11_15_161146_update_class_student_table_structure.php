<?php
// database/migrations/2025_11_15_xxxxxx_update_class_student_table_structure.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('class_student', function (Blueprint $table) {
            // Add new columns
            $table->decimal('progress', 5, 2)->default(0)->after('student_id');
            $table->timestamp('last_accessed')->nullable()->after('progress');
            $table->string('last_activity_type')->nullable()->after('last_accessed');
            
            // Rename enrolled_at to created_at if needed, or keep both
            // Add unique constraint to prevent duplicate enrollments
            $table->unique(['student_id', 'class_id']);
        });

        // If you want to rename enrolled_at to created_at (optional)
        // Schema::table('class_student', function (Blueprint $table) {
        //     $table->renameColumn('enrolled_at', 'created_at');
        // });
    }

    public function down()
    {
        Schema::table('class_student', function (Blueprint $table) {
            // Remove the unique constraint
            $table->dropUnique(['student_id', 'class_id']);
            
            // Remove added columns
            $table->dropColumn(['progress', 'last_accessed', 'last_activity_type']);
        });

        // If you renamed enrolled_at, change it back
        // Schema::table('class_student', function (Blueprint $table) {
        //     $table->renameColumn('created_at', 'enrolled_at');
        // });
    }
};