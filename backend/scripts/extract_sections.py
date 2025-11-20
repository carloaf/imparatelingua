#!/usr/bin/env python3
"""
Script para extrair e organizar seções do arquivo ItalB1-25.txt
Separa o conteúdo por tópicos e cria arquivos JSON estruturados
"""

import os
import re
import json
from pathlib import Path
from typing import Dict, List, Tuple

class ContentOrganizer:
    def __init__(self, source_file: str, output_dir: str):
        self.source_file = Path(source_file)
        self.output_dir = Path(output_dir)
        self.output_dir.mkdir(exist_ok=True)
        
        # Definir módulos e suas palavras-chave
        self.modules = {
            'modulo_01_alfabeto': {
                'name': 'Alfabeto e Fonética',
                'level': 'A1',
                'keywords': ['alfabeto', 'lettere', 'pronuncia', 'suoni', 'consonanti', 'vocali'],
                'start_page': 1,
                'end_page': 3
            },
            'modulo_02_saudacoes': {
                'name': 'Saudações e Apresentações',
                'level': 'A1',
                'keywords': ['saluta', 'ciao', 'buongiorno', 'chiamarsi', 'presentar'],
                'start_page': 3,
                'end_page': 4
            },
            'modulo_03_verbos': {
                'name': 'Verbos ESSERE, AVERE, STARE',
                'level': 'A1',
                'keywords': ['essere', 'avere', 'stare', 'verbo'],
                'start_page': 4,
                'end_page': 8
            },
            'modulo_04_artigos': {
                'name': 'Artigos e Gênero',
                'level': 'A1',
                'keywords': ['articoli', 'definiti', 'indefiniti', 'genere', 'maschile', 'femminile'],
                'start_page': 8,
                'end_page': 15
            },
            'modulo_05_numeros': {
                'name': 'Números, Datas e Horas',
                'level': 'A1',
                'keywords': ['numeri', 'giorni', 'mesi', 'stagioni', 'ora', 'data'],
                'start_page': 15,
                'end_page': 25
            },
            'modulo_06_verbos_regulares': {
                'name': 'Verbos Regulares (-ARE, -ERE, -IRE)',
                'level': 'A2',
                'keywords': ['verbi regolari', 'parlare', 'vendere', 'partire', 'capire', 'presente'],
                'start_page': 26,
                'end_page': 35
            },
            'modulo_07_preposicoes': {
                'name': 'Preposições Simples e Articuladas',
                'level': 'A2',
                'keywords': ['preposizioni', 'semplici', 'articolate', 'di', 'a', 'da', 'in', 'con', 'su', 'per', 'tra'],
                'start_page': 36,
                'end_page': 48
            },
            'modulo_08_pronomes': {
                'name': 'Pronomes (Pessoais, Possessivos, Demonstrativos)',
                'level': 'A2',
                'keywords': ['pronomi', 'personali', 'possessivi', 'dimostrativi', 'questo', 'quello'],
                'start_page': 49,
                'end_page': 58
            },
            'modulo_09_passato_prossimo': {
                'name': 'Passato Prossimo',
                'level': 'A2',
                'keywords': ['passato prossimo', 'participio passato', 'ausiliare', 'essere', 'avere'],
                'start_page': 59,
                'end_page': 63
            },
            'modulo_10_imperfetto': {
                'name': 'Imperfetto',
                'level': 'B1',
                'keywords': ['imperfetto', 'passato', 'descrizioni', 'azioni abituali'],
                'start_page': 64,
                'end_page': 68
            },
            'modulo_11_futuro': {
                'name': 'Futuro Semplice',
                'level': 'B1',
                'keywords': ['futuro', 'semplice', 'previsioni', 'intenzioni'],
                'start_page': 69,
                'end_page': 75
            },
            'modulo_12_condizionale': {
                'name': 'Condizionale (Presente e Passato)',
                'level': 'B1',
                'keywords': ['condizionale', 'presente', 'passato', 'vorrei', 'desideri', 'ipotesi'],
                'start_page': 76,
                'end_page': 85
            }
        }
    
    def read_content(self) -> str:
        """Lê o conteúdo do arquivo fonte"""
        with open(self.source_file, 'r', encoding='utf-8') as f:
            return f.read()
    
    def extract_pages(self, content: str) -> Dict[int, str]:
        """Extrai páginas individuais do conteúdo"""
        pages = {}
        current_page = None
        current_content = []
        
        for line in content.split('\n'):
            # Detectar nova página
            page_match = re.match(r'^PÁGINA (\d+)$', line.strip())
            if page_match:
                # Salvar página anterior
                if current_page is not None:
                    pages[current_page] = '\n'.join(current_content)
                
                current_page = int(page_match.group(1))
                current_content = []
            elif current_page is not None:
                current_content.append(line)
        
        # Salvar última página
        if current_page is not None:
            pages[current_page] = '\n'.join(current_content)
        
        return pages
    
    def extract_module_content(self, pages: Dict[int, str], module_config: Dict) -> str:
        """Extrai conteúdo de um módulo específico"""
        start = module_config['start_page']
        end = module_config['end_page']
        
        content_parts = []
        for page_num in range(start, end + 1):
            if page_num in pages:
                content_parts.append(f"=== PÁGINA {page_num} ===\n")
                content_parts.append(pages[page_num])
                content_parts.append("\n\n")
        
        return ''.join(content_parts)
    
    def clean_content(self, text: str) -> str:
        """Limpa e formata o texto"""
        # Remover linhas de separadores
        text = re.sub(r'={40,}', '', text)
        
        # Remover múltiplas linhas vazias
        text = re.sub(r'\n{3,}', '\n\n', text)
        
        # Remover espaços no final das linhas
        lines = [line.rstrip() for line in text.split('\n')]
        text = '\n'.join(lines)
        
        return text.strip()
    
    def extract_vocabulary(self, text: str) -> List[str]:
        """Extrai palavras de vocabulário importantes"""
        # Procurar por palavras em maiúsculas ou após marcadores
        vocab = set()
        
        # Palavras em maiúsculas (exceto conectores comuns)
        uppercase_words = re.findall(r'\b[A-Z][A-Z]+\b', text)
        vocab.update([w for w in uppercase_words if len(w) > 2])
        
        # Palavras em negrito (entre **)
        bold_words = re.findall(r'\*\*([^*]+)\*\*', text)
        vocab.update(bold_words)
        
        # Exemplos (após "es." ou "Es.")
        examples = re.findall(r'[Ee]s\.?\s*:?\s*([^.\n]+)', text)
        vocab.update(examples)
        
        return sorted(list(vocab))[:30]  # Limitar a 30 palavras
    
    def create_lesson_structure(self, module_id: str, module_config: Dict, content: str) -> Dict:
        """Cria estrutura de lição em formato JSON"""
        cleaned_content = self.clean_content(content)
        vocabulary = self.extract_vocabulary(content)
        
        lesson = {
            'module_id': module_id,
            'module_name': module_config['name'],
            'level': module_config['level'],
            'content_italian': cleaned_content,
            'content_portuguese': '',  # Será preenchido manualmente
            'vocabulary': vocabulary,
            'keywords': module_config['keywords'],
            'estimated_time': 30,  # Padrão
            'difficulty': 2,  # Padrão
            'lesson_type': 'grammar',
            'notes': 'Conteúdo extraído de ItalB1-25.pdf via OCR'
        }
        
        return lesson
    
    def process_all_modules(self):
        """Processa todos os módulos e gera arquivos JSON"""
        print("🔍 Lendo arquivo fonte...")
        content = self.read_content()
        
        print("📄 Extraindo páginas...")
        pages = self.extract_pages(content)
        print(f"✅ {len(pages)} páginas extraídas")
        
        print("\n📚 Processando módulos...")
        for module_id, module_config in self.modules.items():
            print(f"\n  📖 {module_config['name']} (Páginas {module_config['start_page']}-{module_config['end_page']})")
            
            # Extrair conteúdo do módulo
            module_content = self.extract_module_content(pages, module_config)
            
            if not module_content.strip():
                print(f"    ⚠️  Conteúdo vazio, pulando...")
                continue
            
            # Criar estrutura da lição
            lesson = self.create_lesson_structure(module_id, module_config, module_content)
            
            # Salvar JSON
            output_file = self.output_dir / f"{module_id}.json"
            with open(output_file, 'w', encoding='utf-8') as f:
                json.dump(lesson, f, ensure_ascii=False, indent=2)
            
            print(f"    ✅ Salvo em: {output_file}")
            print(f"    📊 Vocabulário: {len(lesson['vocabulary'])} palavras")
            print(f"    📝 Conteúdo: {len(lesson['content_italian'])} caracteres")
        
        print("\n✨ Processamento concluído!")
    
    def generate_summary(self):
        """Gera arquivo de resumo com todos os módulos"""
        summary = {
            'source_file': str(self.source_file),
            'extraction_date': '2025-11-19',
            'total_modules': len(self.modules),
            'modules': []
        }
        
        for module_id, module_config in self.modules.items():
            json_file = self.output_dir / f"{module_id}.json"
            if json_file.exists():
                with open(json_file, 'r', encoding='utf-8') as f:
                    module_data = json.load(f)
                
                summary['modules'].append({
                    'id': module_id,
                    'name': module_config['name'],
                    'level': module_config['level'],
                    'pages': f"{module_config['start_page']}-{module_config['end_page']}",
                    'content_size': len(module_data['content_italian']),
                    'vocabulary_count': len(module_data['vocabulary'])
                })
        
        summary_file = self.output_dir / 'SUMMARY.json'
        with open(summary_file, 'w', encoding='utf-8') as f:
            json.dump(summary, f, ensure_ascii=False, indent=2)
        
        print(f"\n📋 Resumo salvo em: {summary_file}")

def main():
    import sys
    
    if len(sys.argv) < 2:
        print("Uso: python3 extract_sections.py <arquivo_txt> [diretorio_saida]")
        print("\nExemplo:")
        print("  python3 extract_sections.py ItalB1-25.txt modules_extracted")
        sys.exit(1)
    
    source_file = sys.argv[1]
    output_dir = sys.argv[2] if len(sys.argv) > 2 else 'modules_extracted'
    
    print("\n" + "="*80)
    print("📚 EXTRAÇÃO E ORGANIZAÇÃO DE CONTEÚDO")
    print("="*80 + "\n")
    
    organizer = ContentOrganizer(source_file, output_dir)
    organizer.process_all_modules()
    organizer.generate_summary()
    
    print("\n" + "="*80)
    print("✅ EXTRAÇÃO CONCLUÍDA COM SUCESSO!")
    print("="*80 + "\n")

if __name__ == '__main__':
    main()
