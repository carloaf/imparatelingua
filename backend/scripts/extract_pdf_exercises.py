#!/usr/bin/env python3
"""
Script para extrair exercícios das páginas finais do PDF ItalB1-25.txt
e associá-los aos módulos correspondentes.
"""

import json
import re
import os

# Mapeamento de exercícios por página e módulo correspondente
EXERCISE_MAPPING = {
    "pag. 37": {
        "module_id": 3,  # Verbos ESSERE, AVERE, STARE
        "title": "Uma dia di famiglia - Verbi al presente",
        "type": "fill_in_blank",
        "start_line": 3851,
        "answers_line": 3853
    },
    "pag. 42": {
        "module_id": 7,  # Preposições
        "title": "Preposizioni Semplici",
        "type": "fill_in_blank",
        "start_line": 3859,
        "answers_line": 3861
    },
    "pag. 46 - dimostrativi": {
        "module_id": 8,  # Pronomes
        "title": "Aggettivi Dimostrativi",
        "type": "fill_in_blank",
        "start_line": 3881,
        "answers_line": 3883
    },
    "pag. 46 - preposizioni": {
        "module_id": 7,  # Preposições
        "title": "Preposizioni semplici e articolate",
        "type": "fill_in_blank",
        "start_line": 3900,
        "answers_line": 3902
    },
    "pag. 47 - vacanze": {
        "module_id": 7,  # Preposições (contexto de viagem)
        "title": "Le Vacanze degli Italiani - Rispondi",
        "type": "multiple_choice",
        "start_line": 3918,
        "answers_line": 3920
    },
    "pag. 47 - vero_falso": {
        "module_id": 7,  # Preposições (contexto de viagem)
        "title": "Le Vacanze degli Italiani - Vero o Falso",
        "type": "true_false",
        "start_line": 3964,
        "answers_line": 3966
    },
    "pag. 62": {
        "module_id": 9,  # Passato Prossimo
        "title": "Il passato prossimo dei verbi modali",
        "type": "fill_in_blank",
        "start_line": 3997,
        "answers_line": 3999
    },
    "pag. 63": {
        "module_id": 8,  # Pronomes (artigos partitivos são relacionados)
        "title": "Gli articoli partitivi",
        "type": "fill_in_blank",
        "start_line": 4016,
        "answers_line": 4018
    }
}

def read_text_file(filepath):
    """Lê o arquivo de texto extraído do PDF"""
    with open(filepath, 'r', encoding='utf-8') as f:
        return f.readlines()

def extract_exercise_from_line_range(lines, start_line, end_line):
    """Extrai o conteúdo de um exercício dado um range de linhas"""
    return ''.join(lines[start_line-1:end_line-1])

def parse_prepositions_simple(answers_text):
    """
    Parse das respostas de preposições simples (pag. 42)
    Formato: '1.a/a 11. di 21. da'
    """
    exercises = []
    pattern = r'(\d+)\.\s*([a-z/]+)'
    matches = re.findall(pattern, answers_text, re.IGNORECASE)
    
    for num, answer in matches:
        exercises.append({
            "question_number": int(num),
            "correct_answer": answer.strip(),
            "question_text": f"Completa la frase {num} con la preposizione corretta.",
            "question_type": "fill_in_blank",
            "difficulty": 2,
            "order": int(num)
        })
    
    return exercises

def parse_multiple_choice_answers(answers_text):
    """
    Parse das respostas de múltipla escolha
    Formato: '1-B 6-B'
    """
    exercises = []
    pattern = r'(\d+)-([A-D])'
    matches = re.findall(pattern, answers_text)
    
    for num, answer in matches:
        exercises.append({
            "question_number": int(num),
            "correct_answer": answer,
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": int(num)
        })
    
    return exercises

def parse_true_false_answers(answers_text):
    """
    Parse das respostas Vero/Falso
    Formato: '1. Falso > fanno le vacanze ad agosto'
    """
    exercises = []
    lines = answers_text.split('\n')
    
    for line in lines:
        match = re.match(r'(\d+)\.\s*(Vero|Falso)\s*(?:>(.*))?', line, re.IGNORECASE)
        if match:
            num, answer, explanation = match.groups()
            exercises.append({
                "question_number": int(num),
                "correct_answer": answer.strip(),
                "question_type": "true_false",
                "difficulty": 2,
                "order": int(num),
                "explanation": explanation.strip() if explanation else ""
            })
    
    return exercises

def parse_verbi_presente(answers_text):
    """
    Parse das respostas de verbos no presente
    Formato: 'mi chiamo — abito — siamo - abbiamo'
    """
    exercises = []
    verbs = re.split(r'\s*[—-]\s*', answers_text.strip())
    verbs = [v.strip() for v in verbs if v.strip()]
    
    for i, verb in enumerate(verbs, 1):
        exercises.append({
            "question_number": i,
            "correct_answer": verb,
            "question_text": f"Completa la frase con il verbo coniugato ({i}).",
            "question_type": "fill_in_blank",
            "difficulty": 1,
            "order": i
        })
    
    return exercises

def main():
    """Função principal"""
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    txt_file = os.path.join(base_dir, 'storage/app/imports/ItalB1-25.txt')
    output_dir = os.path.join(base_dir, 'storage/app/imports/exercises_extracted/')
    
    # Criar diretório de saída se não existir
    os.makedirs(output_dir, exist_ok=True)
    
    print("🔍 Lendo arquivo de texto extraído do PDF...")
    lines = read_text_file(txt_file)
    total_lines = len(lines)
    print(f"✅ {total_lines} linhas lidas")
    
    print("\n📋 Mapeamento de exercícios encontrados:")
    print("=" * 70)
    
    extracted_exercises = {}
    
    # Extrair seção completa de soluções (páginas 72-75)
    solutions_section = ''.join(lines[3850:4200])
    
    # 1. Verbos no presente (pag. 37)
    print("\n1️⃣  Verbos no Presente (pag. 37) - Módulo 3")
    verbi_match = re.search(r"'Una di famiglia'.*?\(pag\. 37\)(.*?)Preposizioni", solutions_section, re.DOTALL)
    if verbi_match:
        verbi_exercises = parse_verbi_presente(verbi_match.group(1))
        print(f"   ✅ {len(verbi_exercises)} exercícios de conjugação extraídos")
        extracted_exercises["verbi_presente"] = {
            "module_id": 26,  # Módulo ESSERE/AVERE/STARE
            "exercises": verbi_exercises[:10]  # Limitar a 10 para não sobrecarregar
        }
    
    # 2. Preposições Simples (pag. 42)
    print("\n2️⃣  Preposições Simples (pag. 42) - Módulo 7")
    prep_match = re.search(r"Preposizioni Semplices.*?Esercizio(.*?)Preposizioni articolate", solutions_section, re.DOTALL)
    if prep_match:
        prep_exercises = parse_prepositions_simple(prep_match.group(1))
        print(f"   ✅ {len(prep_exercises)} exercícios de preposições extraídos")
        extracted_exercises["preposizioni_semplici"] = {
            "module_id": 36,  # Módulo de Preposições reformatado
            "exercises": prep_exercises[:15]  # Top 15
        }
    
    # 3. Aggettivi Dimostrativi (pag. 46)
    print("\n3️⃣  Aggettivi Dimostrativi (pag. 46) - Módulo 8")
    dim_match = re.search(r"Aggettivi Dimostrativi.*?\(pag\. 46\)(.*?)Esercizio 'Completa e frasi", solutions_section, re.DOTALL)
    if dim_match:
        dim_answers = dim_match.group(1)
        dim_exercises = []
        # Parse formato: '1. quest' — quell''
        pattern = r'(\d+)\.\s*([^\n]+)'
        for match in re.finditer(pattern, dim_answers):
            num, answers = match.groups()
            if '—' in answers or '/' in answers:
                dim_exercises.append({
                    "question_number": int(num),
                    "correct_answer": answers.strip(),
                    "question_text": f"Completa con gli aggettivi dimostrativi ({num}).",
                    "question_type": "fill_in_blank",
                    "difficulty": 2,
                    "order": int(num)
                })
        print(f"   ✅ {len(dim_exercises)} exercícios de demonstrativos extraídos")
        extracted_exercises["aggettivi_dimostrativi"] = {
            "module_id": 31,  # Módulo de Pronomes
            "exercises": dim_exercises
        }
    
    # 4. Le Vacanze - Multiple Choice (pag. 47)
    print("\n4️⃣  Le Vacanze degli Italiani - Multiple Choice (pag. 47)")
    vacanze_match = re.search(r"'Le Vacanze degli Italiani'.*?risposta esatta.*?\(pag\. 47\)(.*?)'Le Vacanze", solutions_section, re.DOTALL)
    if vacanze_match:
        vacanze_exercises = parse_multiple_choice_answers(vacanze_match.group(1))
        print(f"   ✅ {len(vacanze_exercises)} questões de múltipla escolha extraídas")
        extracted_exercises["vacanze_multiple_choice"] = {
            "module_id": 36,  # Preposições (contexto viagem)
            "exercises": vacanze_exercises
        }
    
    # 5. Le Vacanze - Vero o Falso (pag. 47)
    print("\n5️⃣  Le Vacanze degli Italiani - Vero o Falso (pag. 47)")
    vf_match = re.search(r"'Le Vacanze degli Italiani' Vero o Falso(.*?)I passato prossimo", solutions_section, re.DOTALL)
    if vf_match:
        vf_exercises = parse_true_false_answers(vf_match.group(1))
        print(f"   ✅ {len(vf_exercises)} questões Vero/Falso extraídas")
        extracted_exercises["vacanze_vero_falso"] = {
            "module_id": 36,
            "exercises": vf_exercises
        }
    
    # Salvar resultados em JSON
    output_file = os.path.join(output_dir, 'extracted_exercises.json')
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(extracted_exercises, f, ensure_ascii=False, indent=2)
    
    print("\n" + "=" * 70)
    print(f"✅ Extração concluída!")
    print(f"📁 Arquivo salvo: {output_file}")
    
    # Estatísticas finais
    total_exercises = sum(len(v['exercises']) for v in extracted_exercises.values())
    print(f"\n📊 Estatísticas:")
    print(f"   • Total de grupos: {len(extracted_exercises)}")
    print(f"   • Total de exercícios: {total_exercises}")
    print(f"   • Módulos afetados: {len(set(v['module_id'] for v in extracted_exercises.values()))}")
    
    print("\n🎯 Próximos passos:")
    print("   1. Revisar o arquivo JSON gerado")
    print("   2. Associar textos de questões aos exercícios")
    print("   3. Criar seeder para importar ao banco de dados")
    print("   4. Atualizar contagem de exercícios nos módulos")

if __name__ == '__main__':
    main()
