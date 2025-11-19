<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FillInBlankQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exam = \App\Models\Exam::find(2); // CILS A2
        $grammarCategory = \App\Models\Category::where('slug', 'gramatica')->first();
        $vocabularyCategory = \App\Models\Category::where('slug', 'vocabulario')->first();

        // Questão 1: Preencher lacuna - Artigo
        $question1 = \App\Models\Question::create([
            'exam_id' => $exam->id,
            'category_id' => $grammarCategory->id,
            'question_text' => 'Complete com o artigo correto: ___ libro è molto interessante.',
            'question_type' => 'fill_in_blank',
            'difficulty' => 2,
            'context' => 'Artigos definidos em italiano',
            'order' => 3
        ]);

        \App\Models\Answer::create([
            'question_id' => $question1->id,
            'answer_text' => 'il',
            'is_correct' => true,
            'order' => 1
        ]);

        // Questão 2: Preencher lacuna - Verbo
        $question2 = \App\Models\Question::create([
            'exam_id' => $exam->id,
            'category_id' => $grammarCategory->id,
            'question_text' => 'Complete com o verbo "essere" conjugado: Io ___ brasiliano.',
            'question_type' => 'fill_in_blank',
            'difficulty' => 1,
            'context' => 'Verbo essere (ser/estar) no presente',
            'order' => 4
        ]);

        \App\Models\Answer::create([
            'question_id' => $question2->id,
            'answer_text' => 'sono',
            'is_correct' => true,
            'order' => 1
        ]);

        // Questão 3: Preencher lacuna - Vocabulário
        $question3 = \App\Models\Question::create([
            'exam_id' => $exam->id,
            'category_id' => $vocabularyCategory->id,
            'question_text' => 'Complete a palavra: Uma cor do céu é ___ (azul em italiano)',
            'question_type' => 'fill_in_blank',
            'difficulty' => 1,
            'context' => null,
            'order' => 5
        ]);

        \App\Models\Answer::create([
            'question_id' => $question3->id,
            'answer_text' => 'azzurro',
            'is_correct' => true,
            'order' => 1
        ]);

        // Questão 4: Verdadeiro ou Falso
        $question4 = \App\Models\Question::create([
            'exam_id' => $exam->id,
            'category_id' => $grammarCategory->id,
            'question_text' => 'O plural de "ragazzo" é "ragazzi"',
            'question_type' => 'true_false',
            'difficulty' => 2,
            'context' => 'Formação de plural em italiano',
            'order' => 6
        ]);

        \App\Models\Answer::create([
            'question_id' => $question4->id,
            'answer_text' => 'true',
            'is_correct' => true,
            'order' => 1
        ]);

        \App\Models\Answer::create([
            'question_id' => $question4->id,
            'answer_text' => 'false',
            'is_correct' => false,
            'order' => 2
        ]);

        $this->command->info('Questões de preencher lacuna e verdadeiro/falso adicionadas com sucesso!');
    }
}
