<?php
// database/migrations/2024_01_01_000000_add_status_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add the status column with a temporary default
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'inactive', 'rejected'])
                  ->default('active')
                  ->after('role');
        });

        // Step 2: Add rejection reason column
        Schema::table('users', function (Blueprint $table) {
            $table->text('rejection_reason')
                  ->nullable()
                  ->after('status');
        });

        // Step 3: Update existing records based on their role
        $this->updateExistingUserStatus();

        // Step 4: Add indexes for performance
        Schema::table('users', function (Blueprint $table) {
            $table->index('status');
            $table->index(['role', 'status']);
        });
    }

    /**
     * Update existing user records with appropriate status
     */
    private function updateExistingUserStatus(): void
    {
        // All existing users are considered active
        DB::table('users')->update(['status' => 'active']);
        
        // If you have any specific logic for different roles, add it here
        // For example:
        // DB::table('users')->where('role', 'teacher')->update(['status' => 'active']);
        // DB::table('users')->where('role', 'student')->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['status']);
            $table->dropIndex(['role', 'status']);
            
            // Drop columns
            $table->dropColumn('rejection_reason');
            $table->dropColumn('status');
        });
    }
};