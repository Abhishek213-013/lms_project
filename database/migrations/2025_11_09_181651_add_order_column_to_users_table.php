<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_order_column_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('order_column')->default(0)->after('profile_picture');
        });

        // Set initial order for existing teachers
        \App\Models\User::where('role', 'teacher')
            ->where('status', 'active')
            ->orderBy('created_at')
            ->get()
            ->each(function ($teacher, $index) {
                $teacher->update(['order_column' => $index + 1]);
            });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('order_column');
        });
    }
};