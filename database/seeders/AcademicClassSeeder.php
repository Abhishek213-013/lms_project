<?php
// database/seeders/AcademicClassSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicClass;

class AcademicClassSeeder extends Seeder
{
    public function run()
    {
        $classes = [
            ['name' => 'Class 1', 'grade' => 1, 'capacity' => 30],
            ['name' => 'Class 2', 'grade' => 2, 'capacity' => 30],
            ['name' => 'Class 3', 'grade' => 3, 'capacity' => 30],
            ['name' => 'Class 4', 'grade' => 4, 'capacity' => 35],
            ['name' => 'Class 5', 'grade' => 5, 'capacity' => 35],
            ['name' => 'Class 6', 'grade' => 6, 'capacity' => 40],
            ['name' => 'Class 7', 'grade' => 7, 'capacity' => 40],
            ['name' => 'Class 8', 'grade' => 8, 'capacity' => 40],
            ['name' => 'Class 9', 'grade' => 9, 'capacity' => 35],
            ['name' => 'Class 10', 'grade' => 10, 'capacity' => 35],
            ['name' => 'Class 11', 'grade' => 11, 'capacity' => 30],
            ['name' => 'Class 12', 'grade' => 12, 'capacity' => 30],
        ];

        foreach ($classes as $class) {
            AcademicClass::create($class);
        }
    }
}