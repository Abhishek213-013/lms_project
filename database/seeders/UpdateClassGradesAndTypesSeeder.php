<?php
// database/seeders/UpdateClassGradesAndTypesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class UpdateClassGradesAndTypesSeeder extends Seeder
{
    public function run()
    {
        // Update grades for classes 1-12 with all sections
        for ($grade = 1; $grade <= 12; $grade++) {
            ClassModel::where('name', 'like', "Class {$grade}%")
                     ->orWhere('name', 'like', "{$grade}A%")
                     ->orWhere('name', 'like', "{$grade}B%")
                     ->orWhere('name', 'like', "{$grade}C%")
                     ->orWhere('name', 'like', "{$grade} %")
                     ->update(['grade' => $grade, 'type' => 'regular']);
        }

        // Update other courses
        $otherCourses = [
            'life_skills' => ['Life Skills', 'life skills', 'Life skills', 'Personal Development'],
            'arts' => ['Dance Class', 'Dance', 'dance', 'Music', 'Art', 'Drawing', 'Painting', 'Drama'],
            'career_counseling' => ['Career Counseling', 'Career', 'career counseling', 'Career Guidance'],
            'sports' => ['Sports', 'Physical Training', 'Yoga', 'Meditation', 'Basketball', 'Football'],
            'language' => ['Spoken English', 'French', 'Spanish', 'German', 'Language Club'],
            'stem' => ['Robotics', 'Coding', 'Programming', 'STEM Club', 'Science Club'],
            'other' => ['Debate Club', 'Book Club', 'Photography']
        ];

        foreach ($otherCourses as $category => $names) {
            ClassModel::whereIn('name', $names)
                     ->update(['type' => 'other', 'category' => $category]);
        }

        // Set any remaining classes with grade 1-12 as regular
        ClassModel::whereBetween('grade', [1, 12])
                 ->whereNull('type')
                 ->update(['type' => 'regular']);

        $this->command->info('Class grades and types updated successfully!');
        
        // Display summary
        $summary = ClassModel::select('type', 'grade', DB::raw('COUNT(*) as total'))
                           ->groupBy('type', 'grade')
                           ->orderBy('type')
                           ->orderBy('grade')
                           ->get();
        
        foreach ($summary as $row) {
            $this->command->info("Type: {$row->type}, Grade: {$row->grade}, Total: {$row->total}");
        }
    }
}