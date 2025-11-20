#!/usr/bin/env python3
"""
Script para reformatar módulos com HTML de qualidade
Remove marcadores OCR e aplica formatação profissional
"""

import json
import re
from pathlib import Path
from typing import Dict

def clean_and_format_content(raw_content: str, module_name: str) -> str:
    """Limpa e formata o conteúdo removendo artefatos de OCR"""
    
    # Remover marcadores de página
    content = re.sub(r'--- Página \d+ ---', '', raw_content)
    
    # Remover caracteres estranhos e múltiplos espaços
    content = re.sub(r'\s+', ' ', content)
    content = re.sub(r'[)]{2,}', '', content)
    content = re.sub(r'\|{2,}', '', content)
    
    # Dividir em seções lógicas
    sections = []
    
    # Identificar títulos e seções importantes
    if 'alfabeto' in module_name.lower():
        sections = format_alfabeto(content)
    elif 'saudac' in module_name.lower() or 'present' in module_name.lower():
        sections = format_saudacoes(content)
    elif 'verbo' in module_name.lower() and ('essere' in content.lower() or 'avere' in content.lower()):
        sections = format_verbos_base(content)
    elif 'artig' in module_name.lower():
        sections = format_artigos(content)
    elif 'numer' in module_name.lower() or 'número' in module_name.lower():
        sections = format_numeros(content)
    elif 'verbo' in module_name.lower() and 'regular' in module_name.lower():
        sections = format_verbos_regulares(content)
    elif 'preposiz' in module_name.lower():
        sections = format_preposicoes(content)
    elif 'pronom' in module_name.lower():
        sections = format_pronomes(content)
    elif 'passato' in module_name.lower():
        sections = format_passato(content)
    elif 'imperfetto' in module_name.lower():
        sections = format_imperfetto(content)
    elif 'futuro' in module_name.lower():
        sections = format_futuro(content)
    elif 'condizionale' in module_name.lower():
        sections = format_condizionale(content)
    else:
        # Formatação genérica
        sections = [f"<div class='content-section'><p>{content.strip()}</p></div>"]
    
    return '\n\n'.join(sections)

def format_alfabeto(content: str) -> list:
    """Formatação específica para alfabeto"""
    sections = []
    
    # Título
    sections.append("<div class='intro-section'><h2>L'Alfabeto Italiano</h2></div>")
    
    # Letras do alfabeto
    if 'Albero' in content or 'Bocca' in content:
        sections.append("""
<div class='alphabet-section'>
<h3>Le Lettere</h3>
<ul class='alphabet-list'>
<li><strong>A</strong> - Albero</li>
<li><strong>B</strong> - Bocca</li>
<li><strong>C</strong> - Casa</li>
<li><strong>D</strong> - Dado</li>
<li><strong>E</strong> - Elefante</li>
<li><strong>F</strong> - Famiglia</li>
<li><strong>G</strong> - Gioco</li>
<li><strong>H</strong> - Hotel</li>
<li><strong>I</strong> - Isola</li>
<li><strong>L</strong> - Limone</li>
<li><strong>M</strong> - Mano</li>
<li><strong>N</strong> - Naso</li>
<li><strong>O</strong> - Ombra</li>
<li><strong>P</strong> - Pioggia</li>
<li><strong>Q</strong> - Quadro</li>
<li><strong>R</strong> - Ramo</li>
<li><strong>S</strong> - Sole</li>
<li><strong>T</strong> - Tavolo</li>
<li><strong>U</strong> - Uovo</li>
<li><strong>V</strong> - Vaso</li>
<li><strong>Z</strong> - Zaino</li>
</ul>
</div>
""")
    
    # Consoantes duplas
    if 'doppie' in content.lower():
        sections.append("""
<div class='grammar-note'>
<h3>Consonanti Doppie</h3>
<p>Tutte le consonanti, ad eccezione della <strong>Q</strong> e dell'<strong>H</strong>, possono essere doppie.</p>
<p><em>Esempi:</em> Mamma, Tetto, Palla, Tassa, Anno, Tappo</p>
</div>
""")
    
    # Sons de C e G
    if 'suono dolce' in content.lower() or 'ci' in content.lower():
        sections.append("""
<div class='pronunciation-section'>
<h3>I Suoni di C e G</h3>
<table class='pronunciation-table'>
<tr>
<th>Letra</th>
<th>Som Dolce</th>
<th>Som Duro</th>
</tr>
<tr>
<td><strong>C</strong></td>
<td>CI, CE = [tʃ] (ciao, cinema)</td>
<td>CA, CO, CU = [k] (casa, cuore)</td>
</tr>
<tr>
<td><strong>G</strong></td>
<td>GI, GE = [dʒ] (giorno, gelato)</td>
<td>GA, GO, GU = [g] (gatto, gusto)</td>
</tr>
</table>
</div>
""")
    
    # Combinações especiais
    if 'gli' in content.lower() or 'gn' in content.lower():
        sections.append("""
<div class='special-sounds'>
<h3>Suoni Speciali</h3>
<ul>
<li><strong>GLI</strong> = [ʎ] (como LH) → figlio, famiglia, foglio</li>
<li><strong>GN</strong> = [ɲ] (como NH) → gnocchi, lavagna, bagno</li>
<li><strong>SCI/SCE</strong> = [ʃ] (como X) → scivolo, pesce, piscina</li>
</ul>
</div>
""")
    
    return sections

def format_saudacoes(content: str) -> list:
    """Formatação para saudações"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>Come si saluta?</h2></div>")
    
    if 'ciao' in content.lower():
        sections.append("""
<div class='greetings-section'>
<h3>Saluti Informali e Formali</h3>
<ul>
<li><strong>CIAO</strong> - informal (para chegar e sair)</li>
<li><strong>BUONGIORNO</strong> - manhã até meio-dia</li>
<li><strong>BUONA SERA</strong> - tarde/noite</li>
<li><strong>BUONA NOTTE</strong> - despedida (ir dormir)</li>
<li><strong>ARRIVEDERCI</strong> - até logo</li>
</ul>
</div>
""")
    
    if 'chiamarsi' in content.lower():
        sections.append("""
<div class='verbs-section'>
<h3>Verbo CHIAMARSI (chamar-se)</h3>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>mi chiamo</td></tr>
<tr><td><strong>tu</strong></td><td>ti chiami</td></tr>
<tr><td><strong>lui/lei</strong></td><td>si chiama</td></tr>
<tr><td><strong>noi</strong></td><td>ci chiamiamo</td></tr>
<tr><td><strong>voi</strong></td><td>vi chiamate</td></tr>
<tr><td><strong>loro</strong></td><td>si chiamano</td></tr>
</table>
<p class='example'><strong>Informal:</strong> Come ti chiami?</p>
<p class='example'><strong>Formal:</strong> Come si chiama?</p>
<p class='example'><strong>Risposta:</strong> Mi chiamo Maria.</p>
</div>
""")
    
    return sections

def format_verbos_base(content: str) -> list:
    """Formatação para ESSERE, AVERE, STARE"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>I Verbi Fondamentali</h2></div>")
    
    if 'essere' in content.lower():
        sections.append("""
<div class='verbs-section'>
<h3>Verbo ESSERE (ser/estar)</h3>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>sono</td></tr>
<tr><td><strong>tu</strong></td><td>sei</td></tr>
<tr><td><strong>lui/lei</strong></td><td>è</td></tr>
<tr><td><strong>noi</strong></td><td>siamo</td></tr>
<tr><td><strong>voi</strong></td><td>siete</td></tr>
<tr><td><strong>loro</strong></td><td>sono</td></tr>
</table>
<p class='usage'><strong>Uso:</strong> ESSERE + AGGETTIVO</p>
<p class='example'>Io sono stanco / Noi siamo felici / Lei è bella</p>
</div>
""")
    
    if 'avere' in content.lower():
        sections.append("""
<div class='verbs-section'>
<h3>Verbo AVERE (ter/haver)</h3>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>ho</td></tr>
<tr><td><strong>tu</strong></td><td>hai</td></tr>
<tr><td><strong>lui/lei</strong></td><td>ha</td></tr>
<tr><td><strong>noi</strong></td><td>abbiamo</td></tr>
<tr><td><strong>voi</strong></td><td>avete</td></tr>
<tr><td><strong>loro</strong></td><td>hanno</td></tr>
</table>
<p class='usage'><strong>Uso:</strong> AVERE + SOSTANTIVO</p>
<p class='example'>Io ho fame / Lui ha 20 anni / Noi abbiamo una casa</p>
</div>
""")
    
    return sections

def format_artigos(content: str) -> list:
    """Formatação para artigos"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>Gli Articoli</h2></div>")
    
    sections.append("""
<div class='grammar-section'>
<h3>Articoli Determinativi (o, a, os, as)</h3>
<table class='articles-table'>
<tr>
<th></th>
<th>Masculino Singular</th>
<th>Feminino Singular</th>
<th>Masculino Plural</th>
<th>Feminino Plural</th>
</tr>
<tr>
<td><strong>Geral</strong></td>
<td>IL (il libro)</td>
<td>LA (la casa)</td>
<td>I (i libri)</td>
<td>LE (le case)</td>
</tr>
<tr>
<td><strong>S+cons, Z, X</strong></td>
<td>LO (lo studente)</td>
<td>-</td>
<td>GLI (gli studenti)</td>
<td>-</td>
</tr>
<tr>
<td><strong>Vogal</strong></td>
<td>L' (l'amico)</td>
<td>L' (l'amica)</td>
<td>GLI (gli amici)</td>
<td>LE (le amiche)</td>
</tr>
</table>
</div>

<div class='grammar-section'>
<h3>Articoli Indeterminativi (um, uma)</h3>
<table class='articles-table'>
<tr>
<th>Masculino</th>
<th>Feminino</th>
</tr>
<tr>
<td>UN (un libro)</td>
<td>UNA (una casa)</td>
</tr>
<tr>
<td>UNO (uno studente - antes de S+cons)</td>
<td>UN' (un'amica - antes de vogal)</td>
</tr>
</table>
</div>
""")
    
    return sections

def format_numeros(content: str) -> list:
    """Formatação para números"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>I Numeri</h2></div>")
    
    sections.append("""
<div class='numbers-section'>
<h3>Numeri da 0 a 20</h3>
<ul class='numbers-list'>
<li>0 - zero</li>
<li>1 - uno</li>
<li>2 - due</li>
<li>3 - tre</li>
<li>4 - quattro</li>
<li>5 - cinque</li>
<li>6 - sei</li>
<li>7 - sette</li>
<li>8 - otto</li>
<li>9 - nove</li>
<li>10 - dieci</li>
<li>11 - undici</li>
<li>12 - dodici</li>
<li>13 - tredici</li>
<li>14 - quattordici</li>
<li>15 - quindici</li>
<li>16 - sedici</li>
<li>17 - diciassette</li>
<li>18 - diciotto</li>
<li>19 - diciannove</li>
<li>20 - venti</li>
</ul>
</div>

<div class='numbers-section'>
<h3>Numeri da 20 a 100</h3>
<ul class='numbers-list'>
<li>30 - trenta</li>
<li>40 - quaranta</li>
<li>50 - cinquanta</li>
<li>60 - sessanta</li>
<li>70 - settanta</li>
<li>80 - ottanta</li>
<li>90 - novanta</li>
<li>100 - cento</li>
</ul>
</div>
""")
    
    return sections

def format_verbos_regulares(content: str) -> list:
    """Formatação para verbos regulares"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>I Verbi Regolari</h2></div>")
    
    sections.append("""
<div class='verbs-section'>
<h3>Prima Coniugazione: -ARE (parlare)</h3>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>parlo</td></tr>
<tr><td><strong>tu</strong></td><td>parli</td></tr>
<tr><td><strong>lui/lei</strong></td><td>parla</td></tr>
<tr><td><strong>noi</strong></td><td>parliamo</td></tr>
<tr><td><strong>voi</strong></td><td>parlate</td></tr>
<tr><td><strong>loro</strong></td><td>parlano</td></tr>
</table>
<p class='example'><em>Esempi:</em> abitare, mangiare, lavorare, studiare</p>
</div>

<div class='verbs-section'>
<h3>Seconda Coniugazione: -ERE (credere)</h3>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>credo</td></tr>
<tr><td><strong>tu</strong></td><td>credi</td></tr>
<tr><td><strong>lui/lei</strong></td><td>crede</td></tr>
<tr><td><strong>noi</strong></td><td>crediamo</td></tr>
<tr><td><strong>voi</strong></td><td>credete</td></tr>
<tr><td><strong>loro</strong></td><td>credono</td></tr>
</table>
<p class='example'><em>Esempi:</em> leggere, scrivere, vendere, prendere</p>
</div>

<div class='verbs-section'>
<h3>Terza Coniugazione: -IRE (dormire)</h3>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>dormo</td></tr>
<tr><td><strong>tu</strong></td><td>dormi</td></tr>
<tr><td><strong>lui/lei</strong></td><td>dorme</td></tr>
<tr><td><strong>noi</strong></td><td>dormiamo</td></tr>
<tr><td><strong>voi</strong></td><td>dormite</td></tr>
<tr><td><strong>loro</strong></td><td>dormono</td></tr>
</table>
<p class='example'><em>Esempi:</em> partire, sentire, aprire</p>
</div>
""")
    
    return sections

def format_preposicoes(content: str) -> list:
    """Formatação para preposições"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>Le Preposizioni</h2></div>")
    
    sections.append("""
<div class='grammar-section'>
<h3>Preposizioni Semplici</h3>
<ul>
<li><strong>DI</strong> - de</li>
<li><strong>A</strong> - a, para</li>
<li><strong>DA</strong> - de (origem), desde</li>
<li><strong>IN</strong> - em</li>
<li><strong>CON</strong> - com</li>
<li><strong>SU</strong> - sobre</li>
<li><strong>PER</strong> - para, por</li>
<li><strong>TRA/FRA</strong> - entre</li>
</ul>
</div>

<div class='grammar-section'>
<h3>Preposizioni Articolate</h3>
<p>Preposição + Artigo = Preposição Articulada</p>
<table class='prepositions-table'>
<tr>
<th></th>
<th>IL</th>
<th>LO</th>
<th>LA</th>
<th>I</th>
<th>GLI</th>
<th>LE</th>
</tr>
<tr>
<td><strong>DI</strong></td>
<td>del</td>
<td>dello</td>
<td>della</td>
<td>dei</td>
<td>degli</td>
<td>delle</td>
</tr>
<tr>
<td><strong>A</strong></td>
<td>al</td>
<td>allo</td>
<td>alla</td>
<td>ai</td>
<td>agli</td>
<td>alle</td>
</tr>
<tr>
<td><strong>DA</strong></td>
<td>dal</td>
<td>dallo</td>
<td>dalla</td>
<td>dai</td>
<td>dagli</td>
<td>dalle</td>
</tr>
<tr>
<td><strong>IN</strong></td>
<td>nel</td>
<td>nello</td>
<td>nella</td>
<td>nei</td>
<td>negli</td>
<td>nelle</td>
</tr>
<tr>
<td><strong>SU</strong></td>
<td>sul</td>
<td>sullo</td>
<td>sulla</td>
<td>sui</td>
<td>sugli</td>
<td>sulle</td>
</tr>
</table>
<p class='example'><em>Esempi:</em> Vado AL cinema / Il libro DELLA professoressa / NEI giorni di festa</p>
</div>
""")
    
    return sections

def format_pronomes(content: str) -> list:
    """Formatação para pronomes"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>I Pronomi</h2></div>")
    
    sections.append("""
<div class='grammar-section'>
<h3>Pronomi Diretti (me, te, o, a, nos, vos, os, as)</h3>
<table class='pronouns-table'>
<tr><td><strong>mi</strong></td><td>me</td></tr>
<tr><td><strong>ti</strong></td><td>te</td></tr>
<tr><td><strong>lo</strong></td><td>o (masculino)</td></tr>
<tr><td><strong>la</strong></td><td>a (feminino)</td></tr>
<tr><td><strong>ci</strong></td><td>nos</td></tr>
<tr><td><strong>vi</strong></td><td>vos</td></tr>
<tr><td><strong>li</strong></td><td>os (masculino plural)</td></tr>
<tr><td><strong>le</strong></td><td>as (feminino plural)</td></tr>
</table>
<p class='example'>Leggo il libro → <strong>Lo</strong> leggo</p>
<p class='example'>Vedo Maria → <strong>La</strong> vedo</p>
</div>

<div class='grammar-section'>
<h3>Pronomi Indiretti (me, te, lhe, nos, vos, lhes)</h3>
<table class='pronouns-table'>
<tr><td><strong>mi</strong></td><td>me</td></tr>
<tr><td><strong>ti</strong></td><td>te</td></tr>
<tr><td><strong>gli</strong></td><td>lhe (masculino)</td></tr>
<tr><td><strong>le</strong></td><td>lhe (feminino)</td></tr>
<tr><td><strong>ci</strong></td><td>nos</td></tr>
<tr><td><strong>vi</strong></td><td>vos</td></tr>
<tr><td><strong>gli/loro</strong></td><td>lhes</td></tr>
</table>
<p class='example'>Parlo a Maria → <strong>Le</strong> parlo</p>
</div>
""")
    
    return sections

def format_passato(content: str) -> list:
    """Formatação para Passato Prossimo"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>Il Passato Prossimo</h2></div>")
    
    sections.append("""
<div class='grammar-section'>
<h3>Formação: Auxiliar (AVERE ou ESSERE) + Particípio Passado</h3>

<h4>Com AVERE (maioria dos verbos)</h4>
<p><strong>io ho parlato</strong> / <strong>tu hai mangiato</strong> / <strong>lui ha dormito</strong></p>
<p class='example'><em>Particípios regulares:</em></p>
<ul>
<li>-ARE → -ATO (parlare → parlato)</li>
<li>-ERE → -UTO (credere → creduto)</li>
<li>-IRE → -ITO (dormire → dormito)</li>
</ul>

<h4>Com ESSERE (verbos de movimento e reflexivos)</h4>
<p><strong>io sono andato/a</strong> / <strong>tu sei partito/a</strong> / <strong>lei è arrivata</strong></p>
<p class='example'><em>Verbos com ESSERE:</em> andare, venire, partire, arrivare, entrare, uscire, nascere, morire</p>
<p class='note'><strong>Atenção:</strong> Com ESSERE, o particípio concorda com o sujeito!</p>

<h4>Particípios Irregulares</h4>
<ul>
<li>fare → fatto</li>
<li>dire → detto</li>
<li>vedere → visto</li>
<li>leggere → letto</li>
<li>scrivere → scritto</li>
<li>prendere → preso</li>
<li>aprire → aperto</li>
<li>essere → stato</li>
<li>avere → avuto</li>
</ul>
</div>
""")
    
    return sections

def format_imperfetto(content: str) -> list:
    """Formatação para Imperfetto"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>L'Imperfetto</h2></div>")
    
    sections.append("""
<div class='grammar-section'>
<h3>Uso do Imperfetto</h3>
<ul>
<li>Ações habituais no passado</li>
<li>Descrições no passado</li>
<li>Ações em progresso no passado</li>
<li>Condições no passado</li>
</ul>

<h4>Coniugazione -ARE (parlare)</h4>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>parlavo</td></tr>
<tr><td><strong>tu</strong></td><td>parlavi</td></tr>
<tr><td><strong>lui/lei</strong></td><td>parlava</td></tr>
<tr><td><strong>noi</strong></td><td>parlavamo</td></tr>
<tr><td><strong>voi</strong></td><td>parlavate</td></tr>
<tr><td><strong>loro</strong></td><td>parlavano</td></tr>
</table>

<h4>Coniugazione -ERE (credere)</h4>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>credevo</td></tr>
<tr><td><strong>tu</strong></td><td>credevi</td></tr>
<tr><td><strong>lui/lei</strong></td><td>credeva</td></tr>
<tr><td><strong>noi</strong></td><td>credevamo</td></tr>
<tr><td><strong>voi</strong></td><td>credevate</td></tr>
<tr><td><strong>loro</strong></td><td>credevano</td></tr>
</table>

<h4>Coniugazione -IRE (dormire)</h4>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>dormivo</td></tr>
<tr><td><strong>tu</strong></td><td>dormivi</td></tr>
<tr><td><strong>lui/lei</strong></td><td>dormiva</td></tr>
<tr><td><strong>noi</strong></td><td>dormivamo</td></tr>
<tr><td><strong>voi</strong></td><td>dormivate</td></tr>
<tr><td><strong>loro</strong></td><td>dormivano</td></tr>
</table>

<p class='example'><em>Esempio:</em> Quando ero bambino, <strong>giocavo</strong> sempre nel parco.</p>
</div>
""")
    
    return sections

def format_futuro(content: str) -> list:
    """Formatação para Futuro"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>Il Futuro Semplice</h2></div>")
    
    sections.append("""
<div class='grammar-section'>
<h3>Futuro -ARE (parlare)</h3>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>parlerò</td></tr>
<tr><td><strong>tu</strong></td><td>parlerai</td></tr>
<tr><td><strong>lui/lei</strong></td><td>parlerà</td></tr>
<tr><td><strong>noi</strong></td><td>parleremo</td></tr>
<tr><td><strong>voi</strong></td><td>parlerete</td></tr>
<tr><td><strong>loro</strong></td><td>parleranno</td></tr>
</table>

<h3>Futuro -ERE/-IRE (credere/partire)</h3>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>crederò / partirò</td></tr>
<tr><td><strong>tu</strong></td><td>crederai / partirai</td></tr>
<tr><td><strong>lui/lei</strong></td><td>crederà / partirà</td></tr>
<tr><td><strong>noi</strong></td><td>crederemo / partiremo</td></tr>
<tr><td><strong>voi</strong></td><td>crederete / partirete</td></tr>
<tr><td><strong>loro</strong></td><td>crederanno / partiranno</td></tr>
</table>

<h3>Futuros Irregulares</h3>
<ul>
<li><strong>essere</strong> → sarò, sarai, sarà...</li>
<li><strong>avere</strong> → avrò, avrai, avrà...</li>
<li><strong>andare</strong> → andrò, andrai, andrà...</li>
<li><strong>fare</strong> → farò, farai, farà...</li>
<li><strong>vedere</strong> → vedrò, vedrai, vedrà...</li>
<li><strong>venire</strong> → verrò, verrai, verrà...</li>
</ul>

<p class='example'><em>Esempio:</em> Domani <strong>andrò</strong> al mare.</p>
</div>
""")
    
    return sections

def format_condizionale(content: str) -> list:
    """Formatação para Condizionale"""
    sections = []
    
    sections.append("<div class='intro-section'><h2>Il Condizionale</h2></div>")
    
    sections.append("""
<div class='grammar-section'>
<h3>Uso do Condizionale</h3>
<ul>
<li>Expressar desejo ou pedido cortês</li>
<li>Dar conselhos</li>
<li>Expressar possibilidade</li>
<li>Frases condicionais com SE</li>
</ul>

<h4>Condizionale -ARE (parlare)</h4>
<table class='conjugation-table'>
<tr><td><strong>io</strong></td><td>parlerei</td></tr>
<tr><td><strong>tu</strong></td><td>parleresti</td></tr>
<tr><td><strong>lui/lei</strong></td><td>parlerebbe</td></tr>
<tr><td><strong>noi</strong></td><td>parleremmo</td></tr>
<tr><td><strong>voi</strong></td><td>parlereste</td></tr>
<tr><td><strong>loro</strong></td><td>parlerebbero</td></tr>
</table>

<h4>Verbos Importantes</h4>
<ul>
<li><strong>VOLERE</strong> → vorrei (eu gostaria)</li>
<li><strong>DOVERE</strong> → dovrei (eu deveria)</li>
<li><strong>POTERE</strong> → potrei (eu poderia)</li>
</ul>

<p class='example'><em>Esempi:</em></p>
<p class='example'>• <strong>Vorrei</strong> un caffè (Eu gostaria de um café)</p>
<p class='example'>• <strong>Dovresti</strong> studiare di più (Você deveria estudar mais)</p>
<p class='example'>• <strong>Potremmo</strong> andare al cinema (Poderíamos ir ao cinema)</p>
</div>
""")
    
    return sections

def reformat_module(module_file: Path):
    """Reformata um módulo específico"""
    print(f"📝 Reformatando: {module_file.name}")
    
    with open(module_file, 'r', encoding='utf-8') as f:
        module = json.load(f)
    
    # Reformatar conteúdo
    old_content = module.get('content_italian', '')
    new_content = clean_and_format_content(old_content, module['module_name'])
    
    module['content_italian'] = new_content
    module['metadata'] = module.get('metadata', {})
    module['metadata']['reformatted'] = True
    
    # Salvar
    with open(module_file, 'w', encoding='utf-8') as f:
        json.dump(module, f, ensure_ascii=False, indent=2)
    
    print(f"  ✅ Reformatado com sucesso")

def main():
    modules_dir = Path('/home/dellno/worksapace/imparalingua/backend/storage/app/imports/modules_formatted')
    
    print("🚀 Reformatando todos os módulos com HTML de qualidade\n")
    
    for module_file in sorted(modules_dir.glob('modulo_*.json')):
        reformat_module(module_file)
    
    print(f"\n✅ Reformatação concluída!")

if __name__ == '__main__':
    main()
