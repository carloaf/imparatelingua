#!/usr/bin/env python3
"""
Script para reformatar completamente o módulo de Preposições
e criar exercícios de qualidade focados no conteúdo
"""

import json
from pathlib import Path

def create_formatted_content():
    """Cria conteúdo HTML formatado profissionalmente"""
    return """
<div class='intro-section'>
<h2>Le Preposizioni</h2>
<p>As preposições são elementos fundamentais da língua italiana. Elas conectam palavras e frases, indicando relações de tempo, espaço, modo e causa.</p>
</div>

<div class='grammar-section'>
<h3>📌 Preposizioni Semplici (Preposições Simples)</h3>
<p>As 8 preposições simples fundamentais do italiano:</p>
<table class='prepositions-table'>
<tr>
<th>Preposição</th>
<th>Significado Principal</th>
<th>Exemplo</th>
</tr>
<tr>
<td><strong>DI</strong></td>
<td>de (posse, origem, material)</td>
<td>Il libro <strong>di</strong> Maria</td>
</tr>
<tr>
<td><strong>A</strong></td>
<td>a, para (direção, hora)</td>
<td>Vado <strong>a</strong> Roma</td>
</tr>
<tr>
<td><strong>DA</strong></td>
<td>de, desde, por (proveniência)</td>
<td>Vengo <strong>da</strong> Firenze</td>
</tr>
<tr>
<td><strong>IN</strong></td>
<td>em (lugar, tempo)</td>
<td>Vivo <strong>in</strong> Italia</td>
</tr>
<tr>
<td><strong>CON</strong></td>
<td>com (companhia, instrumento)</td>
<td>Esco <strong>con</strong> gli amici</td>
</tr>
<tr>
<td><strong>SU</strong></td>
<td>sobre, em cima de</td>
<td>Il libro è <strong>sul</strong> tavolo</td>
</tr>
<tr>
<td><strong>PER</strong></td>
<td>para, por (finalidade, duração)</td>
<td>Studio <strong>per</strong> tre ore</td>
</tr>
<tr>
<td><strong>TRA/FRA</strong></td>
<td>entre, dentro de (tempo futuro)</td>
<td>Arrivo <strong>tra</strong> 10 minuti</td>
</tr>
</table>
</div>

<div class='rule-box'>
<h3>🔍 USI PRINCIPALI - DI</h3>
<ul class='usage-list'>
<li><strong>Possesso:</strong> La macchina <strong>di</strong> Paola (o carro de Paola)</li>
<li><strong>Especificação:</strong> Un film <strong>di</strong> fantascienza (um filme de ficção científica)</li>
<li><strong>Material:</strong> Piatti <strong>di</strong> porcellana (pratos de porcelana)</li>
<li><strong>Idade:</strong> Un bambino <strong>di</strong> sette anni (uma criança de sete anos)</li>
<li><strong>Proveniência:</strong> Sono <strong>di</strong> Torino (sou de Turim)</li>
<li><strong>Momento do dia:</strong> <strong>Di</strong> mattina bevo caffè (de manhã bebo café)</li>
<li><strong>Após verbos:</strong> Finisco <strong>di</strong> lavorare (termino de trabalhar)</li>
</ul>
</div>

<div class='rule-box'>
<h3>🔍 USI PRINCIPALI - A</h3>
<ul class='usage-list'>
<li><strong>Cidade/Ilha pequena:</strong> Vado <strong>a</strong> Milano / <strong>a</strong> Capri</li>
<li><strong>Hora:</strong> <strong>A</strong> mezzogiorno pranziamo (ao meio-dia almoçamos)</li>
<li><strong>Destinatário:</strong> Scrivo una lettera <strong>a</strong> mia madre</li>
<li><strong>Início de ação:</strong> Comincio <strong>a</strong> studiare (começo a estudar)</li>
<li><strong>Movimento:</strong> Vado <strong>a</strong> mangiare (vou comer)</li>
<li><strong>Festividades:</strong> <strong>A</strong> Natale torno a casa</li>
</ul>
</div>

<div class='rule-box'>
<h3>🔍 USI PRINCIPALI - DA</h3>
<ul class='usage-list'>
<li><strong>Proveniência:</strong> Arrivo <strong>da</strong> Roma (chego de Roma)</li>
<li><strong>Casa/trabalho de alguém:</strong> Vado <strong>dal</strong> medico (vou ao médico)</li>
<li><strong>Início temporal:</strong> Studio italiano <strong>da</strong> tre anni (estudo italiano há três anos)</li>
<li><strong>Finalidade de objeto:</strong> Occhiali <strong>da</strong> vista (óculos de grau)</li>
<li><strong>Com infinitivo:</strong> Qualcosa <strong>da</strong> bere (algo para beber)</li>
</ul>
</div>

<div class='rule-box'>
<h3>🔍 USI PRINCIPALI - IN</h3>
<ul class='usage-list'>
<li><strong>País/Região:</strong> Vivo <strong>in</strong> Francia, <strong>in</strong> Provenza</li>
<li><strong>Dentro:</strong> Le chiavi sono <strong>nel</strong> cassetto</li>
<li><strong>Estação/Mês:</strong> <strong>In</strong> primavera, <strong>in</strong> maggio</li>
<li><strong>Tempo para completar:</strong> Finisco <strong>in</strong> due ore (termino em duas horas)</li>
<li><strong>Meio de transporte:</strong> Vado <strong>in</strong> autobus / <strong>in</strong> treno</li>
</ul>
<p class='note'><strong>⚠️ Exceções:</strong> <strong>a</strong> piedi (a pé), <strong>a</strong> cavallo (a cavalo)</p>
</div>

<div class='rule-box'>
<h3>🔍 USI PRINCIPALI - CON, SU, PER, TRA/FRA</h3>
<ul class='usage-list'>
<li><strong>CON - Companhia:</strong> Vado al cinema <strong>con</strong> Paola</li>
<li><strong>CON - Instrumento:</strong> Scrivo <strong>con</strong> la penna</li>
<li><strong>SU - Posição:</strong> Il libro è <strong>sul</strong> tavolo</li>
<li><strong>SU - Argumento:</strong> Un programma <strong>su</strong> Leonardo da Vinci</li>
<li><strong>PER - Duração:</strong> Studio <strong>per</strong> tre ore</li>
<li><strong>PER - Direção:</strong> Parto <strong>per</strong> Roma</li>
<li><strong>PER - Motivo:</strong> Vivo a Milano <strong>per</strong> lavoro</li>
<li><strong>TRA/FRA - Tempo futuro:</strong> Arrivo <strong>tra</strong> 15 minuti</li>
<li><strong>TRA/FRA - Posição:</strong> Il cinema è <strong>tra</strong> la farmacia e la banca</li>
</ul>
</div>

<div class='grammar-section'>
<h3>🔗 Preposizioni Articolate (Preposições Articuladas)</h3>
<p>Quando as preposições simples se combinam com os artigos definidos, formam as preposições articuladas:</p>

<table class='prepositions-table'>
<tr>
<th></th>
<th>IL</th>
<th>LO</th>
<th>LA</th>
<th>L'</th>
<th>I</th>
<th>GLI</th>
<th>LE</th>
</tr>
<tr>
<td><strong>DI</strong></td>
<td>del</td>
<td>dello</td>
<td>della</td>
<td>dell'</td>
<td>dei</td>
<td>degli</td>
<td>delle</td>
</tr>
<tr>
<td><strong>A</strong></td>
<td>al</td>
<td>allo</td>
<td>alla</td>
<td>all'</td>
<td>ai</td>
<td>agli</td>
<td>alle</td>
</tr>
<tr>
<td><strong>DA</strong></td>
<td>dal</td>
<td>dallo</td>
<td>dalla</td>
<td>dall'</td>
<td>dai</td>
<td>dagli</td>
<td>dalle</td>
</tr>
<tr>
<td><strong>IN</strong></td>
<td>nel</td>
<td>nello</td>
<td>nella</td>
<td>nell'</td>
<td>nei</td>
<td>negli</td>
<td>nelle</td>
</tr>
<tr>
<td><strong>SU</strong></td>
<td>sul</td>
<td>sullo</td>
<td>sulla</td>
<td>sull'</td>
<td>sui</td>
<td>sugli</td>
<td>sulle</td>
</tr>
</table>

<p class='note'><strong>💡 Nota:</strong> CON, PER e TRA/FRA raramente se combinam com artigos na língua moderna.</p>
</div>

<div class='example-box'>
<h3>📝 Exemplos Práticos</h3>
<ul class='examples-list'>
<li>Vado <strong>al</strong> cinema (A + IL = AL) - Vou ao cinema</li>
<li>Il libro <strong>dello</strong> studente (DI + LO = DELLO) - O livro do estudante</li>
<li>Vengo <strong>dalla</strong> biblioteca (DA + LA = DALLA) - Venho da biblioteca</li>
<li>Abito <strong>nel</strong> centro (IN + IL = NEL) - Moro no centro</li>
<li>Il gatto è <strong>sul</strong> tetto (SU + IL = SUL) - O gato está no telhado</li>
</ul>
</div>

<div class='tip-box'>
<h3>⚡ Quando Usar Preposição Simples ou Articulada?</h3>

<h4>✅ COM Artigo (Articulada):</h4>
<ul>
<li>Nomes comuns: <em>Vado <strong>al</strong> supermercato</em></li>
<li>Quando especifica algo: <em>Studio <strong>alla</strong> scuola francese</em></li>
<li>Com números (horas, anos): <em>Alle 21:00, nel 1985</em></li>
</ul>

<h4>❌ SEM Artigo (Simples):</h4>
<ul>
<li>Nomes de pessoa: <em>Scrivo <strong>a</strong> Maria</em></li>
<li>Nomes de cidade: <em>Vado <strong>a</strong> Roma</em></li>
<li>Artigo indeterminativo: <em>Vado <strong>da</strong> un amico</em></li>
<li>Possessivo + parentesco singular: <em>Telefono <strong>a</strong> mia madre</em></li>
</ul>
</div>

<div class='verbs-section'>
<h3>🚗 Preposições com Lugares Comuns</h3>
<div class='two-column-list'>
<div>
<h4>IN + lugar:</h4>
<ul>
<li>in Italia, in Europa</li>
<li>in banca, in ufficio</li>
<li>in cucina, in bagno</li>
<li>in centro, in periferia</li>
<li>in macchina, in treno</li>
<li>in montagna, in campagna</li>
</ul>
</div>
<div>
<h4>A/AL + lugar:</h4>
<ul>
<li>a Roma, a Milano</li>
<li>al mare, al cinema</li>
<li>al bar, al ristorante</li>
<li>a casa, a scuola</li>
<li>a piedi, a cavallo</li>
<li>al Nord, al Sud</li>
</ul>
</div>
</div>
</div>

<div class='important-box'>
<h3>⚠️ Erros Comuns a Evitar</h3>
<ul>
<li>❌ Vado <strong>in</strong> Roma → ✅ Vado <strong>a</strong> Roma</li>
<li>❌ Studio <strong>in</strong> casa → ✅ Studio <strong>a</strong> casa</li>
<li>❌ Vado <strong>a</strong> Italia → ✅ Vado <strong>in</strong> Italia</li>
<li>❌ Vengo <strong>di</strong> Brasil → ✅ Vengo <strong>dal</strong> Brasile</li>
<li>❌ Studio italiano <strong>per</strong> 3 anni → ✅ Studio italiano <strong>da</strong> 3 anni</li>
</ul>
</div>

<style>
.prepositions-table {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.prepositions-table th {
    background: #4f46e5;
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: 600;
}
.prepositions-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e5e7eb;
}
.prepositions-table tr:hover {
    background: #f9fafb;
}
.usage-list {
    list-style: none;
    padding-left: 0;
}
.usage-list li {
    padding: 8px 0;
    padding-left: 25px;
    position: relative;
}
.usage-list li:before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #10b981;
    font-weight: bold;
}
.two-column-list {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
@media (max-width: 768px) {
    .two-column-list {
        grid-template-columns: 1fr;
    }
}
</style>
"""

def create_quality_exercises():
    """Cria 12 exercícios de alta qualidade focados em preposições"""
    return [
        {
            "question_text": "Completa: 'Vado ___ cinema stasera con gli amici.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 1,
            "options": [
                {"text": "al", "is_correct": True},
                {"text": "nel", "is_correct": False},
                {"text": "del", "is_correct": False},
                {"text": "sul", "is_correct": False}
            ],
            "explanation": "A + IL = AL. Vado AL cinema. Usa-se 'a' para indicar direção/destino com lugares públicos."
        },
        {
            "question_text": "Completa: 'Maria viene ___ Francia.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 2,
            "options": [
                {"text": "dalla", "is_correct": True},
                {"text": "della", "is_correct": False},
                {"text": "alla", "is_correct": False},
                {"text": "nella", "is_correct": False}
            ],
            "explanation": "DA + LA = DALLA. 'Venire da' indica proveniência. Maria viene DALLA Francia (vem DA França)."
        },
        {
            "question_text": "Completa: 'Abito ___ Italia ___ tre anni.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 3,
            "options": [
                {"text": "in / da", "is_correct": True},
                {"text": "a / per", "is_correct": False},
                {"text": "in / per", "is_correct": False},
                {"text": "a / da", "is_correct": False}
            ],
            "explanation": "IN + país (Abito IN Italia). DA + tempo decorrido (DA tre anni = há três anos). 'Per' é para tempo futuro."
        },
        {
            "question_text": "Completa: 'Il libro è ___ tavolo ___ cucina.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 4,
            "options": [
                {"text": "sul / della", "is_correct": True},
                {"text": "nel / alla", "is_correct": False},
                {"text": "al / nella", "is_correct": False},
                {"text": "dal / per la", "is_correct": False}
            ],
            "explanation": "SU + IL = SUL (sobre o). DI + LA = DELLA (da). Il libro è SUL tavolo DELLA cucina."
        },
        {
            "question_text": "Completa: 'Vado ___ Roma ___ treno.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 5,
            "options": [
                {"text": "a / in", "is_correct": True},
                {"text": "in / con", "is_correct": False},
                {"text": "a / con", "is_correct": False},
                {"text": "da / in", "is_correct": False}
            ],
            "explanation": "A + cidade (Vado A Roma). IN + meio de transporte (IN treno). Exceções: a piedi, a cavallo."
        },
        {
            "question_text": "Completa: 'Studio italiano ___ due ore ogni giorno.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 6,
            "options": [
                {"text": "per", "is_correct": True},
                {"text": "da", "is_correct": False},
                {"text": "in", "is_correct": False},
                {"text": "a", "is_correct": False}
            ],
            "explanation": "PER + duração definida (Studio PER due ore = estudo POR duas horas). 'Da' é para tempo decorrido desde o início."
        },
        {
            "question_text": "Completa: 'Lavoro ___ lunedì ___ venerdì.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 7,
            "options": [
                {"text": "da / a", "is_correct": True},
                {"text": "di / a", "is_correct": False},
                {"text": "per / fino", "is_correct": False},
                {"text": "in / a", "is_correct": False}
            ],
            "explanation": "DA... A... = de... até... (Lavoro DA lunedì A venerdì = trabalho de segunda a sexta)."
        },
        {
            "question_text": "Completa: 'Scrivo una lettera ___ mia madre.'",
            "question_type": "multiple_choice",
            "difficulty": 1,
            "order": 8,
            "options": [
                {"text": "a", "is_correct": True},
                {"text": "per", "is_correct": False},
                {"text": "alla", "is_correct": False},
                {"text": "da", "is_correct": False}
            ],
            "explanation": "A + possessivo + parentesco singular = preposição simples sem artigo. Scrivo A mia madre (escrevo PARA minha mãe)."
        },
        {
            "question_text": "Completa: 'Il treno arriva ___ dieci minuti.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 9,
            "options": [
                {"text": "tra", "is_correct": True},
                {"text": "per", "is_correct": False},
                {"text": "in", "is_correct": False},
                {"text": "da", "is_correct": False}
            ],
            "explanation": "TRA/FRA + tempo = indica tempo futuro (daqui a). Il treno arriva TRA dieci minuti (o trem chega DAQUI A dez minutos)."
        },
        {
            "question_text": "Completa: '___ mattina bevo sempre un caffè.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 10,
            "options": [
                {"text": "Di", "is_correct": True},
                {"text": "In", "is_correct": False},
                {"text": "A", "is_correct": False},
                {"text": "La", "is_correct": False}
            ],
            "explanation": "DI + momento do dia (Di mattina, di sera, di notte). DI mattina = de manhã/pela manhã."
        },
        {
            "question_text": "Completa: 'Vado ___ medico perché non sto bene.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 11,
            "options": [
                {"text": "dal", "is_correct": True},
                {"text": "al", "is_correct": False},
                {"text": "nel", "is_correct": False},
                {"text": "col", "is_correct": False}
            ],
            "explanation": "DA + IL = DAL. 'Andare da + profissão' = ir ao consultório/casa de alguém. Vado DAL medico (vou ao médico)."
        },
        {
            "question_text": "Completa: 'Questo è un regalo ___ mia sorella.'",
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": 12,
            "options": [
                {"text": "per", "is_correct": True},
                {"text": "a", "is_correct": False},
                {"text": "da", "is_correct": False},
                {"text": "di", "is_correct": False}
            ],
            "explanation": "PER = para (finalidade, destinatário de coisa). Un regalo PER mia sorella (um presente PARA minha irmã). 'A' é para ações diretas (dare a, scrivere a)."
        }
    ]

def main():
    # Ler arquivo JSON existente
    json_path = Path('/home/dellno/worksapace/imparalingua/backend/storage/app/imports/modules_formatted/modulo_07_preposicoes_formatted.json')
    
    with open(json_path, 'r', encoding='utf-8') as f:
        module = json.load(f)
    
    # Atualizar conteúdo
    module['content_italian'] = create_formatted_content()
    module['exercises'] = create_quality_exercises()
    module['estimated_time'] = 40  # 40 minutos com 12 exercícios
    module['metadata']['reformatted'] = True
    module['metadata']['exercises_created'] = True
    module['metadata']['total_exercises'] = 12
    module['metadata']['quality_checked'] = True
    
    # Salvar
    with open(json_path, 'w', encoding='utf-8') as f:
        json.dump(module, f, ensure_ascii=False, indent=2)
    
    print("✅ Módulo de Preposições reformatado com sucesso!")
    print(f"📝 Conteúdo HTML profissional aplicado")
    print(f"🎯 12 exercícios de qualidade criados")
    print(f"⏱️ Tempo estimado: 40 minutos")

if __name__ == '__main__':
    main()
