<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\Storage;

class ModuloPilotoSeeder extends Seeder
{
    /**
     * Seeder piloto para importar o Módulo 1 (Alfabeto e Fonética)
     * formatado e revisado manualmente.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando importação do Módulo Piloto (Alfabeto e Fonética)...');

        // 1. Buscar ou criar o curso "Italiano Completo A1-B1"
        $course = Course::firstOrCreate(
            ['slug' => 'italiano-completo-a1-b1'],
            [
                'title' => 'Italiano Completo A1-B1',
                'description' => 'Curso completo de italiano do nível iniciante (A1) ao intermediário (B1). Aprenda gramática, vocabulário e pratique com exercícios baseados no exame CILS.',
                'level' => 'A1',
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("✅ Curso encontrado/criado: {$course->title} (ID: {$course->id})");

        // 2. Carregar JSON do módulo formatado
        $jsonPath = storage_path('app/imports/modules_formatted/modulo_01_alfabeto_formatted.json');
        
        if (!file_exists($jsonPath)) {
            $this->command->error("❌ Arquivo não encontrado: {$jsonPath}");
            return;
        }

        $moduleData = json_decode(file_get_contents($jsonPath), true);
        
        if (!$moduleData) {
            $this->command->error('❌ Erro ao decodificar JSON do módulo');
            return;
        }

        $this->command->info("📖 Módulo carregado: {$moduleData['module_name']}");

        // 3. Criar a lição
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => $moduleData['module_name'],
            'slug' => \Illuminate\Support\Str::slug($moduleData['module_name']),
            'content_italian' => $moduleData['content_italian'],
            'content_portuguese' => $moduleData['content_portuguese'] ?? '',
            'lesson_type' => 'theory',
            'difficulty' => $moduleData['difficulty'],
            'estimated_time' => $moduleData['estimated_time'],
            'order' => 1, // Primeiro módulo
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("✅ Lição criada: {$lesson->title} (ID: {$lesson->id})");

        // 4. Importar exercícios
        $exerciseCount = 0;
        $category = \App\Models\Category::firstOrCreate(
            ['name' => 'Gramática'],
            [
                'slug' => 'gramatica',
                'description' => 'Exercícios de gramática italiana'
            ]
        );

        foreach ($moduleData['exercises'] as $exerciseData) {
            // Criar a questão
            $question = Question::create([
                'exam_id' => null, // Não associado a exame específico
                'category_id' => $category->id,
                'lesson_id' => $lesson->id, // Associar à lição
                'question_text' => $exerciseData['question'],
                'question_type' => $exerciseData['type'],
                'difficulty' => $moduleData['difficulty'],
                'context' => $exerciseData['explanation'] ?? null,
                'order' => $exerciseData['number'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Criar respostas (para múltipla escolha)
            if ($exerciseData['type'] === 'multiple_choice' && isset($exerciseData['options'])) {
                $answerOrder = 1;
                foreach ($exerciseData['options'] as $option) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $option['text'],
                        'is_correct' => $option['is_correct'],
                        'order' => $answerOrder++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            // Para fill_in_blank
            elseif ($exerciseData['type'] === 'fill_in_blank') {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $exerciseData['correct_answer'],
                    'is_correct' => true,
                    'order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Adicionar alternativas se existirem
                if (isset($exerciseData['alternatives'])) {
                    $order = 2;
                    foreach ($exerciseData['alternatives'] as $alt) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => $alt,
                            'is_correct' => true, // Alternativas também corretas
                            'order' => $order++,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            $exerciseCount++;
            $this->command->info("  ✅ Exercício {$exerciseCount}: {$exerciseData['question']}");
        }

        // 5. Resumo final
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('✅ IMPORTAÇÃO CONCLUÍDA COM SUCESSO!');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info("📚 Curso: {$course->title}");
        $this->command->info("📖 Lição: {$lesson->title}");
        $this->command->info("📝 Nível: {$lesson->level}");
        $this->command->info("⭐ Dificuldade: {$lesson->difficulty}/5");
        $this->command->info("⏱️  Tempo estimado: {$lesson->estimated_time} minutos");
        $this->command->info("✏️  Exercícios importados: {$exerciseCount}");
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🌐 Acesse a lição no frontend:');
        $this->command->info("   http://localhost:5173/lessons/{$lesson->id}");
        $this->command->info('');
    }
}
