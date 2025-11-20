<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\DB;

class PdfExercisesSeeder extends Seeder
{
    /**
     * Importa exercícios extraídos do PDF ItalB1-25 (páginas 42, 46, 47, 62)
     * 
     * Módulos afetados:
     * - Módulo 31: Pronomes (4 exercícios de Aggettivi Dimostrativi)
     * - Módulo 32: Passato Prossimo (3 exercícios de verbos modais)
     * - Módulo 36: Preposições (9 exercícios: 6 fill_in_blank + 3 multiple_choice)
     */
    public function run(): void
    {
        $jsonFile = storage_path('app/imports/exercises_extracted/extracted_exercises.json');
        
        if (!file_exists($jsonFile)) {
            $this->command->error("❌ Arquivo não encontrado: {$jsonFile}");
            return;
        }

        $data = json_decode(file_get_contents($jsonFile), true);
        $exerciseGroups = $data['extracted_exercises'];

        $this->command->info("📚 Importando exercícios extraídos do PDF...\n");

        $totalExercises = 0;
        $totalAnswers = 0;

        foreach ($exerciseGroups as $groupKey => $group) {
            $lessonId = $group['module_id'];
            $lesson = Lesson::find($lessonId);

            if (!$lesson) {
                $this->command->warn("⚠️  Lição ID {$lessonId} não encontrada. Pulando grupo '{$groupKey}'");
                continue;
            }

            $this->command->info("📖 Processando: {$lesson->title}");
            $this->command->info("   Tipo: {$group['exercise_type']} | Página: {$group['source_page']}");

            // Obter o maior order já existente para esta lição
            $maxOrder = Question::where('lesson_id', $lessonId)->max('order') ?? 0;

            foreach ($group['exercises'] as $exerciseData) {
                $maxOrder++;

                $questionData = [
                    'lesson_id' => $lessonId,
                    'category_id' => 1, // Gramática
                    'question_text' => $exerciseData['question_text'],
                    'question_type' => $group['exercise_type'],
                    'difficulty' => $exerciseData['difficulty'],
                    'order' => $exerciseData['order'] ?? $maxOrder,
                    'context' => $exerciseData['explanation'] ?? null,
                ];

                $question = Question::create($questionData);
                $totalExercises++;

                // Criar respostas baseado no tipo de exercício
                if ($group['exercise_type'] === 'multiple_choice') {
                    foreach ($exerciseData['options'] as $index => $option) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => $option['text'],
                            'is_correct' => $option['is_correct'],
                            'order' => $index + 1,
                        ]);
                        $totalAnswers++;
                    }
                } elseif ($group['exercise_type'] === 'fill_in_blank') {
                    // Para fill_in_blank, criar resposta correta
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $exerciseData['correct_answer'],
                        'is_correct' => true,
                        'order' => 1,
                    ]);
                    $totalAnswers++;
                } elseif ($group['exercise_type'] === 'true_false') {
                    // Criar opções Vero/Falso
                    $correctAnswer = $exerciseData['correct_answer'];
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => 'Vero',
                        'is_correct' => ($correctAnswer === 'Vero'),
                        'order' => 1,
                    ]);
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => 'Falso',
                        'is_correct' => ($correctAnswer === 'Falso'),
                        'order' => 2,
                    ]);
                    $totalAnswers += 2;
                }
            }

            $this->command->info("   ✅ {$lesson->title}: " . count($group['exercises']) . " exercícios importados\n");
        }

        $this->command->info("═══════════════════════════════════════════════");
        $this->command->info("✅ Importação concluída!");
        $this->command->info("📊 Estatísticas:");
        $this->command->info("   • Total de exercícios: {$totalExercises}");
        $this->command->info("   • Total de respostas: {$totalAnswers}");
        $this->command->info("   • Módulos atualizados: " . count(array_unique(array_column($exerciseGroups, 'module_id'))));
        $this->command->info("═══════════════════════════════════════════════");

        // Mostrar contagem atualizada por módulo
        $this->command->info("\n📈 Contagem de exercícios por módulo:");
        foreach (array_unique(array_column($exerciseGroups, 'module_id')) as $lessonId) {
            $lesson = Lesson::find($lessonId);
            $count = Question::where('lesson_id', $lessonId)->count();
            $this->command->info("   • {$lesson->title}: {$count} exercícios");
        }
    }
}
