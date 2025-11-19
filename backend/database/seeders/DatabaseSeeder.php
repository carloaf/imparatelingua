<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário de teste
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Criar categorias
        $grammar = \App\Models\Category::create([
            'name' => 'Gramática',
            'slug' => 'gramatica',
            'description' => 'Questões sobre estruturas gramaticais do italiano',
        ]);

        $vocabulary = \App\Models\Category::create([
            'name' => 'Vocabulário',
            'slug' => 'vocabulario',
            'description' => 'Questões sobre palavras e expressões italianas',
        ]);

        $reading = \App\Models\Category::create([
            'name' => 'Interpretação de Texto',
            'slug' => 'interpretacao',
            'description' => 'Questões de compreensão textual',
        ]);

        // Criar exames
        $examA1 = \App\Models\Exam::create([
            'name' => 'CILS A1',
            'level' => 'A1',
            'year' => 2024,
            'description' => 'Certificação de Italiano como Língua Estrangeira - Nível A1',
        ]);

        $examA2 = \App\Models\Exam::create([
            'name' => 'CILS A2',
            'level' => 'A2',
            'year' => 2024,
            'description' => 'Certificação de Italiano como Língua Estrangeira - Nível A2',
        ]);

        // Criar questões de exemplo
        
        // Questão 1 - Gramática A1
        $q1 = \App\Models\Question::create([
            'exam_id' => $examA1->id,
            'category_id' => $grammar->id,
            'question_text' => 'Complete a frase: Io ____ italiano.',
            'question_type' => 'multiple_choice',
            'difficulty' => 1,
            'context' => 'Verbo essere (ser/estar) no presente',
            'order' => 1,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q1->id,
            'answer_text' => 'sono',
            'is_correct' => true,
            'order' => 1,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q1->id,
            'answer_text' => 'sei',
            'is_correct' => false,
            'order' => 2,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q1->id,
            'answer_text' => 'è',
            'is_correct' => false,
            'order' => 3,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q1->id,
            'answer_text' => 'siamo',
            'is_correct' => false,
            'order' => 4,
        ]);

        // Questão 2 - Vocabulário A1
        $q2 = \App\Models\Question::create([
            'exam_id' => $examA1->id,
            'category_id' => $vocabulary->id,
            'question_text' => 'Como se diz "bom dia" em italiano?',
            'question_type' => 'multiple_choice',
            'difficulty' => 1,
            'order' => 2,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q2->id,
            'answer_text' => 'Buongiorno',
            'is_correct' => true,
            'order' => 1,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q2->id,
            'answer_text' => 'Buonasera',
            'is_correct' => false,
            'order' => 2,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q2->id,
            'answer_text' => 'Buonanotte',
            'is_correct' => false,
            'order' => 3,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q2->id,
            'answer_text' => 'Ciao',
            'is_correct' => false,
            'order' => 4,
        ]);

        // Questão 3 - Gramática A2
        $q3 = \App\Models\Question::create([
            'exam_id' => $examA2->id,
            'category_id' => $grammar->id,
            'question_text' => 'Complete: Ieri io ____ al cinema.',
            'question_type' => 'multiple_choice',
            'difficulty' => 2,
            'context' => 'Verbo andare (ir) no passato prossimo',
            'order' => 1,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q3->id,
            'answer_text' => 'sono andato',
            'is_correct' => true,
            'order' => 1,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q3->id,
            'answer_text' => 'ho andato',
            'is_correct' => false,
            'order' => 2,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q3->id,
            'answer_text' => 'andavo',
            'is_correct' => false,
            'order' => 3,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q3->id,
            'answer_text' => 'vado',
            'is_correct' => false,
            'order' => 4,
        ]);

        // Questão 4 - Interpretação A2
        $q4 = \App\Models\Question::create([
            'exam_id' => $examA2->id,
            'category_id' => $reading->id,
            'question_text' => 'De acordo com o texto, Maria gosta de:',
            'question_type' => 'multiple_choice',
            'difficulty' => 2,
            'context' => 'Texto: "Maria è una ragazza italiana. Le piace molto leggere libri e ascoltare musica. Nel tempo libero va spesso al parco con i suoi amici."',
            'order' => 2,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q4->id,
            'answer_text' => 'Ler livros e ouvir música',
            'is_correct' => true,
            'order' => 1,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q4->id,
            'answer_text' => 'Cozinhar e dançar',
            'is_correct' => false,
            'order' => 2,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q4->id,
            'answer_text' => 'Praticar esportes',
            'is_correct' => false,
            'order' => 3,
        ]);

        \App\Models\Answer::create([
            'question_id' => $q4->id,
            'answer_text' => 'Ver televisão',
            'is_correct' => false,
            'order' => 4,
        ]);

        echo "✓ Database seeded successfully!\n";
        echo "  - 1 usuário de teste criado\n";
        echo "  - 3 categorias criadas\n";
        echo "  - 2 exames criados (A1 e A2)\n";
        echo "  - 4 questões com respostas criadas\n";
    }
}
