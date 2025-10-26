<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_thumbnail_path_to_resources_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->string('thumbnail_path')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('thumbnail_path');
        });
    }
};