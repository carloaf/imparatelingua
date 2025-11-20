#!/usr/bin/env python3
"""
Script para organizar conteúdo extraído do PDF ItalB1-25.pdf
Separa conteúdo teórico (páginas 1-85) dos exercícios (páginas 86-174)
e cria estrutura JSON organizada por módulos com lições e exercícios.
"""

import re
import json
from pathlib import Path
from typing import Dict, List, Tuple

class ContentOrganizer:
    def __init__(self, input_file: str, output_dir: str):
        self.input_file = Path(input_file)
        self.output_dir = Path(output_dir)
        self.output_dir.mkdir(parents=True, exist_ok=True)
        
        # Definição dos módulos com páginas de conteúdo teórico e exercícios
        self.modules = [
            {
                'id': 'modulo_01_alfabeto',
                'name': 'Alfabeto e Fonética',
                'level': 'A1',
                'theory_pages': (1, 3),
                'exercise_pages': (86, 88),
                'topics': ['Alfabeto', 'Pronúncia', 'Sons de C/G', 'GLI', 'SCI', 'GN']
            },
            {
                'id': 'modulo_02_saudacoes',
                'name': 'Saudações e Apresentações',
                'level': 'A1',
                'theory_pages': (3, 4),
                'exercise_pages': (88, 90),
                'topics': ['Ciao', 'Buongiorno', 'CHIAMARSI', 'Formal vs Informal']
            },
            {
                'id': 'modulo_03_verbos',
                'name': 'Verbos ESSERE, AVERE, STARE',
                'level': 'A1',
                'theory_pages': (4, 8),
                'exercise_pages': (90, 95),
                'topics': ['ESSERE', 'AVERE', 'STARE', 'Conjugações']
            },
            {
                'id': 'modulo_04_artigos',
                'name': 'Artigos e Gênero',
                'level': 'A1',
                'theory_pages': (8, 15),
                'exercise_pages': (95, 105),
                'topics': ['Artigos Definidos', 'Artigos Indefinidos', 'Masculino/Feminino']
            },
            {
                'id': 'modulo_05_numeros',
                'name': 'Números, Datas e Horas',
                'level': 'A1',
                'theory_pages': (15, 25),
                'exercise_pages': (105, 115),
                'topics': ['Números', 'Dias da Semana', 'Meses', 'Estações', 'Horas']
            },
            {
                'id': 'modulo_06_verbos_regulares',
                'name': 'Verbos Regulares -ARE/-ERE/-IRE',
                'level': 'A2',
                'theory_pages': (26, 35),
                'exercise_pages': (115, 125),
                'topics': ['Presente dos Regulares', 'Conjugações -ARE', '-ERE', '-IRE']
            },
            {
                'id': 'modulo_07_preposicoes',
                'name': 'Preposições Simples e Articuladas',
                'level': 'A2',
                'theory_pages': (36, 48),
                'exercise_pages': (125, 138),
                'topics': ['DI', 'A', 'DA', 'IN', 'CON', 'SU', 'PER', 'TRA/FRA', 'Articuladas']
            },
            {
                'id': 'modulo_08_pronomes',
                'name': 'Pronomes (Pessoais, Possessivos, Demonstrativos)',
                'level': 'A2',
                'theory_pages': (49, 58),
                'exercise_pages': (138, 148),
                'topics': ['Pronomes Pessoais', 'Possessivos', 'Demonstrativos', 'Diretos', 'Indiretos']
            },
            {
                'id': 'modulo_09_passato_prossimo',
                'name': 'Passato Prossimo',
                'level': 'A2',
                'theory_pages': (59, 63),
                'exercise_pages': (148, 153),
                'topics': ['Particípio Passado', 'Auxiliar AVERE', 'Auxiliar ESSERE', 'Concordância']
            },
            {
                'id': 'modulo_10_imperfetto',
                'name': 'Imperfetto',
                'level': 'B1',
                'theory_pages': (64, 68),
                'exercise_pages': (153, 158),
                'topics': ['Formação', 'Usos', 'Imperfetto vs Passato Prossimo', 'Descrições']
            },
            {
                'id': 'modulo_11_futuro',
                'name': 'Futuro Semplice',
                'level': 'B1',
                'theory_pages': (69, 75),
                'exercise_pages': (158, 165),
                'topics': ['Futuro Regular', 'Futuro Irregular', 'Usos', 'Previsões']
            },
            {
                'id': 'modulo_12_condizionale',
                'name': 'Condizionale',
                'level': 'B1',
                'theory_pages': (76, 85),
                'exercise_pages': (165, 174),
                'topics': ['Condizionale Presente', 'Pedidos Educados', 'Hipóteses', 'Conselhos']
            }
        ]
    
    def extract_pages(self, text: str) -> Dict[int, str]:
        """Extrai páginas individuais do texto"""
        pages = {}
        current_page = None
        current_content = []
        
        for line in text.split('\n'):
            # Detecta marcador de página
            page_match = re.search(r'PÁGINA\s+(\d+)', line)
            if page_match:
                # Salva página anterior
                if current_page is not None:
                    pages[current_page] = '\n'.join(current_content).strip()
                
                # Inicia nova página
                current_page = int(page_match.group(1))
                current_content = []
            elif current_page is not None and line.strip() and not line.startswith('==='):
                current_content.append(line)
        
        # Salva última página
        if current_page is not None:
            pages[current_page] = '\n'.join(current_content).strip()
        
        return pages
    
    def extract_page_range(self, pages: Dict[int, str], start: int, end: int) -> str:
        """Extrai conteúdo de um range de páginas"""
        content = []
        for page_num in range(start, end + 1):
            if page_num in pages:
                content.append(f"--- Página {page_num} ---")
                content.append(pages[page_num])
                content.append("")
        return '\n'.join(content).strip()
    
    def clean_content(self, text: str) -> str:
        """Limpa conteúdo removendo artefatos de OCR"""
        # Remove linhas muito curtas (prováveis artefatos)
        lines = text.split('\n')
        cleaned = []
        for line in lines:
            stripped = line.strip()
            if len(stripped) > 2 or stripped in ['', '-', '•', '○']:
                cleaned.append(line)
        
        return '\n'.join(cleaned)
    
    def extract_exercises(self, text: str) -> List[Dict]:
        """Extrai exercícios do conteúdo"""
        exercises = []
        
        # Padrões para identificar exercícios
        # Ex: "E1 -", "Ex1.", "Esercizio 1", "1.", etc.
        exercise_pattern = r'(?:E|Ex|Esercizio)\s*[:\-]?\s*\d+|^\d+\.'
        
        lines = text.split('\n')
        current_exercise = None
        current_text = []
        
        for line in lines:
            if re.match(exercise_pattern, line.strip()):
                # Salva exercício anterior
                if current_exercise:
                    exercises.append({
                        'number': current_exercise,
                        'text': '\n'.join(current_text).strip()
                    })
                
                # Inicia novo exercício
                match = re.search(r'\d+', line)
                if match:
                    current_exercise = int(match.group())
                    current_text = [line]
            elif current_exercise:
                current_text.append(line)
        
        # Salva último exercício
        if current_exercise:
            exercises.append({
                'number': current_exercise,
                'text': '\n'.join(current_text).strip()
            })
        
        return exercises
    
    def create_module_structure(self, module: Dict, pages: Dict[int, str]) -> Dict:
        """Cria estrutura JSON de um módulo"""
        # Extrai conteúdo teórico
        theory_start, theory_end = module['theory_pages']
        theory_content = self.extract_page_range(pages, theory_start, theory_end)
        theory_content = self.clean_content(theory_content)
        
        # Extrai exercícios
        exercise_start, exercise_end = module['exercise_pages']
        exercise_content = self.extract_page_range(pages, exercise_start, exercise_end)
        exercises = self.extract_exercises(exercise_content)
        
        # Calcula estatísticas
        char_count = len(theory_content)
        word_count = len(theory_content.split())
        
        # Dificuldade baseada no nível
        difficulty_map = {'A1': 1, 'A2': 2, 'B1': 3}
        difficulty = difficulty_map.get(module['level'], 2)
        
        # Tempo estimado (baseado em caracteres)
        estimated_time = max(15, min(60, char_count // 200))
        
        return {
            'module_id': module['id'],
            'module_name': module['name'],
            'level': module['level'],
            'difficulty': difficulty,
            'estimated_time': estimated_time,
            'topics': module['topics'],
            'theory_pages': f"{theory_start}-{theory_end}",
            'exercise_pages': f"{exercise_start}-{exercise_end}",
            'content_italian': theory_content,
            'content_portuguese': '',  # A ser preenchido posteriormente
            'exercises': exercises,
            'exercise_count': len(exercises),
            'statistics': {
                'char_count': char_count,
                'word_count': word_count,
                'exercise_count': len(exercises)
            },
            'keywords': module['topics'][:5],  # Primeiros 5 tópicos como keywords
            'notes': f'Conteúdo extraído de ItalB1-25.pdf via OCR. Teoria: páginas {theory_start}-{theory_end}, Exercícios: páginas {exercise_start}-{exercise_end}'
        }
    
    def organize(self):
        """Organiza todo o conteúdo"""
        print(f"📚 Lendo arquivo: {self.input_file}")
        
        # Lê arquivo completo
        with open(self.input_file, 'r', encoding='utf-8') as f:
            full_text = f.read()
        
        print("📄 Extraindo páginas individuais...")
        pages = self.extract_pages(full_text)
        print(f"✅ {len(pages)} páginas extraídas")
        
        print("\n🔨 Organizando módulos...")
        summary = {
            'total_modules': len(self.modules),
            'extraction_date': '2025-11-19',
            'source_file': 'ItalB1-25.pdf',
            'theory_pages': '1-85',
            'exercise_pages': '86-174',
            'modules': []
        }
        
        for i, module in enumerate(self.modules, 1):
            print(f"\n📖 Módulo {i}/{len(self.modules)}: {module['name']}")
            
            # Cria estrutura do módulo
            module_data = self.create_module_structure(module, pages)
            
            # Salva arquivo JSON do módulo
            output_file = self.output_dir / f"{module['id']}.json"
            with open(output_file, 'w', encoding='utf-8') as f:
                json.dump(module_data, f, ensure_ascii=False, indent=2)
            
            print(f"   ✅ Teoria: {module_data['statistics']['char_count']} caracteres")
            print(f"   ✅ Exercícios: {module_data['exercise_count']} identificados")
            print(f"   💾 Salvo em: {output_file.name}")
            
            # Adiciona ao resumo
            summary['modules'].append({
                'id': module['id'],
                'name': module['name'],
                'level': module['level'],
                'theory_pages': module_data['theory_pages'],
                'exercise_pages': module_data['exercise_pages'],
                'char_count': module_data['statistics']['char_count'],
                'exercise_count': module_data['exercise_count']
            })
        
        # Salva resumo
        summary_file = self.output_dir / 'SUMMARY.json'
        with open(summary_file, 'w', encoding='utf-8') as f:
            json.dump(summary, f, ensure_ascii=False, indent=2)
        
        print(f"\n✅ Organização concluída!")
        print(f"📊 Resumo salvo em: {summary_file}")
        print(f"\n📈 Estatísticas Gerais:")
        print(f"   • Total de módulos: {len(self.modules)}")
        print(f"   • Páginas de teoria: 1-85 (85 páginas)")
        print(f"   • Páginas de exercícios: 86-174 (89 páginas)")
        
        total_chars = sum(m['char_count'] for m in summary['modules'])
        total_exercises = sum(m['exercise_count'] for m in summary['modules'])
        print(f"   • Total de caracteres: {total_chars:,}")
        print(f"   • Total de exercícios: {total_exercises}")
        
        # Estatísticas por nível
        levels = {}
        for module in summary['modules']:
            level = module['level']
            if level not in levels:
                levels[level] = {'count': 0, 'exercises': 0}
            levels[level]['count'] += 1
            levels[level]['exercises'] += module['exercise_count']
        
        print(f"\n📊 Por Nível:")
        for level in ['A1', 'A2', 'B1']:
            if level in levels:
                stats = levels[level]
                print(f"   • {level}: {stats['count']} módulos, {stats['exercises']} exercícios")

if __name__ == '__main__':
    import sys
    
    if len(sys.argv) < 3:
        print("Uso: python3 organize_content.py <arquivo_entrada.txt> <pasta_saida>")
        print("\nExemplo:")
        print("  python3 organize_content.py ItalB1-25.txt modules_organized")
        sys.exit(1)
    
    input_file = sys.argv[1]
    output_dir = sys.argv[2]
    
    organizer = ContentOrganizer(input_file, output_dir)
    organizer.organize()
