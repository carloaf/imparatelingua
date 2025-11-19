<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exam;
use App\Models\Category;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImportQuestionsFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'questions:import {file} {--replace}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa questões de um arquivo JSON. Use --replace para substituir exame existente.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = $this->argument('file');
        $replace = $this->option('replace');

        // Verificar se arquivo existe
        $filepath = "imports/{$filename}";
        if (!Storage::exists($filepath)) {
            $this->error("Arquivo não encontrado: storage/app/{$filepath}");
            $this->info("Coloque seu arquivo JSON em: storage/app/imports/");
            return Command::FAILURE;
        }

        // Ler arquivo JSON
        $jsonContent = Storage::get($filepath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Erro ao decodificar JSON: " . json_last_error_msg());
            return Command::FAILURE;
        }

        // Validar estrutura
        $validator = Validator::make($data, [
            'exam' => 'required|array',
            'exam.name' => 'required|string',
            'exam.level' => 'required|in:A1,A2,B1,B2,C1,C2',
            'exam.year' => 'required|integer',
            'exam.is_official' => 'nullable|boolean',
            'exam.session' => 'nullable|string|in:Giugno,Dicembre',
            'exam.exam_code' => 'nullable|string',
            'exam.source_url' => 'nullable|url',
            'questions' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            $this->error("Erro de validação:");
            foreach ($validator->errors()->all() as $error) {
                $this->error("  - {$error}");
            }
            return Command::FAILURE;
        }

        $this->info("📖 Importando questões do arquivo: {$filename}");
        $this->info("📚 Exame: {$data['exam']['name']} ({$data['exam']['level']})");
        $this->info("📝 Questões: " . count($data['questions']));

        DB::beginTransaction();
        try {
            // Verificar se exame já existe (buscar por exam_code se disponível)
            $exam = null;
            if (isset($data['exam']['exam_code'])) {
                $exam = Exam::where('exam_code', $data['exam']['exam_code'])->first();
            }
            
            if (!$exam) {
                $exam = Exam::where('name', $data['exam']['name'])
                            ->where('level', $data['exam']['level'])
                            ->where('year', $data['exam']['year'])
                            ->first();
            }

            if ($exam && !$replace) {
                $this->error("❌ Exame já existe! Use --replace para substituir.");
                DB::rollBack();
                return Command::FAILURE;
            }

            if ($exam && $replace) {
                $this->warn("🔄 Substituindo exame existente: {$exam->name}");
                // Deletar questões antigas (cascade deletará respostas)
                $exam->questions()->delete();
                // Atualizar dados do exame
                $exam->update($data['exam']);
                $this->info("✅ Exame atualizado");
            } else {
                // Criar novo exame
                $exam = Exam::create($data['exam']);
                $this->info("✅ Exame criado: {$exam->name}");
            }

            $progressBar = $this->output->createProgressBar(count($data['questions']));
            $progressBar->start();

            $questionsCreated = 0;
            $answersCreated = 0;

            foreach ($data['questions'] as $questionData) {
                // Buscar categoria por slug ou nome
                $category = Category::where('slug', $questionData['category'])
                    ->orWhere('name', $questionData['category'])
                    ->first();
                
                if (!$category) {
                    $this->newLine();
                    $this->warn("⚠️  Categoria '{$questionData['category']}' não encontrada, pulando questão.");
                    continue;
                }

                // Criar questão
                $question = Question::create([
                    'exam_id' => $exam->id,
                    'category_id' => $category->id,
                    'question_text' => $questionData['question_text'],
                    'question_type' => $questionData['question_type'],
                    'difficulty' => $questionData['difficulty'],
                    'context' => $questionData['context'] ?? null,
                    'explanation' => $questionData['explanation'] ?? null,
                    'order' => $questionData['order'] ?? 0,
                ]);

                $questionsCreated++;

                // Criar respostas
                if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                    foreach ($questionData['answers'] as $answerData) {
                        // Verificar se tem a estrutura mínima necessária
                        if (!isset($answerData['answer_text'])) {
                            continue;
                        }
                        
                        Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => $answerData['answer_text'],
                            'is_correct' => $answerData['is_correct'] ?? false,
                            'justification' => $answerData['justification'] ?? null,
                            'order' => $answerData['order'] ?? 0,
                        ]);
                        $answersCreated++;
                    }
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            DB::commit();

            $this->info("✅ Importação concluída com sucesso!");
            $this->table(
                ['Métrica', 'Quantidade'],
                [
                    ['Exame', $exam->name],
                    ['Questões criadas', $questionsCreated],
                    ['Respostas criadas', $answersCreated],
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Erro durante importação: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
