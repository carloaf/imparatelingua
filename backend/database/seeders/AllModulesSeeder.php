<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Category;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Str;

class AllModulesSeeder extends Seeder
{
    /**
     * Importa todos os 12 módulos formatados para o banco de dados
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando importação de todos os módulos A1-B1');
        
        // Encontrar ou criar o curso
        $course = Course::firstOrCreate(
            ['slug' => 'italiano-completo-a1-b1'],
            [
                'title' => 'Italiano Completo A1-B1',
                'description' => 'Curso completo de italiano do nível iniciante (A1) ao intermediário (B1). Aprenda gramática, vocabulário e pratique com exercícios baseados no exame CILS.',
                'level' => 'A1',
                'image_url' => null,
            ]
        );
        
        $this->command->info("✅ Curso: {$course->title} (ID: {$course->id})");
        
        // Encontrar ou criar categoria
        $category = Category::firstOrCreate(
            ['slug' => 'gramatica'],
            [
                'name' => 'Gramática',
                'description' => 'Gramática italiana essencial'
            ]
        );
        
        // Listar todos os módulos formatados
        $modulesPath = storage_path('app/imports/modules_formatted');
        $moduleFiles = glob($modulesPath . '/modulo_*_formatted.json');
        
        sort($moduleFiles); // Ordenar por nome
        
        $this->command->info("\n📚 Encontrados " . count($moduleFiles) . " módulos para importar\n");
        
        $totalExercises = 0;
        $totalTime = 0;
        
        foreach ($moduleFiles as $index => $filePath) {
            $moduleData = json_decode(file_get_contents($filePath), true);
            
            $order = $index + 1;
            $slug = Str::slug($moduleData['module_name']);
            
            $this->command->info("📖 [{$order}/12] Importando: {$moduleData['module_name']}");
            
            // Criar lição
            $lesson = Lesson::create([
                'course_id' => $course->id,
                'title' => $moduleData['module_name'],
                'slug' => $slug,
                'content_italian' => $moduleData['content_italian'],
                'content_portuguese' => $moduleData['content_portuguese'],
                'order' => $order,
                'estimated_time' => $moduleData['estimated_time'],
                'difficulty' => $moduleData['difficulty'],
            ]);
            
            // Criar exercícios (se houver)
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
                    } elseif ($exerciseData['question_type'] === 'fill_in_blank') {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => $exerciseData['correct_answer'],
                            'is_correct' => true,
                            'order' => 1,
                        ]);
                    }
                    
                    $exercisesCount++;
                }
            }
            
            $totalExercises += $exercisesCount;
            $totalTime += $moduleData['estimated_time'];
            
            $this->command->info("  ✅ Lição ID: {$lesson->id} | {$exercisesCount} exercícios | {$moduleData['estimated_time']}min");
        }
        
        $this->command->info("\n🎉 Importação concluída com sucesso!");
        $this->command->info("📊 Estatísticas:");
        $this->command->info("   • Curso: {$course->title}");
        $this->command->info("   • Total de lições: " . count($moduleFiles));
        $this->command->info("   • Total de exercícios: {$totalExercises}");
        $this->command->info("   • Tempo total estimado: {$totalTime} minutos (" . round($totalTime / 60, 1) . " horas)");
        $this->command->info("\n🌐 Acesse: http://localhost:3000/courses/{$course->id}");
    }
}
