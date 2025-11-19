<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        $courses = Course::where('is_active', true)
            ->orderBy('order')
            ->with(['lessons' => function($query) {
                $query->orderBy('order');
            }])
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'description' => $course->description,
                    'level' => $course->level,
                    'image_url' => $course->image_url,
                    'total_lessons' => $course->lessons->count(),
                    'estimated_time' => $course->lessons->sum('estimated_time'),
                ];
            });

        return response()->json($courses);
    }

    /**
     * Display the specified course with lessons.
     */
    public function show($id)
    {
        $course = Course::with(['lessons' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($id);

        $userId = request()->user_id ?? 1; // TODO: Get from authenticated user

        $lessonsWithProgress = $course->lessons->map(function($lesson) use ($userId) {
            $progress = $lesson->progressForUser($userId);
            
            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'slug' => $lesson->slug,
                'lesson_type' => $lesson->lesson_type,
                'difficulty' => $lesson->difficulty,
                'estimated_time' => $lesson->estimated_time,
                'order' => $lesson->order,
                'progress' => $progress ? [
                    'status' => $progress->status,
                    'completion_percentage' => $progress->completion_percentage,
                    'exercises_completed' => $progress->exercises_completed,
                    'exercises_correct' => $progress->exercises_correct,
                ] : null
            ];
        });

        return response()->json([
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'description' => $course->description,
            'level' => $course->level,
            'image_url' => $course->image_url,
            'lessons' => $lessonsWithProgress,
            'total_lessons' => $lessonsWithProgress->count(),
            'estimated_time' => $course->lessons->sum('estimated_time'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
