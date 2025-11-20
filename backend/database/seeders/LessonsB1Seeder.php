<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

class LessonsB1Seeder extends Seeder
{
    public function run(): void
    {
        // Buscar o curso "Italiano Básico 2025"
        $course = Course::where('slug', 'italiano-basico-2025')->first();

        if (!$course) {
            $this->command->error('Curso "Italiano Básico 2025" não encontrado!');
            return;
        }

        $this->command->info('Importando lições de nível B1...');

        // Lição 4: Verbos Modais (VOLERE, DOVERE, POTERE)
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lição 4: Verbos Modais (Volere, Dovere, Potere)',
            'slug' => 'licao-4-verbos-modais',
            'content_italian' => $this->getVerbosModaisContent(),
            'content_portuguese' => '',
            'exercises' => json_encode($this->getVerbosModaisExercises()),
            'lesson_type' => 'grammar',
            'difficulty' => 3,
            'estimated_time' => 35,
            'order' => 4,
        ]);

        // Lição 5: Verbos Reflexivos
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lição 5: Verbos Reflexivos (Verbi Riflessivi)',
            'slug' => 'licao-5-verbos-reflexivos',
            'content_italian' => $this->getVerbosReflexivosContent(),
            'content_portuguese' => '',
            'exercises' => json_encode($this->getVerbosReflexivosExercises()),
            'lesson_type' => 'grammar',
            'difficulty' => 3,
            'estimated_time' => 30,
            'order' => 5,
        ]);

        // Lição 6: Avverbi di Frequenza e Routine
        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Lição 6: Avverbi di Frequenza e Routine Quotidiana',
            'slug' => 'licao-6-avverbi-frequenza-routine',
            'content_italian' => $this->getAvverbiFrequenzaContent(),
            'content_portuguese' => '',
            'exercises' => json_encode($this->getAvverbiFrequenzaExercises()),
            'lesson_type' => 'vocabulary',
            'difficulty' => 2,
            'estimated_time' => 25,
            'order' => 6,
        ]);

        $this->command->info('✅ Lições B1 importadas com sucesso!');
    }

    private function getVerbosModaisContent(): string
    {
        return <<<'CONTENT'
<h2>I Verbi Modali (Volere, Dovere, Potere)</h2>

<div class="intro">
I verbi modali (o servili) sono verbi che precisano la relazione tra il soggetto ed il verbo. Sono fondamentali per esprimere <strong>volontà, possibilità e necessità</strong>.
</div>

<h3>✨ I Tre Verbi Modali Principali</h3>

<div class="verbs-section">
<h4>🎯 VOLERE (Querer)</h4>
<p>Esprime la <strong>VOLONTÀ</strong> di fare qualcosa.</p>
<ul>
<li>io <strong>voglio</strong></li>
<li>tu <strong>vuoi</strong></li>
<li>lui/lei <strong>vuole</strong></li>
<li>noi <strong>vogliamo</strong></li>
<li>voi <strong>volete</strong></li>
<li>loro <strong>vogliono</strong></li>
</ul>
<p class="example">📝 <em>Voglio andare al cinema.</em> (Quero ir ao cinema.)</p>
</div>

<div class="verbs-section">
<h4>📋 DOVERE (Dever)</h4>
<p>Esprime la <strong>NECESSITÀ</strong> o l'obbligo di fare qualcosa.</p>
<ul>
<li>io <strong>devo</strong></li>
<li>tu <strong>devi</strong></li>
<li>lui/lei <strong>deve</strong></li>
<li>noi <strong>dobbiamo</strong></li>
<li>voi <strong>dovete</strong></li>
<li>loro <strong>devono</strong></li>
</ul>
<p class="example">📝 <em>Devo lavorare fino a tardi.</em> (Devo trabalhar até tarde.)</p>
</div>

<div class="verbs-section">
<h4>✅ POTERE (Poder)</h4>
<p>Esprime la <strong>POSSIBILITÀ</strong> o il permesso di fare qualcosa.</p>
<ul>
<li>io <strong>posso</strong></li>
<li>tu <strong>puoi</strong></li>
<li>lui/lei <strong>può</strong> (con accento!)</li>
<li>noi <strong>possiamo</strong></li>
<li>voi <strong>potete</strong></li>
<li>loro <strong>possono</strong></li>
</ul>
<p class="example">📝 <em>Posso entrare?</em> (Posso entrar?)</p>
</div>

<h3>📖 Regola Fondamentale</h3>

<div class="rule-box">
<p><strong>Soggetto + Verbo Modale Coniugato + Verbo all'Infinito</strong></p>
<p class="example">✅ Io <strong>voglio</strong> <u>mangiare</u> una pizza.<br>
✅ Noi <strong>dobbiamo</strong> <u>studiare</u>.<br>
✅ Loro <strong>possono</strong> <u>partire</u> domani.</p>
</div>

<h3>💡 SAPERE come Verbo Modale</h3>

<p>Il verbo <strong>SAPERE</strong> è anche un verbo modale quando significa "avere la capacità di fare qualcosa".</p>

<ul>
<li>io <strong>so</strong></li>
<li>tu <strong>sai</strong></li>
<li>lui/lei <strong>sa</strong></li>
<li>noi <strong>sappiamo</strong></li>
<li>voi <strong>sapete</strong></li>
<li>loro <strong>sanno</strong></li>
</ul>

<p class="example">📝 <em>Non so nuotare.</em> (Não sei nadar.)</p>

<div class="tip-box">
<h4>🎯 Consiglio per il CILS B1</h4>
<p>Saper usare correttamente i verbi modali è <strong>essenziale</strong> per esprimere opinioni, desideri e necessità. Nella prova orale, ti capiterà sicuramente di dover dire frasi come <em>"Secondo me, dobbiamo fare..."</em> o <em>"Nel mio tempo libero, voglio..."</em></p>
</div>

<h3>⚠️ Attenzione!</h3>

<ul>
<li>La terza persona singolare di POTERE è <strong>può</strong> (con l'accento). Non dimenticarlo!</li>
<li>VOLERE: prima persona <strong>voglio</strong> (con -glio), terza plurale <strong>vogliono</strong></li>
<li>I verbi modali sono <strong>sempre seguiti da un infinito</strong></li>
</ul>
CONTENT;
    }

    private function getVerbosModaisExercises(): array
    {
        return [
            [
                'type' => 'multiple_choice',
                'question' => 'Claudia e Giovanni __________ partire per le vacanze.',
                'options' => ['vogliono', 'voglio', 'vuole', 'volete'],
                'correct_answer' => 'vogliono'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Tu __________ studiare di più!',
                'options' => ['devi', 'deve', 'dobbiamo', 'devono'],
                'correct_answer' => 'devi'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Io __________ nuotare molto bene.',
                'options' => ['so', 'sai', 'sa', 'sapete'],
                'correct_answer' => 'so'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Io e Maria __________ rimanere fuori fino alle 23:00.',
                'options' => ['possiamo', 'posso', 'potete', 'possono'],
                'correct_answer' => 'possiamo'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Loro non __________ arrivare in ritardo alla lezione.',
                'options' => ['possono', 'possiamo', 'può', 'puoi'],
                'correct_answer' => 'possono'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'I bambini stasera __________ mangiare la pizza.',
                'options' => ['vogliono', 'voglio', 'vuole', 'vogliamo'],
                'correct_answer' => 'vogliono'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Giacomo __________ cambiare casa.',
                'options' => ['deve', 'devo', 'devi', 'dobbiamo'],
                'correct_answer' => 'deve'
            ],
        ];
    }

    private function getVerbosReflexivosContent(): string
    {
        return <<<'CONTENT'
<h2>I Verbi Riflessivi (Verbi Reflexivos)</h2>

<div class="intro">
I verbi riflessivi sono verbi accompagnati da un <strong>pronome riflessivo</strong> (mi, ti, si, ci, vi, si) che concorda con il soggetto. L'azione espressa dal verbo riguarda <strong>direttamente il soggetto</strong> che la esegue.
</div>

<h3>📖 Differenza: Verbo Normale vs Verbo Riflessivo</h3>

<div class="comparison-table">
<table>
<thead>
<tr>
<th>Verbo Normale</th>
<th>Verbo Riflessivo</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>LAVARE</strong></td>
<td><strong>LAVARSI</strong></td>
</tr>
<tr>
<td>io lavo</td>
<td>io <strong>mi</strong> lavo</td>
</tr>
<tr>
<td>tu lavi</td>
<td>tu <strong>ti</strong> lavi</td>
</tr>
<tr>
<td>lui/lei lava</td>
<td>lui/lei <strong>si</strong> lava</td>
</tr>
<tr>
<td>noi laviamo</td>
<td>noi <strong>ci</strong> laviamo</td>
</tr>
<tr>
<td>voi lavate</td>
<td>voi <strong>vi</strong> lavate</td>
</tr>
<tr>
<td>loro lavano</td>
<td>loro <strong>si</strong> lavano</td>
</tr>
</tbody>
</table>
</div>

<h3>📝 Esempi di Utilizzo</h3>

<div class="example-section">
<h4>Forma Transitiva (oggetto esterno):</h4>
<p class="example">✅ Io lavo <u>i vestiti</u>. (Eu lavo as roupas.)</p>

<h4>Forma Riflessiva (azione su se stessi):</h4>
<p class="example">✅ Io <strong>mi</strong> lavo <u>le mani</u>. (Eu lavo as minhas mãos.)<br>
✅ Maria <strong>si</strong> lava <u>i denti</u> tre volte al giorno. (Maria escova os dentes três vezes por dia.)</p>
</div>

<h3>🔄 Uso Reciproco</h3>

<p>I pronomi <strong>ci, vi, si</strong> possono anche indicare un'azione che due o più persone si scambiano.</p>

<div class="example-section">
<p class="example">✅ Noi <strong>ci vediamo</strong> domani. (Nós nos vemos amanhã.)<br>
✅ Marco e Anna <strong>si amano</strong>. (Marco e Anna se amam.)<br>
✅ Io e te <strong>ci incontriamo</strong> al bar. (Eu e você nos encontramos no bar.)</p>
</div>

<h3>🌅 Verbi Riflessivi Comuni della Routine</h3>

<ul>
<li><strong>svegliarsi</strong> - acordar</li>
<li><strong>alzarsi</strong> - levantar-se</li>
<li><strong>lavarsi</strong> - lavar-se</li>
<li><strong>farsi la doccia</strong> - tomar banho</li>
<li><strong>vestirsi</strong> - vestir-se</li>
<li><strong>pettinarsi</strong> - pentear-se</li>
<li><strong>prepararsi</strong> - preparar-se</li>
<li><strong>sedersi</strong> - sentar-se</li>
<li><strong>addormentarsi</strong> - adormecer</li>
<li><strong>riposarsi</strong> - descansar</li>
</ul>

<h3>📖 La Giornata di Clara (Esempio)</h3>

<div class="story-box">
<p>La mattina <strong>mi sveglio</strong> alle 7:00, <strong>mi lavo</strong>, <strong>mi vesto</strong> e <strong>mi preparo</strong> la colazione. Il mio gatto <strong>si sveglia</strong> insieme a me, fa colazione anche lui e poi <strong>si addormenta</strong> di nuovo.</p>

<p>Arrivo al lavoro alle 8:30 e <strong>mi siedo</strong> davanti al computer. Lavoro per un paio d'ore e verso le 10:30 <strong>mi alzo</strong> per andare a prendere un caffè.</p>

<p>A fine serata io e i miei amici <strong>ci salutiamo</strong> e torno a casa.</p>
</div>

<div class="tip-box">
<h4>🎯 Consiglio per il CILS B1</h4>
<p>La prova orale spesso include una domanda sulla tua "giornata tipo". Prepara un breve discorso usando i verbi riflessivi. È una competenza fondamentale al livello B1!</p>
</div>

<h3>⚠️ Regola Importante</h3>

<div class="rule-box">
<p>Il pronome riflessivo va <strong>sempre prima</strong> del verbo coniugato e deve <strong>concordare con il soggetto</strong>.</p>
<p class="example">✅ Io <strong>mi</strong> sveglio alle 7.<br>
✅ Tu <strong>ti</strong> svegli alle 8.<br>
✅ Noi <strong>ci</strong> svegliamo presto.</p>
</div>
CONTENT;
    }

    private function getVerbosReflexivosExercises(): array
    {
        return [
            [
                'type' => 'multiple_choice',
                'question' => 'Quella ragazza è molto elegante. È vero, __________ sempre molto bene.',
                'options' => ['si veste', 'ti vesti', 'mi vesto', 'ci vestiamo'],
                'correct_answer' => 'si veste'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'A che ora __________? - Di solito molto presto, prima delle 6.30.',
                'options' => ['ti alzi', 'si alza', 'mi alzo', 'ci alziamo'],
                'correct_answer' => 'ti alzi'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Se domani __________ in tempo, possiamo fare una gita in montagna.',
                'options' => ['vi svegliate', 'ti svegli', 'si sveglia', 'mi sveglio'],
                'correct_answer' => 'vi svegliate'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Normalmente __________ alle sette e poi vado in bagno.',
                'options' => ['mi sveglio', 'ti svegli', 'si sveglia', 'ci svegliamo'],
                'correct_answer' => 'mi sveglio'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Noi __________ domani al bar alle 10.',
                'options' => ['ci vediamo', 'vi vedete', 'si vedono', 'mi vedo'],
                'correct_answer' => 'ci vediamo'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Mario __________ i denti tre volte al giorno.',
                'options' => ['si lava', 'ti lavi', 'mi lavo', 'ci laviamo'],
                'correct_answer' => 'si lava'
            ],
        ];
    }

    private function getAvverbiFrequenzaContent(): string
    {
        return <<<'CONTENT'
<h2>Avverbi di Frequenza e Routine Quotidiana</h2>

<div class="intro">
Gli avverbi di frequenza rispondono alla domanda <strong>"Quanto spesso?"</strong> e sono essenziali per descrivere la tua routine e le tue abitudini.
</div>

<h3>📊 Avverbi di Frequenza (da più a meno frequente)</h3>

<ul class="frequency-list">
<li><strong>sempre</strong> - sempre (100%)</li>
<li><strong>normalmente / solitamente</strong> - normalmente</li>
<li><strong>frequentemente / spesso</strong> - frequentemente</li>
<li><strong>a volte / qualche volta</strong> - às vezes</li>
<li><strong>raramente</strong> - raramente</li>
<li><strong>quasi mai</strong> - quase nunca</li>
<li><strong>mai</strong> - nunca (0%)</li>
</ul>

<h3>📖 Posizione nella Frase</h3>

<div class="rule-box">
<p>Gli avverbi di frequenza generalmente si mettono <strong>dopo il verbo</strong>.</p>
<p class="example">✅ Vado <strong>spesso</strong> al cinema.<br>
✅ Leggo <strong>raramente</strong> i romanzi.<br>
✅ Mangio <strong>sempre</strong> la colazione.</p>
</div>

<h3>⚠️ La Doppia Negazione</h3>

<div class="important-box">
<p>In italiano, a differenza del portoghese, la negazione con <strong>"mai"</strong> richiede un <strong>"non"</strong> prima del verbo.</p>

<p class="example">✅ <strong>Non</strong> bevo <strong>mai</strong> il caffè. (Nunca bebo café.)<br>
✅ <strong>Non</strong> vado <strong>mai</strong> a teatro.<br>
✅ <strong>Non</strong> mangio <strong>quasi mai</strong> la carne.</p>

<p class="error">❌ ERRATO: Bevo mai il caffè.</p>
</div>

<h3>📅 I Giorni della Settimana</h3>

<ul>
<li><strong>lunedì</strong> (segunda-feira)</li>
<li><strong>martedì</strong> (terça-feira)</li>
<li><strong>mercoledì</strong> (quarta-feira)</li>
<li><strong>giovedì</strong> (quinta-feira)</li>
<li><strong>venerdì</strong> (sexta-feira)</li>
<li><strong>sabato</strong> (sábado)</li>
<li><strong>domenica</strong> (domingo)</li>
</ul>

<div class="tip-box">
<p><strong>Nota:</strong> I giorni da lunedì a venerdì sono <strong>maschili</strong> e finiscono con l'accento. Sono invariabili (il lunedì, i lunedì). In italiano, i giorni della settimana si scrivono con la <strong>lettera minuscola</strong>.</p>
</div>

<h3>⏰ Espressioni Temporali Utili</h3>

<h4>Espressioni con "sempre":</h4>
<ul>
<li><strong>tutti i giorni</strong> - todos os dias</li>
<li><strong>ogni giorno</strong> - cada dia</li>
<li><strong>tutte le settimane</strong> - todas as semanas</li>
<li><strong>ogni lunedì</strong> - toda segunda-feira</li>
</ul>

<h4>Linea Temporale:</h4>
<div class="timeline">
<p><strong>Tre giorni fa</strong> ← L'altro ieri ← Ieri ← <strong>Oggi</strong> → Domani → Dopodomani → <strong>Fra tre giorni</strong></p>

<p><strong>Settimana scorsa</strong> ← <strong>Questa settimana</strong> → <strong>Settimana prossima</strong></p>

<p><strong>Prima</strong> ← <strong>Adesso / Ora</strong> → <strong>Poi / Dopo</strong></p>
</div>

<h3>🌅 Esempio di Routine Quotidiana</h3>

<div class="story-box">
<p><strong>La mia giornata tipo:</strong></p>

<p>Normalmente mi sveglio <strong>sempre</strong> alle 7:00. Mi alzo subito e vado in bagno. Mi lavo i denti e mi faccio la doccia. Dopo, torno in camera, mi vesto e mi pettino.</p>

<p>Alle 8:00 faccio <strong>sempre</strong> colazione. <strong>Spesso</strong> mangio pane e marmellata con un caffè. <strong>A volte</strong> mangio anche frutta fresca.</p>

<p>Vado al lavoro <strong>tutti i giorni</strong> tranne il sabato e la domenica. <strong>Mai</strong> arrivo in ritardo!</p>

<p>La sera, <strong>qualche volta</strong> esco con gli amici, ma <strong>di solito</strong> rimango a casa e guardo la televisione o leggo un libro.</p>

<p><strong>Raramente</strong> vado a dormire prima delle 23:00. Il fine settimana, <strong>normalmente</strong> mi sveglio più tardi.</p>
</div>

<div class="tip-box">
<h4>🎯 Consiglio per il CILS B1</h4>
<p>Saper descrivere la tua routine usando correttamente i verbi riflessivi e gli avverbi di frequenza è una competenza <strong>fondamentale</strong> per il CILS B1. Prepara una descrizione della tua giornata tipo e praticala ad alta voce!</p>
</div>

<h3>💪 Pratica</h3>

<p>Prova a descrivere quanto spesso fai queste azioni:</p>
<ul>
<li>Fare sport</li>
<li>Fare la spesa</li>
<li>Guardare la televisione</li>
<li>Leggere</li>
<li>Ascoltare musica</li>
<li>Uscire con gli amici</li>
<li>Andare al mare</li>
</ul>
CONTENT;
    }

    private function getAvverbiFrequenzaExercises(): array
    {
        return [
            [
                'type' => 'multiple_choice',
                'question' => 'Io __________ vado al cinema. (100% das vezes)',
                'options' => ['sempre', 'spesso', 'a volte', 'mai'],
                'correct_answer' => 'sempre'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Non mangio __________ la carne. (nunca)',
                'options' => ['mai', 'sempre', 'spesso', 'a volte'],
                'correct_answer' => 'mai'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Vado __________ in palestra, circa due volte alla settimana.',
                'options' => ['spesso', 'sempre', 'mai', 'raramente'],
                'correct_answer' => 'spesso'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Il lunedì vado __________ al lavoro.',
                'options' => ['sempre', 'mai', 'raramente', 'qualche volta'],
                'correct_answer' => 'sempre'
            ],
            [
                'type' => 'multiple_choice',
                'question' => 'Maria __________ va a teatro, preferisce il cinema.',
                'options' => ['raramente', 'sempre', 'spesso', 'normalmente'],
                'correct_answer' => 'raramente'
            ],
            [
                'type' => 'multiple_choice',
                'question' => '__________ faccio sport la domenica mattina.',
                'options' => ['Normalmente', 'Mai', 'Quasi mai', 'Raramente'],
                'correct_answer' => 'Normalmente'
            ],
        ];
    }
}
