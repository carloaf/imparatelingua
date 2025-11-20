<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

class LessonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar curso "Italiano Básico 2025"
        $course = Course::create([
            'title' => 'Italiano Básico 2025',
            'slug' => 'italiano-basico-2025',
            'description' => 'Curso completo de italiano desde o alfabeto até estruturas intermediárias. Ideal para preparação CILS B1.',
            'level' => 'A1',
            'image_url' => null,
            'is_active' => true,
            'order' => 1
        ]);

        // Lição 1: O Alfabeto
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lição 1: O Alfabeto e a Pronúncia de C e G',
            'slug' => 'alfabeto-e-pronuncia-c-g',
            'content_italian' => "# L'alfabeto italiano\n\n## Le lettere\n\nA, B, C, D, E, F, G, H, I, L, M, N, O, P, Q, R, S, T, U, V, Z\n\n**Lettere Straniere:** J, K, W, X, Y\n\nTutte le consonanti, ad eccezione della \"Q\" e dell' \"H\", possono essere doppie.\nEsempi: Mamma, Tetto, Palla, Tassa, Anno, Tappo\n\n## Il suono delle lettere C e G\n\n**C – \"ci\" e \"ce\" hanno un suono dolce** / tchi – tche /\n**C – \"ca\", \"co\", \"cu\" hanno un suono duro** / ka – ko – ku /\n\n**G – \"gi\" e \"ge\" hanno un suono dolce** / dji – dje /\n**G – \"ga\", \"go\", \"gu\" hanno un suono duro** / ga – go – gu /\n\n### Pratique:\nCaffè, Piacere, Spaghetti, Gatto, Ciao, Cinema, Gelato, Giornale, Cane, Medico, Cena, Fungo, Cuore, Guardia, Pagina, Chiesa",
            'content_portuguese' => "# Comentários do Professor\n\n## O Alfabeto\n\nO alfabeto italiano tem 21 letras. As 5 \"letras estrangeiras\" (J, K, W, X, Y) aparecem apenas em palavras de origem estrangeira.\n\n## Consoantes Duplas\n\nA pronúncia das consoantes duplas é muito importante! Você deve \"segurar\" o som da consoante por mais tempo.\n\nExemplo:\n- **pala** (pá) - som rápido\n- **palla** (bola) - som prolongado\n\n## A Regra de Ouro de C e G\n\n### Som \"Doce\" (Suave)\n\nQuando C e G vêm antes de E ou I:\n- **CE** (tche) - Ex: Cena (jantar)\n- **CI** (tchi) - Ex: Ciao (olá)\n- **GE** (dje) - Ex: Gelato (sorvete)\n- **GI** (dji) - Ex: Gioco (jogo)\n\n### Som \"Duro\" (Forte)\n\nQuando C e G vêm antes de A, O, U ou H:\n- **CA, CO, CU** (K) - Ex: Casa, Cosa, Cuore\n- **GA, GO, GU** (G) - Ex: Gatto, Pago\n- **CHE, CHI** (K) - Ex: Macchina\n- **GHE, GHI** (G) - Ex: Spaghetti",
            'exercises' => json_encode([
                [
                    'type' => 'pronunciation',
                    'question' => 'Pratique a pronúncia das seguintes palavras:',
                    'correct_answer' => 'Caffè (ka-fè), Ciao (tchao), Gelato (dge-lato), Gatto (gatto), Cena (tchena)'
                ],
                [
                    'type' => 'fill_blank',
                    'question' => 'Complete: La lettera CI davanti a "e" e "i" ha un suono _____',
                    'options' => ['dolce', 'duro', 'muto', 'nasale'],
                    'correct_answer' => 'dolce'
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Come si pronuncia la parola "Gelato"?',
                    'options' => ['ghe-lato', 'dge-lato', 'je-lato', 'ke-lato'],
                    'correct_answer' => 'dge-lato'
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Quale parola ha il suono duro di C?',
                    'options' => ['Ciao', 'Cinema', 'Casa', 'Cena'],
                    'correct_answer' => 'Casa'
                ]
            ]),
            'lesson_type' => 'pronunciation',
            'difficulty' => 1,
            'estimated_time' => 30,
            'order' => 1
        ]);

        // Lição 2: Saudações
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lição 2: Saudações e Apresentações',
            'slug' => 'saudacoes-apresentacoes',
            'content_italian' => "# Come si saluta?\n\n## Saudações ao longo do dia\n\nIn modo informale si può usare **CIAO** sia per quando si arriva per quando si va via.\n\n**Formale:**\n- La mattina: **Buongiorno**\n- Il pomeriggio: **Buongiorno** / **Buona sera**\n- La sera: **Buona sera**\n- La notte (despedida): **Buona notte**\n\n## Despedidas\n- Arrivederci\n- A presto\n- A domani\n- Ci vediamo\n\n## Il verbo CHIAMARSI\n\n| Pronome | Conjugação |\n|---------|------------|\n| io | mi chiamo |\n| Tu | ti chiami |\n| Lui/lei | si chiama |\n| Noi | ci chiamiamo |\n| Voi | vi chiamate |\n| Loro | si chiamano |\n\n**Informale:** Come ti chiami?\n**Formale:** Come si chiama?\n\n**Risposta:** Mi chiamo [nome]",
            'content_portuguese' => "# Comentários do Professor\n\n## Saudações (Saluti)\n\n- **Ciao:** \"Oi\" e \"tchau\" informal\n- **Buongiorno:** \"Bom dia\" (formal e informal)\n- **Buona sera:** \"Boa noite\" ao chegar\n- **Buona notte:** \"Boa noite\" ao se despedir para dormir\n- **Arrivederci:** \"Até logo\" (formal)\n\n## Verbo chiamarsi (chamar-se)\n\nPrimeiro verbo reflexivo. As partículas **mi, ti, si, ci, vi, si** são essenciais.\n\n## TU vs. LEI (Informal vs. Formal)\n\n**Tu (informal):** Com amigos, família, pessoas da mesma idade\n- Pergunta: \"Come **ti** chiami?\"\n\n**Lei (formal):** Com pessoas mais velhas, situações profissionais, autoridades\n- Pergunta: \"Come **si** chiama?\"\n\n⚠️ **CILS B1:** Saber usar Tu e Lei corretamente é essencial!",
            'exercises' => json_encode([
                [
                    'type' => 'scenario',
                    'question' => 'Come saluti quando entri in una panetteria la mattina?',
                    'options' => ['Ciao', 'Buongiorno', 'Buona sera', 'Buona notte'],
                    'correct_answer' => 'Buongiorno'
                ],
                [
                    'type' => 'scenario',
                    'question' => 'Come saluti un amico che incontri per un caffè?',
                    'options' => ['Buongiorno', 'Arrivederci', 'Ciao', 'Buona sera'],
                    'correct_answer' => 'Ciao'
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Qual è la forma corretta? "Come ___ chiami?"',
                    'options' => ['ti', 'si', 'mi', 'ci'],
                    'correct_answer' => 'ti'
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Formale: "Come ___ chiama, signora?"',
                    'options' => ['ti', 'si', 'mi', 'vi'],
                    'correct_answer' => 'si'
                ]
            ]),
            'lesson_type' => 'vocabulary',
            'difficulty' => 1,
            'estimated_time' => 25,
            'order' => 2
        ]);

        // Lição 3: Verbos ESSERE e AVERE
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lição 3: Verbos Essenciais - ESSERE e AVERE',
            'slug' => 'verbos-essere-avere',
            'content_italian' => "# Verbi Essenziali\n\n## VERBO ESSERE (Ser/Estar)\n\n| Pronome | Conjugação |\n|---------|------------|\n| IO | SONO |\n| TU | SEI |\n| LUI/LEI | È |\n| NOI | SIAMO |\n| VOI | SIETE |\n| LORO | SONO |\n\n**Uso:** ESSERE + AGGETTIVO\n\nEsempi:\n- Io sono alto\n- Tu sei simpatico\n- Lei è bella\n- Noi siamo stanchi\n\n## VERBO AVERE (Ter)\n\n| Pronome | Conjugação |\n|---------|------------|\n| IO | HO |\n| TU | HAI |\n| LUI/LEI | HA |\n| NOI | ABBIAMO |\n| VOI | AVETE |\n| LORO | HANNO |\n\n**Uso:** AVERE + SOSTANTIVO\n\nEsempi:\n- Io ho 29 anni\n- Tu hai fame\n- Noi abbiamo sete\n\n## Espressioni com AVERE\n- Avere fame (fome)\n- Avere sete (sede)\n- Avere sonno (sono)\n- Avere caldo (calor)\n- Avere freddo (frio)\n- Avere paura (medo)\n- Avere fretta (pressa)",
            'content_portuguese' => "# Comentários do Professor\n\n## O Verbo ESSERE (Ser/Estar)\n\n**Uso:** Descrever qualidades, identidade, profissão, nacionalidade.\n\n**Estrutura:** Soggetto + ESSERE + Aggettivo\n\n### Concordância\n\nO adjetivo concorda em gênero e número:\n- **Io sono stanco** (homem)\n- **Io sono stanca** (mulher)\n- **Noi siamo stanchi** (grupo misto)\n- **Noi siamo stanche** (só mulheres)\n\n## O Verbo AVERE (Ter)\n\n**Uso:** Posse e sensações físicas.\n\n### ⚠️ Grande Diferença\n\nEm italiano usamos AVERE onde em português usamos \"estar com\":\n\n- **Avere fame:** Estar com fome\n- **Avere sete:** Estar com sede\n- **Avere sonno:** Estar com sono\n- **Avere caldo:** Estar com calor\n- **Avere freddo:** Estar com frio\n- **Avere paura:** Ter medo\n- **Avere fretta:** Estar com pressa\n- **Avere ... anni:** Ter ... anos (sempre AVERE!)",
            'exercises' => json_encode([
                [
                    'type' => 'multiple_choice',
                    'question' => 'Io ___ italiano.',
                    'options' => ['sono', 'ho', 'sei', 'hai'],
                    'correct_answer' => 'sono'
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Tu ___ 30 anni?',
                    'options' => ['sei', 'hai', 'sono', 'ho'],
                    'correct_answer' => 'hai'
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Noi ___ fretta.',
                    'options' => ['abbiamo', 'siamo', 'avete', 'sono'],
                    'correct_answer' => 'abbiamo'
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Loro ___ stanchi.',
                    'options' => ['sono', 'hanno', 'sei', 'ho'],
                    'correct_answer' => 'sono'
                ],
                [
                    'type' => 'multiple_choice',
                    'question' => 'Quale espressione usa AVERE?',
                    'options' => ['Essere stanco', 'Avere fame', 'Essere felice', 'Essere italiano'],
                    'correct_answer' => 'Avere fame'
                ]
            ]),
            'lesson_type' => 'grammar',
            'difficulty' => 2,
            'estimated_time' => 40,
            'order' => 3
        ]);
    }
}
