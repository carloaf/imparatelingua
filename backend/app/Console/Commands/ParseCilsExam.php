<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ParseCilsExam extends Command
{
    protected $signature = 'cils:parse {textfile} {--output= : Output JSON filename} {--category= : Specific category to parse}';
    
    protected $description = 'Parse extracted CILS exam text and generate structured JSON';

    private $currentCategory = null;

    public function handle()
    {
        $textfile = $this->argument('textfile');
        $filepath = storage_path('app/imports/' . $textfile);

        if (!file_exists($filepath)) {
            $this->error("❌ Arquivo não encontrado: {$textfile}");
            return 1;
        }

        $this->info("📖 Lendo arquivo: {$textfile}");
        $text = file_get_contents($filepath);

        // Detectar metadados do exame
        $examData = $this->extractExamMetadata($text);
        $this->info("📋 Exame: {$examData['name']} - {$examData['level']} - {$examData['session']} {$examData['year']}");

        // Dividir texto por seções
        $sections = $this->splitIntoSections($text);
        $this->info("📚 Seções encontradas: " . count($sections));

        // Processar cada seção
        $allQuestions = [];
        $categoryFilter = $this->option('category');

        foreach ($sections as $sectionName => $sectionText) {
            if ($categoryFilter && stripos($sectionName, $categoryFilter) === false) {
                continue;
            }

            $this->info("🔍 Processando: {$sectionName}");
            $questions = $this->parseSection($sectionName, $sectionText);
            
            if (!empty($questions)) {
                $allQuestions = array_merge($allQuestions, $questions);
                $this->info("  ✅ {$sectionName}: " . count($questions) . " questões extraídas");
            }
        }

        if (empty($allQuestions)) {
            $this->error("❌ Nenhuma questão foi extraída.");
            return 1;
        }

        // Gerar JSON
        $output = [
            'exam' => $examData,
            'questions' => $allQuestions
        ];

        // Salvar arquivo
        $outputFile = $this->option('output') ?: 
            pathinfo($textfile, PATHINFO_FILENAME) . '_parsed.json';
        
        $outputPath = storage_path('app/imports/' . $outputFile);
        file_put_contents($outputPath, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->newLine();
        $this->info("✅ JSON gerado com sucesso!");
        $this->info("📄 Arquivo: {$outputFile}");
        $this->info("📊 Total de questões: " . count($allQuestions));
        
        $this->newLine();
        $this->warn("⚠️  IMPORTANTE: Revise o JSON gerado!");
        $this->warn("   - Marque as respostas corretas (is_correct: true)");
        $this->warn("   - Adicione justificativas");
        $this->warn("   - Verifique os tipos de questão");

        return 0;
    }

    private function extractExamMetadata($text)
    {
        $metadata = [
            'name' => 'CILS Exam',
            'level' => 'B1',
            'year' => 2017,
            'session' => 'Dicembre',
            'description' => 'Certificazione di Italiano come Lingua Straniera',
            'is_official' => true,
            'exam_code' => 'CILS_B1_DIC_2017'
        ];

        // Extrair nível
        if (preg_match('/Livello:\s*(\w+[-\s]?\w*)/i', $text, $matches)) {
            $level = trim($matches[1]);
            $metadata['level'] = preg_match('/B1|UNO-B1/i', $level) ? 'B1' : $level;
            $metadata['name'] = "CILS {$metadata['level']}";
        }

        // Extrair sessão
        if (preg_match('/Sessione:\s*(\w+)\s+(\d{4})/i', $text, $matches)) {
            $metadata['session'] = trim($matches[1]);
            $metadata['year'] = (int)$matches[2];
            $metadata['name'] .= " - {$metadata['session']} {$metadata['year']}";
        }

        // Gerar código do exame
        $sessionCode = $metadata['session'] === 'Dicembre' ? 'DIC' : 'GIU';
        $metadata['exam_code'] = "CILS_{$metadata['level']}_{$sessionCode}_{$metadata['year']}";

        return $metadata;
    }

    private function splitIntoSections($text)
    {
        $sections = [];
        
        // Regex mais abrangente para capturar todas as seções (aceita - ou --)
        // Captura até a próxima seção OU até "Test di produzione"
        preg_match_all('/((?:Ascolto|Comprensione della lettura|Analisi delle strutture di comunicazione)\s*-{1,2}\s*Prova\s+n\.\s*\d+)(.+?)(?=(?:Ascolto|Comprensione della lettura|Analisi delle strutture di comunicazione)\s*-{1,2}\s*Prova\s+n\.\s*\d+|Test\s+di\s+produzione|$)/is', $text, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $sectionName = trim(preg_replace('/\s+/', ' ', $match[1]));
            $sectionContent = trim($match[2]);
            
            if (!empty($sectionContent)) {
                $sections[$sectionName] = $sectionContent;
            }
        }
        
        return $sections;
    }

    private function parseSection($sectionName, $text)
    {
        $this->currentCategory = $this->detectCategory($sectionName);
        
        // Detectar número da prova
        preg_match('/Prova\s+n\.\s*(\d+)/i', $sectionName, $provaMatch);
        $provaNumber = $provaMatch[1] ?? 0;
        
        // Detectar tipo de prova baseado na seção E número da prova
        if (stripos($sectionName, 'ascolto') !== false) {
            if ($provaNumber == 3) {
                return $this->parseMultipleSelection($text, 'Ascolto Prova 3');
            }
            return $this->parseMultipleChoice($text, 'Ascolto');
        } 
        
        if (stripos($sectionName, 'comprensione') !== false || stripos($sectionName, 'lettura') !== false) {
            if ($provaNumber == 2) {
                return $this->parseMultipleSelection($text, 'Comprensione Prova 2');
            } elseif ($provaNumber == 3) {
                return $this->parseOrdering($text);
            }
            return $this->parseMultipleChoice($text, 'Comprensione');
        }
        
        if (stripos($sectionName, 'analisi') !== false || stripos($sectionName, 'strutture') !== false) {
            if ($provaNumber == 1) {
                return $this->parseFillInBlanksArticles($text);
            } elseif ($provaNumber == 2) {
                return $this->parseFillInBlanksVerbs($text);
            } elseif ($provaNumber == 3) {
                return $this->parseMultipleChoiceCloze($text);
            } elseif ($provaNumber == 4) {
                // Extrair instruções para Prova 4
                $instructions = '';
                if (preg_match('/^(.+?)(?=\n\s*\d+\.)/s', $text, $instrMatch)) {
                    $instructions = trim($instrMatch[1]);
                }
                return $this->parseMatchingCommunication($text, $instructions);
            }
        }
        
        return [];
    }

    private function detectCategory($sectionName)
    {
        $categories = [
            'ascolto' => 'Ascolto',
            'comprensione' => 'Comprensione della Lettura',
            'lettura' => 'Comprensione della Lettura',
            'analisi' => 'Analisi delle Strutture di Comunicazione',
            'strutture' => 'Analisi delle Strutture di Comunicazione',
            'produzione scritta' => 'Produzione Scritta',
            'produzione orale' => 'Produzione Orale'
        ];
        
        foreach ($categories as $key => $category) {
            if (stripos($sectionName, $key) !== false) {
                return $category;
            }
        }
        
        return 'Generale';
    }

    private function parseMultipleChoice($text, $section)
    {
        $questions = [];
        
        // Extrair instruções
        $instructions = '';
        if (preg_match('/^(.+?)(?=\n\s*\d+\.)/s', $text, $instrMatch)) {
            $instructions = trim($instrMatch[1]);
        }
        
        // Extrair questões numeradas com opções A-D
        preg_match_all('/(\d+)\.\s*(.+?)(?=\n\s*\d+\.|$)/s', $text, $questMatches, PREG_SET_ORDER);
        
        foreach ($questMatches as $qMatch) {
            $questionNumber = $qMatch[1];
            $questionBlock = $qMatch[2];
            
            // Separar texto da questão das opções
            $lines = explode("\n", $questionBlock);
            $questionText = '';
            $answers = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Aceitar tanto O) quanto C) para opção C
                if (preg_match('/^([A-DO])\)\s*(.+)$/i', $line, $optMatch)) {
                    $letter = $optMatch[1];
                    // Converter O para C
                    if (strtoupper($letter) === 'O') $letter = 'C';
                    
                    $answers[] = [
                        'answer_text' => trim($optMatch[2]),
                        'is_correct' => false,
                        'order' => ord(strtoupper($letter)) - ord('A') + 1,
                        'justification' => null
                    ];
                } else {
                    $questionText .= ' ' . $line;
                }
            }
            
            if (!empty($answers) && count($answers) >= 3) {
                $questions[] = [
                    'category' => $this->currentCategory,
                    'question_text' => trim($questionText),
                    'question_type' => 'multiple_choice',
                    'difficulty' => 3,
                    'context' => $instructions,
                    'order' => (int)$questionNumber,
                    'explanation' => null,
                    'answers' => $answers
                ];
            }
        }
        
        return $questions;
    }
    
    private function parseMultipleSelection($text, $provaName)
    {
        $questions = [];
        
        // Extrair instruções
        $instructions = '';
        if (preg_match('/^(.+?)(?=\n[A-Z]\.)/s', $text, $instrMatch)) {
            $instructions = trim($instrMatch[1]);
        }
        
        // Determinar número de seleções corretas e range de letras
        $numCorrect = 6;
        $maxLetter = 'M';
        
        if (strpos($provaName, 'Comprensione Prova 2') !== false) {
            $numCorrect = 7;
            $maxLetter = 'O';
        }
        
        // Extrair todas as opções (A-M ou A-O)
        preg_match_all('/([A-' . $maxLetter . '])\.\s*(.+?)(?=\n[A-' . $maxLetter . ']\.|$)/s', $text, $optMatches, PREG_SET_ORDER);
        
        $options = [];
        foreach ($optMatches as $opt) {
            $letter = $opt[1];
            $optionText = trim($opt[2]);
            
            $options[] = [
                'answer_text' => $optionText,
                'is_correct' => false,
                'order' => ord($letter) - ord('A') + 1,
                'justification' => null
            ];
        }
        
        if (!empty($options)) {
            $questions[] = [
                'category' => $this->currentCategory,
                'question_text' => "Scegli le {$numCorrect} informazioni presenti nel testo (da A a {$maxLetter}).",
                'question_type' => 'multiple_selection',
                'difficulty' => 3,
                'context' => $instructions,
                'order' => 1,
                'explanation' => null,
                'answers' => $options
            ];
        }
        
        return $questions;
    }
    
    private function parseOrdering($text)
    {
        $questions = [];
        
        // Extrair instruções
        $instructions = '';
        if (preg_match('/^(.+?)(?=\n[A-Z]\.)/s', $text, $instrMatch)) {
            $instructions = trim($instrMatch[1]);
        }
        
        // Extrair todas as partes do texto (A-K para 11 partes)
        preg_match_all('/([A-K])\.\s*(.+?)(?=\n[A-K]\.|$)/s', $text, $partMatches, PREG_SET_ORDER);
        
        $parts = [];
        foreach ($partMatches as $part) {
            $letter = $part[1];
            $partText = trim($part[2]);
            
            // A primeira parte (A) sempre tem ordem 1
            $correctOrder = ($letter === 'A') ? 1 : null;
            
            $parts[] = [
                'answer_text' => $partText,
                'is_correct' => false,
                'order' => ord($letter) - ord('A') + 1,
                'correct_position' => $correctOrder,
                'justification' => null
            ];
        }
        
        if (!empty($parts)) {
            $questions[] = [
                'category' => $this->currentCategory,
                'question_text' => "Ricostruisci il testo mettendo le parti in ordine. La parte A è sempre la numero 1.",
                'question_type' => 'ordering',
                'difficulty' => 4,
                'context' => $instructions,
                'order' => 1,
                'explanation' => null,
                'answers' => $parts
            ];
        }
        
        return $questions;
    }

    private function parseComprensione($text)
    {
        // Extrair instruções
        $instructions = '';
        if (preg_match('/^(.+?)(?=\n\s*\d+\.)/s', $text, $instrMatch)) {
            $instructions = trim($instrMatch[1]);
        }
        
        // Verificar se é tipo "seleção múltipla" (escolher N de M informações)
        if (preg_match('/Scegli\s+le?\s+(\d+)\s+informazioni.*presenti nel testo/is', $text, $selectMatch)) {
            return $this->parseMultipleSelection($text, $instructions);
        }
        
        // Parsing padrão de múltipla escolha
        return $this->parseAscolto($text);
    }

    private function parseAnalisi($text)
    {
        $questions = [];
        
        // Extrair instruções
        $instructions = '';
        if (preg_match('/^(.+?)(?=\n\s*\d+[\.|)])/s', $text, $instrMatch)) {
            $instructions = trim($instrMatch[1]);
        }
        
        // Verificar se é Prova 4 (matching de situações de comunicação)
        if (preg_match('/Scegli per ogni espressione/i', $text)) {
            return $this->parseMatchingCommunication($text, $instructions);
        }
        
        // Verificar se é fill in the blanks (espaços no texto com opções A/B/C/D)
        if (preg_match('/\d+\.\s*\|A\)/m', $text)) {
            return $this->parseFillInTheBlanksMultiple($text, $instructions);
        }
        
        // Verificar se é fill_in_blank simples (artigos, preposições, verbos)
        if (preg_match('/Completa il testo con (gli articoli|le forme dei verbi)/i', $text)) {
            return $this->parseFillInBlankSimple($text, $instructions);
        }
        
        // Parsing padrão de múltipla escolha
        return $this->parseAscolto($text);
    }
    
    private function parseMatchingCommunication($text, $instructions)
    {
        $questions = [];
        
        // Extrair questões numeradas - cada uma tem uma expressão e 4 contextos (A-D)
        preg_match_all('/(\d+)\.\s*(.+?)(?=\n\s*\d+\.|$)/s', $text, $questMatches, PREG_SET_ORDER);
        
        foreach ($questMatches as $qMatch) {
            $questionNumber = $qMatch[1];
            $questionBlock = $qMatch[2];
            
            // Primeira linha é a expressão
            $lines = explode("\n", $questionBlock);
            $expression = '';
            $contexts = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Opções A-D são os contextos/situações
                if (preg_match('/^([A-D])\)\s*(.+)$/i', $line, $optMatch)) {
                    $contexts[] = [
                        'answer_text' => trim($optMatch[2]),
                        'is_correct' => false,
                        'order' => ord(strtoupper($optMatch[1])) - ord('A') + 1,
                        'justification' => null
                    ];
                } else {
                    $expression .= ' ' . $line;
                }
            }
            
            if (!empty($contexts)) {
                $questions[] = [
                    'category' => $this->currentCategory,
                    'question_text' => trim($expression),
                    'question_type' => 'multiple_choice', // Apesar de ser matching, usa mesma estrutura
                    'difficulty' => 3,
                    'context' => $instructions,
                    'order' => (int)$questionNumber,
                    'explanation' => null,
                    'answers' => $contexts
                ];
            }
        }
        
        return $questions;
    }
    
    private function parseFillInTheBlanksMultiple($text, $instructions)
    {
        $questions = [];
        
        // Extrair contexto (texto com lacunas)
        $contextText = '';
        if (preg_match('/^(.+?)(?=\n\s*\d+\.\s*\|)/s', $text, $ctxMatch)) {
            $contextText = trim($ctxMatch[1]);
            // Remover instruções
            $contextText = preg_replace('/^.+?(?=\n\n)/s', '', $contextText);
            $contextText = trim($contextText);
        }
        
        // Extrair lacunas numeradas com opções (formato: 13. |A) opção B) opção C) opção D) opção)
        preg_match_all('/(\d+)\.\s*\|([A-D]\).+?)(?=\n\s*\d+\.\s*\||$)/s', $text, $blankMatches, PREG_SET_ORDER);
        
        foreach ($blankMatches as $bMatch) {
            $blankNumber = $bMatch[1];
            $optionsLine = $bMatch[2];
            
            // Extrair opções A-D
            preg_match_all('/([A-D])\)\s*([^A-D]+?)(?=[A-D]\)|$)/', $optionsLine, $optMatches, PREG_SET_ORDER);
            
            $options = [];
            foreach ($optMatches as $opt) {
                $letter = $opt[1];
                $optText = trim($opt[2]);
                
                $options[] = [
                    'answer_text' => $optText,
                    'is_correct' => false,
                    'order' => ord($letter) - ord('A') + 1,
                    'justification' => null
                ];
            }
            
            if (!empty($options)) {
                $questions[] = [
                    'category' => $this->currentCategory,
                    'question_text' => "Lacuna {$blankNumber}",
                    'question_type' => 'multiple_choice_cloze',
                    'difficulty' => 3,
                    'context' => $contextText,
                    'order' => (int)$blankNumber,
                    'explanation' => null,
                    'answers' => $options
                ];
            }
        }
        
        return $questions;
    }
    
    private function parseFillInBlankSimple($text, $instructions)
    {
        // Este tipo não tem opções pré-definidas - o aluno escreve a resposta
        // Vamos extrair apenas como referência, mas não gerar questões completas
        // pois precisaria de resposta livre (não múltipla escolha)
        
        $this->warn("  ⚠️  Prova de preenchimento livre detectada - requer resposta escrita (não implementado)");
        
        return [];
    }
    
    private function parseFillInBlanksArticles($text)
    {
        $questions = [];
        
        // Extrair instruções
        $instructions = '';
        if (preg_match('/^(.+?)(?=\n\n)/s', $text, $instrMatch)) {
            $instructions = trim($instrMatch[1]);
        }
        
        // 15 lacunas para artigos e preposições (1-15)
        for ($i = 1; $i <= 15; $i++) {
            $questions[] = [
                'category' => $this->currentCategory,
                'question_text' => "Lacuna {$i}: Completa con l'articolo o la preposizione corretta",
                'question_type' => 'fill_in_blank',
                'difficulty' => 3,
                'context' => $instructions . "\n\n" . preg_replace('/\s*@\s*/', ' ___ ', $text),
                'order' => $i,
                'explanation' => null,
                'answers' => [
                    [
                        'answer_text' => '', // Resposta livre
                        'is_correct' => true,
                        'order' => 1,
                        'justification' => null
                    ]
                ]
            ];
        }
        
        return $questions;
    }
    
    private function parseFillInBlanksVerbs($text)
    {
        $questions = [];
        
        // Extrair instruções
        $instructions = '';
        if (preg_match('/^(.+?)(?=\n\n)/s', $text, $instrMatch)) {
            $instructions = trim($instrMatch[1]);
        }
        
        // 20 lacunas para verbos (1-20)
        for ($i = 1; $i <= 20; $i++) {
            $questions[] = [
                'category' => $this->currentCategory,
                'question_text' => "Lacuna {$i}: Completa con la forma corretta del verbo tra parentesi",
                'question_type' => 'fill_in_blank',
                'difficulty' => 3,
                'context' => $instructions . "\n\n" . $text,
                'order' => $i,
                'explanation' => null,
                'answers' => [
                    [
                        'answer_text' => '', // Resposta livre
                        'is_correct' => true,
                        'order' => 1,
                        'justification' => null
                    ]
                ]
            ];
        }
        
        return $questions;
    }
    
    private function parseMultipleChoiceCloze($text)
    {
        $questions = [];
        
        // Extrair instruções
        $instructions = '';
        if (preg_match('/^(.+?)(?=\n\n)/s', $text, $instrMatch)) {
            $instructions = trim($instrMatch[1]);
        }
        
        // Extrair contexto (texto principal com lacunas numeradas)
        $contextText = '';
        if (preg_match('/\n\n(.+?)(?=\n\n0\.|$)/s', $text, $ctxMatch)) {
            $contextText = trim($ctxMatch[1]);
        }
        
        // Extrair opções numeradas ao final (0-15)
        // Regex melhorado para aceitar mais variações
        preg_match_all('/(\d+)\.\s*\|?\s*A\)\s*(.+?)\s+B\)\s*(.+?)\s+[CO©]\)\s*(.+?)\s+[DO]\)\s*(.+?)(?=\n\s*\d+\.|$)/s', $text, $optMatches, PREG_SET_ORDER);
        
        foreach ($optMatches as $opt) {
            $number = (int)$opt[1];
            
            // Pular o exemplo (número 0)
            if ($number == 0) continue;
            
            $options = [
                [
                    'answer_text' => trim($opt[2]),
                    'is_correct' => false,
                    'order' => 1,
                    'justification' => null
                ],
                [
                    'answer_text' => trim($opt[3]),
                    'is_correct' => false,
                    'order' => 2,
                    'justification' => null
                ],
                [
                    'answer_text' => trim($opt[4]),
                    'is_correct' => false,
                    'order' => 3,
                    'justification' => null
                ],
                [
                    'answer_text' => trim($opt[5]),
                    'is_correct' => false,
                    'order' => 4,
                    'justification' => null
                ]
            ];
            
            $questions[] = [
                'category' => $this->currentCategory,
                'question_text' => "Lacuna {$number}",
                'question_type' => 'multiple_choice_cloze',
                'difficulty' => 3,
                'context' => $contextText,
                'order' => $number,
                'explanation' => null,
                'answers' => $options
            ];
        }
        
        return $questions;
    }
}
