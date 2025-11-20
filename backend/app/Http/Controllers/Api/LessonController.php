<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\UserLessonProgress;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LessonController extends Controller
{
    /**
     * Display the specified lesson with full content.
     */
    public function show($id)
    {
        $lesson = Lesson::with(['course', 'questions.answers'])->findOrFail($id);
        $userId = request()->user_id ?? 1; // TODO: Get from authenticated user

        // Get or create progress
        $progress = UserLessonProgress::firstOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $lesson->id
            ],
            [
                'status' => 'not_started',
                'started_at' => Carbon::now()
            ]
        );

        // Update last accessed
        $progress->update(['last_accessed_at' => Carbon::now()]);

        // Mark as in_progress if not started
        if ($progress->status === 'not_started') {
            $progress->update(['status' => 'in_progress']);
        }

        // Format exercises from Questions table
        $exercises = $lesson->questions->map(function ($question) {
            $formattedQuestion = [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'difficulty' => $question->difficulty,
                'order' => $question->order,
                'explanation' => $question->context,
            ];

            if ($question->question_type === 'multiple_choice') {
                $formattedQuestion['options'] = $question->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'text' => $answer->answer_text,
                        'is_correct' => $answer->is_correct,
                    ];
                })->toArray();
            } elseif ($question->question_type === 'fill_in_blank') {
                $correctAnswer = $question->answers->where('is_correct', true)->first();
                $formattedQuestion['correct_answer'] = $correctAnswer ? $correctAnswer->answer_text : null;
            }

            return $formattedQuestion;
        })->toArray();

        return response()->json([
            'id' => $lesson->id,
            'title' => $lesson->title,
            'slug' => $lesson->slug,
            'content_italian' => $lesson->content_italian,
            'content_portuguese' => $lesson->content_portuguese,
            'exercises' => $exercises,
            'lesson_type' => $lesson->lesson_type,
            'difficulty' => $lesson->difficulty,
            'estimated_time' => $lesson->estimated_time,
            'order' => $lesson->order,
            'course' => [
                'id' => $lesson->course->id,
                'title' => $lesson->course->title,
            ],
            'progress' => [
                'status' => $progress->status,
                'completion_percentage' => $progress->completion_percentage,
                'exercises_completed' => $progress->exercises_completed,
                'exercises_correct' => $progress->exercises_correct,
                'time_spent' => $progress->time_spent,
            ]
        ]);
    }

    /**
     * Update lesson progress.
     */
    public function updateProgress(Request $request, $id)
    {
        $userId = $request->user_id ?? 1; // TODO: Get from authenticated user
        
        $progress = UserLessonProgress::where('user_id', $userId)
            ->where('lesson_id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => 'sometimes|in:not_started,in_progress,completed',
            'time_spent' => 'sometimes|integer',
            'completion_percentage' => 'sometimes|integer|min:0|max:100',
            'exercises_completed' => 'sometimes|integer',
            'exercises_correct' => 'sometimes|integer',
        ]);

        // If marking as completed, set completed_at
        if (isset($validated['status']) && $validated['status'] === 'completed' && $progress->status !== 'completed') {
            $validated['completed_at'] = Carbon::now();
            $validated['completion_percentage'] = 100;
        }

        $progress->update($validated);

        return response()->json([
            'message' => 'Progress updated successfully',
            'progress' => $progress
        ]);
    }

    /**
     * Mark lesson as completed.
     */
    public function complete($id)
    {
        $userId = request()->user_id ?? 1; // TODO: Get from authenticated user
        
        $progress = UserLessonProgress::where('user_id', $userId)
            ->where('lesson_id', $id)
            ->firstOrFail();

        $progress->update([
            'status' => 'completed',
            'completion_percentage' => 100,
            'completed_at' => Carbon::now()
        ]);

        return response()->json([
            'message' => 'Lesson completed successfully',
            'progress' => $progress
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
