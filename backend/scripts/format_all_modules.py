#!/usr/bin/env python3
"""
Script para formatar todos os módulos do italiano A1-B1
Converte texto OCR em HTML formatado com CSS e cria exercícios de qualidade
"""

import json
import re
from pathlib import Path
from typing import Dict, List

class ModuleFormatter:
    def __init__(self):
        self.css_classes = {
            'intro': 'intro',
            'verbs': 'verbs-section',
            'rule': 'rule-box',
            'tip': 'tip-box',
            'example': 'example',
            'important': 'important-box'
        }
    
    def clean_ocr_text(self, text: str) -> str:
        """Remove artefatos comuns de OCR"""
        # Remove múltiplos espaços
        text = re.sub(r'\s+', ' ', text)
        # Remove espaços antes de pontuação
        text = re.sub(r'\s+([.,;:!?])', r'\1', text)
        # Corrige espaços após pontuação
        text = re.sub(r'([.,;:!?])([A-Za-z])', r'\1 \2', text)
        return text.strip()
    
    def format_as_html(self, content: str, module_name: str) -> str:
        """Converte conteúdo texto em HTML formatado"""
        html_parts = []
        
        # Dividir em seções
        sections = content.split('\n\n')
        
        for section in sections:
            section = self.clean_ocr_text(section)
            if not section:
                continue
            
            # Identificar tipo de seção e formatar
            if any(word in section.lower() for word in ['introduzione', 'benvenuto', 'iniziamo']):
                html_parts.append(f"<div class='intro'>\n<p>{section}</p>\n</div>\n")
            
            elif any(word in section.lower() for word in ['verbo', 'coniugazione', 'presente', 'passato']):
                html_parts.append(f"<div class='verbs-section'>\n<p>{section}</p>\n</div>\n")
            
            elif any(word in section.lower() for word in ['regola', 'attenzione', 'importante']):
                html_parts.append(f"<div class='rule-box'>\n<p>{section}</p>\n</div>\n")
            
            elif any(word in section.lower() for word in ['esempio', 'per esempio', 'ad esempio']):
                html_parts.append(f"<p class='example'>{section}</p>\n")
            
            elif any(word in section.lower() for word in ['tip', 'suggerimento', 'consiglio', 'cils']):
                html_parts.append(f"<div class='tip-box'>\n<p>{section}</p>\n</div>\n")
            
            else:
                html_parts.append(f"<p>{section}</p>\n")
        
        return '\n'.join(html_parts)
    
    def extract_exercises(self, exercises: List[str], module_name: str) -> List[Dict]:
        """Extrai e formata exercícios do módulo"""
        formatted_exercises = []
        
        for i, exercise_text in enumerate(exercises, 1):
            # Tenta identificar tipo de exercício
            if '?' in exercise_text or any(opt in exercise_text for opt in ['A)', 'B)', 'C)', 'D)']):
                # Exercício de múltipla escolha
                formatted_exercises.append(self.create_multiple_choice(exercise_text, i, module_name))
            elif '______' in exercise_text or '...' in exercise_text:
                # Exercício de completar
                formatted_exercises.append(self.create_fill_in_blank(exercise_text, i, module_name))
        
        return formatted_exercises
    
    def create_multiple_choice(self, text: str, number: int, module_name: str) -> Dict:
        """Cria exercício de múltipla escolha"""
        # Estrutura básica
        lines = text.split('\n')
        question_text = lines[0] if lines else text[:100]
        
        return {
            "question_text": self.clean_ocr_text(question_text),
            "question_type": "multiple_choice",
            "difficulty": 2,
            "order": number,
            "options": [
                {"text": "Opção A (revisar)", "is_correct": True},
                {"text": "Opção B (revisar)", "is_correct": False},
                {"text": "Opção C (revisar)", "is_correct": False},
                {"text": "Opção D (revisar)", "is_correct": False}
            ],
            "explanation": f"Revisar explicação do exercício {number} - {module_name}"
        }
    
    def create_fill_in_blank(self, text: str, number: int, module_name: str) -> Dict:
        """Cria exercício de completar lacuna"""
        return {
            "question_text": self.clean_ocr_text(text),
            "question_type": "fill_in_blank",
            "difficulty": 2,
            "order": number,
            "correct_answer": "[REVISAR]",
            "alternatives": [],
            "explanation": f"Revisar explicação do exercício {number} - {module_name}"
        }
    
    def format_module(self, module_path: Path) -> Dict:
        """Formata um módulo completo"""
        print(f"📝 Formatando: {module_path.name}")
        
        with open(module_path, 'r', encoding='utf-8') as f:
            module = json.load(f)
        
        # Formatar conteúdo italiano em HTML
        content_html = self.format_as_html(
            module['content_italian'], 
            module['module_name']
        )
        
        # Extrair e formatar exercícios
        exercises = []
        if module.get('exercises'):
            exercises = self.extract_exercises(
                module['exercises'],
                module['module_name']
            )
        
        # Criar estrutura formatada
        formatted = {
            "module_id": module['module_id'],
            "module_name": module['module_name'],
            "level": module['level'],
            "order": module.get('order', 1),
            "content_italian": content_html,
            "content_portuguese": f"[Tradução para revisar] {module['module_name']}",
            "estimated_time": 20 + len(exercises) * 3,  # Base 20min + 3min por exercício
            "difficulty": self.calculate_difficulty(module['level']),
            "exercises": exercises,
            "metadata": {
                "formatted_at": "2025-11-20",
                "source": module_path.name,
                "needs_review": True,
                "review_notes": "Exercícios precisam ser revisados manualmente"
            }
        }
        
        print(f"  ✅ {len(exercises)} exercícios criados")
        return formatted
    
    def calculate_difficulty(self, level: str) -> int:
        """Calcula dificuldade baseada no nível CEFR"""
        difficulty_map = {
            'A1': 1,
            'A2': 2,
            'B1': 3,
            'B2': 4,
            'C1': 5,
            'C2': 5
        }
        return difficulty_map.get(level, 2)
    
    def format_all_modules(self, input_dir: Path, output_dir: Path):
        """Formata todos os módulos"""
        output_dir.mkdir(parents=True, exist_ok=True)
        
        module_files = sorted(input_dir.glob('modulo_*.json'))
        
        print(f"\n🚀 Iniciando formatação de {len(module_files)} módulos\n")
        
        summary = {
            "total_modules": len(module_files),
            "formatted_modules": [],
            "total_exercises": 0,
            "formatted_at": "2025-11-20"
        }
        
        for module_path in module_files:
            try:
                formatted = self.format_module(module_path)
                
                # Salvar módulo formatado
                output_file = output_dir / f"{module_path.stem}_formatted.json"
                with open(output_file, 'w', encoding='utf-8') as f:
                    json.dump(formatted, f, ensure_ascii=False, indent=2)
                
                summary['formatted_modules'].append({
                    "name": formatted['module_name'],
                    "level": formatted['level'],
                    "exercises": len(formatted['exercises']),
                    "time": formatted['estimated_time'],
                    "file": output_file.name
                })
                summary['total_exercises'] += len(formatted['exercises'])
                
                print(f"  💾 Salvo: {output_file.name}\n")
                
            except Exception as e:
                print(f"  ❌ Erro: {e}\n")
        
        # Salvar resumo
        summary_file = output_dir / 'FORMAT_SUMMARY.json'
        with open(summary_file, 'w', encoding='utf-8') as f:
            json.dump(summary, f, ensure_ascii=False, indent=2)
        
        print(f"\n✅ Formatação concluída!")
        print(f"📊 Total: {summary['total_modules']} módulos, {summary['total_exercises']} exercícios")
        print(f"📄 Resumo salvo em: {summary_file}\n")

def main():
    import sys
    
    if len(sys.argv) < 3:
        print("Uso: python3 format_all_modules.py <dir_entrada> <dir_saida>")
        print("\nExemplo:")
        print("  python3 format_all_modules.py modules_organized/ modules_formatted/")
        sys.exit(1)
    
    input_dir = Path(sys.argv[1])
    output_dir = Path(sys.argv[2])
    
    if not input_dir.exists():
        print(f"❌ Diretório não encontrado: {input_dir}")
        sys.exit(1)
    
    formatter = ModuleFormatter()
    formatter.format_all_modules(input_dir, output_dir)

if __name__ == '__main__':
    main()
