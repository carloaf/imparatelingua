<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Answer;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Question::with(['exam', 'category', 'answersOrdered']);

        // Filtrar por categoria
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrar por exame
        if ($request->has('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        // Filtrar por dificuldade
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $questions = $query->orderBy('order')->get();

        return response()->json([
            'success' => true,
            'data' => $questions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'category_id' => 'required|exists:categories,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,fill_in_blank,true_false',
            'difficulty' => 'required|integer|min:1|max:5',
            'context' => 'nullable|string',
            'order' => 'nullable|integer',
            'answers' => 'required|array|min:2',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
            'answers.*.order' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            // Criar a questão
            $question = Question::create([
                'exam_id' => $validated['exam_id'],
                'category_id' => $validated['category_id'],
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'difficulty' => $validated['difficulty'],
                'context' => $validated['context'] ?? null,
                'order' => $validated['order'] ?? 0,
            ]);

            // Criar as respostas
            foreach ($validated['answers'] as $index => $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $answerData['answer_text'],
                    'is_correct' => $answerData['is_correct'],
                    'order' => $answerData['order'] ?? $index,
                ]);
            }

            DB::commit();

            $question->load('answers');

            return response()->json([
                'success' => true,
                'message' => 'Questão criada com sucesso',
                'data' => $question,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar questão: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $question = Question::with(['exam', 'category', 'answersOrdered'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $question,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'exam_id' => 'sometimes|exists:exams,id',
            'category_id' => 'sometimes|exists:categories,id',
            'question_text' => 'sometimes|string',
            'question_type' => 'sometimes|in:multiple_choice,fill_in_blank,true_false',
            'difficulty' => 'sometimes|integer|min:1|max:5',
            'context' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $question->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Questão atualizada com sucesso',
            'data' => $question,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Questão deletada com sucesso',
        ]);
    }

    /**
     * Answer a question.
     */
    public function answer(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'answer_id' => 'required|exists:answers,id',
            'user_id' => 'required|exists:users,id', // Temporário, depois usar Auth::id()
        ]);

        $question = Question::findOrFail($id);
        $answer = Answer::findOrFail($validated['answer_id']);

        // Verificar se a resposta pertence à questão
        if ($answer->question_id != $question->id) {
            return response()->json([
                'success' => false,
                'message' => 'Resposta não pertence a esta questão',
            ], 400);
        }

        // Registrar o progresso
        $progress = UserProgress::create([
            'user_id' => $validated['user_id'],
            'question_id' => $question->id,
            'selected_answer_id' => $answer->id,
            'is_correct' => $answer->is_correct,
            'answered_at' => now(),
        ]);

        // Buscar a resposta correta para retornar
        $correctAnswer = $question->correctAnswer;

        return response()->json([
            'success' => true,
            'message' => $answer->is_correct ? 'Resposta correta!' : 'Resposta incorreta',
            'data' => [
                'is_correct' => $answer->is_correct,
                'selected_answer' => $answer,
                'correct_answer' => $correctAnswer,
            ],
        ]);
    }
}
