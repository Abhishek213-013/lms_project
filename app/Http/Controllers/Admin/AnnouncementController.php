<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TeacherController;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Resource;
class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        
        return inertia('Admin/Announcement/Index', [
            'announcements' => $announcements
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_bn' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Generate Bengali date
        $bengaliDate = $this->getBengaliDate();

        $announcement = new Announcement();
        $announcement->title = $request->title;
        $announcement->title_bn = $request->title_bn;
        $announcement->content = $request->content;
        $announcement->content_bn = $request->content_bn;
        $announcement->date = now()->toDateString();
        $announcement->date_bn = $bengaliDate;
        $announcement->type = 'manual'; // Manual announcement

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
            $announcement->image = $imagePath;
        }

        $announcement->save();

        return redirect()->back()->with('success', 'Announcement created successfully.');
    }

    /**
     * Create a dynamic announcement for new course creation
     */
    public static function createCourseAnnouncement($courseData)
    {
        $bengaliDate = self::getBengaliDateStatic();

        $announcement = new Announcement();
        
        if ($courseData['type'] === 'regular') {
            // Regular class announcement
            $announcement->title = "New Class Alert: Class {$courseData['grade']} - {$courseData['name']} is Created";
            $announcement->title_bn = "নতুন ক্লাস সতর্কতা: ক্লাস {$courseData['grade']} - {$courseData['name']} তৈরি করা হয়েছে";
            $announcement->content = "A new class has been created: Class {$courseData['grade']} - {$courseData['name']}. This class includes multiple subjects and is now available for enrollment.";
            $announcement->content_bn = "একটি নতুন ক্লাস তৈরি করা হয়েছে: ক্লাস {$courseData['grade']} - {$courseData['name']}। এই ক্লাসে একাধিক বিষয় অন্তর্ভুক্ত রয়েছে এবং এখন ভর্তির জন্য উপলব্ধ।";
        } else {
            // Skill-based course announcement
            $categoryName = self::getCategoryName($courseData['category']);
            $announcement->title = "New Course Alert: {$courseData['name']} - {$categoryName} Course is Created";
            $announcement->title_bn = "নতুন কোর্স সতর্কতা: {$courseData['name']} - {$categoryName} কোর্স তৈরি করা হয়েছে";
            $announcement->content = "A new skill-based course has been created: {$courseData['name']}. This {$categoryName} course is now available for enrollment.";
            $announcement->content_bn = "একটি নতুন দক্ষতা-ভিত্তিক কোর্স তৈরি করা হয়েছে: {$courseData['name']}। এই {$categoryName} কোর্স এখন ভর্তির জন্য উপলব্ধ।";
        }

        $announcement->date = now()->toDateString();
        $announcement->date_bn = $bengaliDate;
        $announcement->type = 'auto_course'; // Auto-generated for course creation
        $announcement->related_id = $courseData['id'] ?? null;
        $announcement->related_type = 'course';

        $announcement->save();

        return $announcement;
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_bn' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $announcement->title = $request->title;
        $announcement->title_bn = $request->title_bn;
        $announcement->content = $request->content;
        $announcement->content_bn = $request->content_bn;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image);
            }
            
            $imagePath = $request->file('image')->store('announcements', 'public');
            $announcement->image = $imagePath;
        }

        $announcement->save();

        return redirect()->back()->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image);
        }

        $announcement->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }

    public function toggleStatus(Announcement $announcement)
    {
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        return redirect()->back()->with('success', 'Announcement status updated successfully.');
    }

    private function getBengaliDate()
    {
        return self::getBengaliDateStatic();
    }

    private static function getBengaliDateStatic()
    {
        // Simple Bengali date conversion
        $englishMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $bengaliMonths = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'];
        
        $date = now();
        $bengaliMonth = $bengaliMonths[$date->month - 1];
        
        return $date->format('d') . ' ' . $bengaliMonth . ' ' . $date->format('Y');
    }

    private static function getCategoryName($category)
    {
        $categories = [
            'life_skills' => 'Life Skills',
            'spoken_english' => 'Spoken English',
            'computer_basics' => 'Computer Basics',
            'art_craft' => 'Art & Craft',
            'music' => 'Music',
            'sports' => 'Sports',
            'dance' => 'Dance',
            'yoga' => 'Yoga & Meditation',
            'career_counseling' => 'Career Counseling',
            'other' => 'Other'
        ];

        return $categories[$category] ?? 'Skill';
    }

    public static function createTeacherAssignmentAnnouncement($assignmentData)
    {
        $bengaliDate = self::getBengaliDateStatic();

        $announcement = new Announcement();
        
        // Get teacher and class details
        $teacher = User::find($assignmentData['teacher_id']);
        $class = ClassModel::find($assignmentData['class_id']);

        if ($teacher && $class) {
            // For regular classes: "Teacher Alert: Dr. Smith is assigned to Class 5 - Mathematics"
            // For other courses: "Teacher Alert: Ms. Johnson is assigned to Spoken English Course"
            
            if ($class->type === 'regular') {
                $announcement->title = "Teacher Alert: {$teacher->name} is assigned to {$class->name} - {$class->subject}";
                $announcement->title_bn = "শিক্ষক সতর্কতা: {$teacher->name} {$class->name} - {$class->subject} এ নিযুক্ত করা হয়েছে";
                $announcement->content = "Teacher {$teacher->name} has been assigned to teach {$class->subject} in {$class->name}. Students can now access classes from this experienced educator.";
                $announcement->content_bn = "শিক্ষক {$teacher->name} কে {$class->name} - {$class->subject} পড়ানোর জন্য নিযুক্ত করা হয়েছে। শিক্ষার্থীরা এখন এই অভিজ্ঞ শিক্ষকের ক্লাসে অংশগ্রহণ করতে পারবে।";
            } else {
                $courseType = self::getCourseTypeName($class->category);
                $announcement->title = "Teacher Alert: {$teacher->name} is assigned to {$class->name} - {$courseType} Course";
                $announcement->title_bn = "শিক্ষক সতর্কতা: {$teacher->name} {$class->name} - {$courseType} কোর্সে নিযুক্ত করা হয়েছে";
                $announcement->content = "Teacher {$teacher->name} has been assigned to teach {$class->name} course. This {$courseType} course is now available with expert instruction.";
                $announcement->content_bn = "শিক্ষক {$teacher->name} কে {$class->name} কোর্স পড়ানোর জন্য নিযুক্ত করা হয়েছে। এই {$courseType} কোর্সটি এখন বিশেষজ্ঞ নির্দেশনায় উপলব্ধ।";
            }
        } else {
            // Fallback if data is incomplete
            $announcement->title = "Teacher Assignment: New teacher assigned";
            $announcement->title_bn = "শিক্ষক নিয়োগ: নতুন শিক্ষক নিযুক্ত";
            $announcement->content = "A teacher has been assigned to a class. Check the teacher portal for details.";
            $announcement->content_bn = "একজন শিক্ষককে একটি ক্লাসে নিযুক্ত করা হয়েছে। বিস্তারিত জানতে শিক্ষক পোর্টাল দেখুন।";
        }

        $announcement->date = now()->toDateString();
        $announcement->date_bn = $bengaliDate;
        $announcement->type = 'auto_teacher'; // Auto-generated for teacher assignment
        $announcement->related_id = $assignmentData['teacher_id'];
        $announcement->related_type = 'teacher_assignment';

        $announcement->save();

        return $announcement;
    }

    /**
     * Get course type name for display
     */
    private static function getCourseTypeName($category)
    {
        $categories = [
            'life_skills' => 'Life Skills',
            'spoken_english' => 'Spoken English',
            'computer_basics' => 'Computer Basics',
            'art_craft' => 'Art & Craft',
            'music' => 'Music',
            'sports' => 'Sports',
            'dance' => 'Dance',
            'yoga' => 'Yoga & Meditation',
            'career_counseling' => 'Career Counseling',
            'other' => 'Skill Development'
        ];

        return $categories[$category] ?? 'Skill';
    }
    public static function createResourceAnnouncement($resourceData)
    {
        $bengaliDate = self::getBengaliDateStatic();

        $announcement = new Announcement();
        
        // Get teacher, class, and resource details
        $teacher = User::find($resourceData['teacher_id']);
        $class = ClassModel::find($resourceData['class_id']);
        $resource = Resource::find($resourceData['resource_id']); // Changed this line

        if ($teacher && $class && $resource) {
            $resourceType = $resource->type === 'video' ? 'Video Resource' : 'Resource';
            $resourceTypeBn = $resource->type === 'video' ? 'ভিডিও রিসোর্স' : 'রিসোর্স';
            
            $announcement->title = "{$resourceType}: {$teacher->name} provided {$resource->title} to {$class->name}";
            $announcement->title_bn = "{$resourceTypeBn}: {$teacher->name} {$class->name} এ {$resource->title} প্রদান করেছেন";
            
            if ($resource->description) {
                $announcement->content = "Teacher {$teacher->name} has shared a new resource: \"{$resource->title}\" - {$resource->description}. Available in {$class->name}.";
                $announcement->content_bn = "শিক্ষক {$teacher->name} একটি নতুন রিসোর্স শেয়ার করেছেন: \"{$resource->title}\" - {$resource->description}। {$class->name} এ উপলব্ধ।";
            } else {
                $announcement->content = "Teacher {$teacher->name} has shared a new resource: \"{$resource->title}\". Available in {$class->name}.";
                $announcement->content_bn = "শিক্ষক {$teacher->name} একটি নতুন রিসোর্স শেয়ার করেছেন: \"{$resource->title}\"। {$class->name} এ উপলব্ধ।";
            }
        } else {
            // Fallback if data is incomplete
            $announcement->title = "New Resource: Teacher shared learning material";
            $announcement->title_bn = "নতুন রিসোর্স: শিক্ষক শিক্ষণ উপকরণ শেয়ার করেছেন";
            $announcement->content = "A teacher has shared new learning resources. Check your class for details.";
            $announcement->content_bn = "একজন শিক্ষক নতুন শিক্ষণ রিসোর্স শেয়ার করেছেন। বিস্তারিত জানতে আপনার ক্লাস চেক করুন।";
        }

        $announcement->date = now()->toDateString();
        $announcement->date_bn = $bengaliDate;
        $announcement->type = 'auto_resource'; // Auto-generated for resource upload
        $announcement->related_id = $resourceData['resource_id'];
        $announcement->related_type = 'resource_upload';

        $announcement->save();

        return $announcement;
    }
}