<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_classes_table_add_new_columns.php

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
        Schema::table('classes', function (Blueprint $table) {
            // Add the missing columns
            $table->integer('grade')->nullable()->after('name');
            $table->string('code')->nullable()->after('subject');
            $table->integer('capacity')->default(30)->after('code');
            $table->enum('type', ['regular', 'other'])->default('regular')->after('capacity');
            $table->string('category')->nullable()->after('type');
            
            // Make teacher_id nullable (since courses might not have teachers initially)
            $table->unsignedBigInteger('teacher_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['grade', 'code', 'capacity', 'type', 'category']);
            // Revert teacher_id to not nullable if needed
            $table->unsignedBigInteger('teacher_id')->nullable(false)->change();
        });
    }
};