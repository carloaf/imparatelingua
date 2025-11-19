<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $exams = Exam::with('questions')->get();
        
        return response()->json([
            'success' => true,
            'data' => $exams,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'year' => 'nullable|integer|min:2000|max:2100',
            'description' => 'nullable|string',
        ]);

        $exam = Exam::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Exame criado com sucesso',
            'data' => $exam,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $exam = Exam::with(['questions.answers', 'questions.category'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $exam,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $exam = Exam::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'level' => 'sometimes|in:A1,A2,B1,B2,C1,C2',
            'year' => 'nullable|integer|min:2000|max:2100',
            'description' => 'nullable|string',
        ]);

        $exam->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Exame atualizado com sucesso',
            'data' => $exam,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exame deletado com sucesso',
        ]);
    }

    /**
     * Get questions for a specific exam.
     */
    public function questions(string $id): JsonResponse
    {
        $exam = Exam::with(['questionsOrdered.answersOrdered', 'questionsOrdered.category'])
            ->findOrFail($id);

        // Renomear 'answersOrdered' para 'answers_ordered' na resposta
        $questions = $exam->questionsOrdered->map(function ($question) {
            $questionArray = $question->toArray();
            if (isset($questionArray['answers_ordered'])) {
                $questionArray['answers_ordered'] = $questionArray['answers_ordered'];
            }
            return $questionArray;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'exam' => $exam->only(['id', 'name', 'level', 'year']),
                'questions' => $questions,
            ],
        ]);
    }
}
