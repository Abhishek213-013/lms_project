<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_profile_fields_to_students_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToStudentsTable extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Add admission_date if not exists
            if (!Schema::hasColumn('students', 'admission_date')) {
                $table->date('admission_date')->nullable()->after('address');
            }
            
            // Add profile fields
            if (!Schema::hasColumn('students', 'phone')) {
                $table->string('phone')->nullable()->after('parent_contact');
            }
            
            if (!Schema::hasColumn('students', 'bio')) {
                $table->text('bio')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('students', 'location')) {
                $table->string('location')->nullable()->after('bio');
            }
            
            if (!Schema::hasColumn('students', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('location');
            }
            
            // Make parent_contact nullable since we're adding phone
            $table->string('parent_contact')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['admission_date', 'phone', 'bio', 'location', 'profile_picture']);
            $table->string('parent_contact')->nullable(false)->change();
        });
    }
}