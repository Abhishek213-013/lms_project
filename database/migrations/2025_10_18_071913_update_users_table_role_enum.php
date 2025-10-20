<?php
// database/migrations/xxxx_xx_xx_xxxxxx_update_users_table_role_enum.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, let's check if we need to modify the role enum
        Schema::table('users', function (Blueprint $table) {
            // If role is an enum, we need to modify it to include 'student'
            // This is a bit complex with MySQL, so we'll use DB raw statement
        });

        // Alternative: Use raw SQL to modify the enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'teacher', 'student') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'teacher') NOT NULL");
    }
};