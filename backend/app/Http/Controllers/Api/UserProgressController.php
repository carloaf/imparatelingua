<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserProgressController extends Controller
{
    /**
     * Get user progress (all answered questions).
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('user_id', 1); // Temporário, depois usar Auth::id()

        $progress = UserProgress::with(['question.exam', 'question.category', 'selectedAnswer'])
            ->where('user_id', $userId)
            ->orderBy('answered_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    /**
     * Get user statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $userId = $request->input('user_id', 1); // Temporário, depois usar Auth::id()

        $stats = [
            'total_answered' => UserProgress::where('user_id', $userId)->count(),
            'correct_answers' => UserProgress::where('user_id', $userId)->where('is_correct', true)->count(),
            'incorrect_answers' => UserProgress::where('user_id', $userId)->where('is_correct', false)->count(),
        ];

        $stats['accuracy'] = $stats['total_answered'] > 0 
            ? round(($stats['correct_answers'] / $stats['total_answered']) * 100, 2) 
            : 0;

        // Estatísticas por categoria
        $byCategory = UserProgress::select('categories.name', 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN user_progress.is_correct = 1 THEN 1 ELSE 0 END) as correct')
            )
            ->join('questions', 'user_progress.question_id', '=', 'questions.id')
            ->join('categories', 'questions.category_id', '=', 'categories.id')
            ->where('user_progress.user_id', $userId)
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function($item) {
                $item->accuracy = $item->total > 0 
                    ? round(($item->correct / $item->total) * 100, 2) 
                    : 0;
                return $item;
            });

        // Estatísticas por nível (exame)
        $byLevel = UserProgress::select('exams.level', 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN user_progress.is_correct = 1 THEN 1 ELSE 0 END) as correct')
            )
            ->join('questions', 'user_progress.question_id', '=', 'questions.id')
            ->join('exams', 'questions.exam_id', '=', 'exams.id')
            ->where('user_progress.user_id', $userId)
            ->groupBy('exams.level')
            ->get()
            ->map(function($item) {
                $item->accuracy = $item->total > 0 
                    ? round(($item->correct / $item->total) * 100, 2) 
                    : 0;
                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'overall' => $stats,
                'by_category' => $byCategory,
                'by_level' => $byLevel,
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $progress = UserProgress::with(['question', 'selectedAnswer'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $progress = UserProgress::findOrFail($id);
        $progress->delete();

        return response()->json([
            'success' => true,
            'message' => 'Progresso deletado com sucesso',
        ]);
    }
}
