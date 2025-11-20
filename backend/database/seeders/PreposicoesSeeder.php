<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Category;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Str;

class PreposicoesSeeder extends Seeder
{
    /**
     * Importa módulo de Preposições reformatado com exercícios de qualidade
     */
    public function run(): void
    {
        $this->command->info('🚀 Importando Módulo de Preposições Reformatado');
        
        // Curso
        $course = Course::where('slug', 'italiano-completo-a1-b1')->firstOrFail();
        
        // Categoria
        $category = Category::firstOrCreate(
            ['slug' => 'gramatica'],
            [
                'name' => 'Gramática',
                'description' => 'Gramática italiana essencial'
            ]
        );
        
        // Carregar módulo JSON
        $modulePath = storage_path('app/imports/modules_formatted/modulo_07_preposicoes_formatted.json');
        $moduleData = json_decode(file_get_contents($modulePath), true);
        
        $this->command->info("📖 Importando: {$moduleData['module_name']}");
        
        // Criar lição
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => $moduleData['module_name'],
            'slug' => 'preposicoes-simples-e-articuladas',
            'content_italian' => $moduleData['content_italian'],
            'content_portuguese' => $moduleData['content_portuguese'],
            'order' => 7,
            'estimated_time' => $moduleData['estimated_time'],
            'difficulty' => $moduleData['difficulty'],
        ]);
        
        // Criar exercícios
        $exercisesCount = 0;
        if (!empty($moduleData['exercises'])) {
            foreach ($moduleData['exercises'] as $exerciseData) {
                $question = Question::create([
                    'lesson_id' => $lesson->id,
                    'category_id' => $category->id,
                    'question_text' => $exerciseData['question_text'],
                    'question_type' => $exerciseData['question_type'],
                    'difficulty' => $exerciseData['difficulty'],
                    'order' => $exerciseData['order'],
                    'context' => $exerciseData['explanation'] ?? null,
                ]);
                
                // Criar opções de resposta
                if ($exerciseData['question_type'] === 'multiple_choice' && isset($exerciseData['options'])) {
                    foreach ($exerciseData['options'] as $optIndex => $option) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => $option['text'],
                            'is_correct' => $option['is_correct'],
                            'order' => $optIndex + 1,
                        ]);
                    }
                }
                
                $exercisesCount++;
            }
        }
        
        $this->command->info("  ✅ Lição ID: {$lesson->id} | {$exercisesCount} exercícios | {$lesson->estimated_time}min");
        
        $this->command->info("\n🎉 Importação concluída!");
        $this->command->info("📊 Estatísticas:");
        $this->command->info("   • Lição: {$lesson->title}");
        $this->command->info("   • Exercícios: {$exercisesCount}");
        $this->command->info("   • Tempo estimado: {$lesson->estimated_time} minutos");
        $this->command->info("\n🌐 Acesse: http://localhost:3000/lesson/{$lesson->id}");
    }
}
