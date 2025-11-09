<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds image support for courses and classes
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Main course image (for display in cards, details page)
            $table->string('image')->nullable()->after('category')
                  ->comment('Main image for the course/class');
            
            // Thumbnail version (for lists, previews)
            $table->string('thumbnail')->nullable()->after('image')
                  ->comment('Thumbnail version for better performance');
            
            // Optional: Add image metadata fields
            $table->string('image_alt')->nullable()->after('thumbnail')
                  ->comment('Alt text for accessibility');
            $table->string('image_caption')->nullable()->after('image_alt')
                  ->comment('Optional image caption');
            
            // Indexes for better query performance
            $table->index(['type', 'status'], 'idx_type_status');
            $table->index('grade', 'idx_grade');
            $table->index('teacher_id', 'idx_teacher');
            
            // Optional: Add soft deletes if not already present
            if (!Schema::hasColumn('classes', 'deleted_at')) {
                $table->softDeletes()->comment('Soft delete support');
            }
        });

        // Update existing records with default image paths if needed
        DB::statement("UPDATE classes SET image = NULL, thumbnail = NULL WHERE 1=1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Remove indexes first
            $table->dropIndex('idx_type_status');
            $table->dropIndex('idx_grade');
            $table->dropIndex('idx_teacher');
            
            // Remove columns
            $table->dropColumn([
                'image',
                'thumbnail', 
                'image_alt',
                'image_caption'
            ]);
            
            // Optional: Remove soft deletes if we added them
            if (Schema::hasColumn('classes', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};