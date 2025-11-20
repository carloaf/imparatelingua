#!/usr/bin/env python3
"""
Script para criar exercícios de qualidade para cada módulo
Baseado no conteúdo teórico e nos exercícios extraídos do PDF
"""

import json
import re
from pathlib import Path
from typing import List, Dict

class ExerciseCreator:
    """Cria exercícios de qualidade baseados no conteúdo dos módulos"""
    
    def __init__(self):
        self.exercise_templates = {
            'alfabeto': self.create_alfabeto_exercises,
            'saudacoes': self.create_saudacoes_exercises,
            'verbos_base': self.create_verbos_base_exercises,
            'artigos': self.create_artigos_exercises,
            'numeros': self.create_numeros_exercises,
            'verbos_regulares': self.create_verbos_regulares_exercises,
            'preposicoes': self.create_preposicoes_exercises,
            'pronomes': self.create_pronomes_exercises,
            'passato': self.create_passato_exercises,
            'imperfetto': self.create_imperfetto_exercises,
            'futuro': self.create_futuro_exercises,
            'condizionale': self.create_condizionale_exercises,
        }
    
    def create_alfabeto_exercises(self) -> List[Dict]:
        """Exercícios para Alfabeto e Fonética"""
        return [
            {
                "question_text": "Come si pronuncia la parola 'ciao'?",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 1,
                "options": [
                    {"text": "[tʃao] - som dolce", "is_correct": True},
                    {"text": "[kiao] - som duro", "is_correct": False},
                    {"text": "[siao] - som sibilante", "is_correct": False},
                    {"text": "[ʃiao] - som de 'x'", "is_correct": False}
                ],
                "explanation": "CI tem som dolce [tʃ], como em 'tchau'. Exemplo: ciao, cinema, cena."
            },
            {
                "question_text": "Quale parola ha la 'G' con suono duro?",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 2,
                "options": [
                    {"text": "Gatto", "is_correct": True},
                    {"text": "Gelato", "is_correct": False},
                    {"text": "Giorno", "is_correct": False},
                    {"text": "Gente", "is_correct": False}
                ],
                "explanation": "GA/GO/GU têm som duro [g]. GE/GI têm som dolce [dʒ]. Gatto = [gato]."
            },
            {
                "question_text": "Come si pronuncia 'GLI' in 'famiglia'?",
                "question_type": "multiple_choice",
                "difficulty": 2,
                "order": 3,
                "options": [
                    {"text": "Como 'lh' em português (família)", "is_correct": True},
                    {"text": "Como 'gli' separado", "is_correct": False},
                    {"text": "Como 'li'", "is_correct": False},
                    {"text": "Silencioso", "is_correct": False}
                ],
                "explanation": "GLI = [ʎ] (som palatal como 'lh'). Exemplos: figlio, foglio, aglio."
            },
            {
                "question_text": "Quale combinazione fa il suono [ʃ] (come 'x' português)?",
                "question_type": "multiple_choice",
                "difficulty": 2,
                "order": 4,
                "options": [
                    {"text": "SCI/SCE", "is_correct": True},
                    {"text": "SCA/SCO", "is_correct": False},
                    {"text": "SI/SE", "is_correct": False},
                    {"text": "CHI/CHE", "is_correct": False}
                ],
                "explanation": "SCI/SCE = [ʃi/ʃe]. Exemplos: scivolo, pesce, piscina, sciare."
            },
            {
                "question_text": "Come si pronuncia 'GN' in 'gnocchi'?",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 5,
                "options": [
                    {"text": "Como 'nh' em português (nhoque)", "is_correct": True},
                    {"text": "Como 'gn' separado", "is_correct": False},
                    {"text": "Como 'n' simples", "is_correct": False},
                    {"text": "O 'G' é silencioso", "is_correct": False}
                ],
                "explanation": "GN = [ɲ] (som nasal como 'nh'). Exemplos: lavagna, agnello, bagno."
            },
            {
                "question_text": "Completa com o artigo correto: '___ caffè' (o café)",
                "question_type": "fill_in_blank",
                "difficulty": 1,
                "order": 6,
                "correct_answer": "il",
                "alternatives": ["un", "lo", "la"],
                "explanation": "Caffè é masculino e começa com C (consoante simples) = IL caffè."
            },
            {
                "question_text": "Quale saluto si usa di mattina in modo formale?",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 7,
                "options": [
                    {"text": "Buongiorno", "is_correct": True},
                    {"text": "Buona sera", "is_correct": False},
                    {"text": "Ciao", "is_correct": False},
                    {"text": "Buona notte", "is_correct": False}
                ],
                "explanation": "Buongiorno = manhã até meio-dia. Ciao = informal. Buona sera = tarde/noite."
            },
            {
                "question_text": "Come si chiede il nome in modo informale?",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 8,
                "options": [
                    {"text": "Come ti chiami?", "is_correct": True},
                    {"text": "Come si chiama?", "is_correct": False},
                    {"text": "Qual è il Suo nome?", "is_correct": False},
                    {"text": "Chi sei?", "is_correct": False}
                ],
                "explanation": "TU (informal) = Come TI chiami? | LEI (formal) = Come SI chiama?"
            }
        ]
    
    def create_saudacoes_exercises(self) -> List[Dict]:
        """Exercícios para Saudações"""
        return [
            {
                "question_text": "Completa: 'Io ___ chiamo Marco.'",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 1,
                "options": [
                    {"text": "mi", "is_correct": True},
                    {"text": "ti", "is_correct": False},
                    {"text": "si", "is_correct": False},
                    {"text": "ci", "is_correct": False}
                ],
                "explanation": "Verbo CHIAMARSI: io MI chiamo, tu TI chiami, lui/lei SI chiama."
            },
            {
                "question_text": "Quando si usa 'Buona notte'?",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 2,
                "options": [
                    {"text": "Quando si va a dormire (despedida)", "is_correct": True},
                    {"text": "Quando si arriva di sera", "is_correct": False},
                    {"text": "A qualsiasi ora della notte", "is_correct": False},
                    {"text": "Di mattina", "is_correct": False}
                ],
                "explanation": "Buona notte = despedida antes de dormir. Buona sera = chegada à noite."
            }
        ]
    
    def create_verbos_base_exercises(self) -> List[Dict]:
        """Exercícios para ESSERE, AVERE, STARE"""
        return [
            {
                "question_text": "Completa: 'Io ___ italiano.'",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 1,
                "options": [
                    {"text": "sono", "is_correct": True},
                    {"text": "sei", "is_correct": False},
                    {"text": "è", "is_correct": False},
                    {"text": "siamo", "is_correct": False}
                ],
                "explanation": "ESSERE: io SONO, tu sei, lui/lei è. Indica nacionalidade, profissão, característica."
            },
            {
                "question_text": "Completa: 'Noi ___ una casa grande.'",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 2,
                "options": [
                    {"text": "abbiamo", "is_correct": True},
                    {"text": "avete", "is_correct": False},
                    {"text": "hanno", "is_correct": False},
                    {"text": "ho", "is_correct": False}
                ],
                "explanation": "AVERE: io ho, tu hai, noi ABBIAMO, voi avete, loro hanno."
            }
        ]
    
    def create_artigos_exercises(self) -> List[Dict]:
        """Exercícios para Artigos"""
        return [
            {
                "question_text": "Qual é o artigo correto: '___ studente' (o estudante)",
                "question_type": "multiple_choice",
                "difficulty": 2,
                "order": 1,
                "options": [
                    {"text": "lo", "is_correct": True},
                    {"text": "il", "is_correct": False},
                    {"text": "l'", "is_correct": False},
                    {"text": "un", "is_correct": False}
                ],
                "explanation": "LO = masculino singular antes de S+consonante, Z, X, Y, GN, PS. Il studente ❌ → Lo studente ✓"
            }
        ]
    
    def create_numeros_exercises(self) -> List[Dict]:
        """Exercícios para Números"""
        return [
            {
                "question_text": "Come si dice '15' in italiano?",
                "question_type": "multiple_choice",
                "difficulty": 1,
                "order": 1,
                "options": [
                    {"text": "quindici", "is_correct": True},
                    {"text": "cinquanta", "is_correct": False},
                    {"text": "cinque", "is_correct": False},
                    {"text": "quinto", "is_correct": False}
                ],
                "explanation": "15 = quindici. Atenção: 50 = cinquanta, 5 = cinque."
            }
        ]
    
    def create_verbos_regulares_exercises(self) -> List[Dict]:
        """Exercícios para Verbos Regulares"""
        return [
            {
                "question_text": "Completa: 'Loro ___ (parlare) italiano.'",
                "question_type": "multiple_choice",
                "difficulty": 2,
                "order": 1,
                "options": [
                    {"text": "parlano", "is_correct": True},
                    {"text": "parlate", "is_correct": False},
                    {"text": "parla", "is_correct": False},
                    {"text": "parliamo", "is_correct": False}
                ],
                "explanation": "Verbos -ARE: io parlo, tu parli, lui parla, noi parliamo, voi parlate, loro PARLANO."
            }
        ]
    
    def create_preposicoes_exercises(self) -> List[Dict]:
        """Exercícios para Preposições"""
        return [
            {
                "question_text": "Quale preposizione articolata è corretta: 'Vado ___ cinema' (ao cinema)",
                "question_type": "multiple_choice",
                "difficulty": 2,
                "order": 1,
                "options": [
                    {"text": "al", "is_correct": True},
                    {"text": "del", "is_correct": False},
                    {"text": "nel", "is_correct": False},
                    {"text": "sul", "is_correct": False}
                ],
                "explanation": "A + IL = AL. Vado AL cinema (vou AO cinema). DI+IL=DEL, IN+IL=NEL, SU+IL=SUL."
            }
        ]
    
    def create_pronomes_exercises(self) -> List[Dict]:
        """Exercícios para Pronomes"""
        return [
            {
                "question_text": "Quale pronome sostituisce 'il libro' in 'Leggo il libro'?",
                "question_type": "multiple_choice",
                "difficulty": 2,
                "order": 1,
                "options": [
                    {"text": "lo (Lo leggo)", "is_correct": True},
                    {"text": "la (La leggo)", "is_correct": False},
                    {"text": "li (Li leggo)", "is_correct": False},
                    {"text": "le (Le leggo)", "is_correct": False}
                ],
                "explanation": "IL libro (masculino singular) = LO. Leggo il libro → Lo leggo."
            }
        ]
    
    def create_passato_exercises(self) -> List[Dict]:
        """Exercícios para Passato Prossimo"""
        return [
            {
                "question_text": "Completa: 'Ieri io ___ (andare) al mare.'",
                "question_type": "multiple_choice",
                "difficulty": 3,
                "order": 1,
                "options": [
                    {"text": "sono andato/a", "is_correct": True},
                    {"text": "ho andato", "is_correct": False},
                    {"text": "andavo", "is_correct": False},
                    {"text": "andrò", "is_correct": False}
                ],
                "explanation": "ANDARE usa auxiliar ESSERE no passato prossimo: io SONO andato/a. Verbos de movimento = ESSERE."
            }
        ]
    
    def create_imperfetto_exercises(self) -> List[Dict]:
        """Exercícios para Imperfetto"""
        return [
            {
                "question_text": "Quando si usa l'imperfetto?",
                "question_type": "multiple_choice",
                "difficulty": 3,
                "order": 1,
                "options": [
                    {"text": "Per azioni abituali nel passato", "is_correct": True},
                    {"text": "Per azioni concluse nel passato", "is_correct": False},
                    {"text": "Per azioni future", "is_correct": False},
                    {"text": "Per il presente", "is_correct": False}
                ],
                "explanation": "Imperfetto = ações habituais, descrições, condições no passado. Ex: 'Quando ero bambino, giocavo sempre.'"
            }
        ]
    
    def create_futuro_exercises(self) -> List[Dict]:
        """Exercícios para Futuro"""
        return [
            {
                "question_text": "Completa: 'Domani io ___ (partire) per Roma.'",
                "question_type": "multiple_choice",
                "difficulty": 3,
                "order": 1,
                "options": [
                    {"text": "partirò", "is_correct": True},
                    {"text": "parto", "is_correct": False},
                    {"text": "sono partito", "is_correct": False},
                    {"text": "partivo", "is_correct": False}
                ],
                "explanation": "Futuro semplice -IRE: io partirò, tu partirai, lui partirà. Domani = futuro."
            }
        ]
    
    def create_condizionale_exercises(self) -> List[Dict]:
        """Exercícios para Condizionale"""
        return [
            {
                "question_text": "Come si traduce 'Eu gostaria de um café'?",
                "question_type": "multiple_choice",
                "difficulty": 3,
                "order": 1,
                "options": [
                    {"text": "Vorrei un caffè", "is_correct": True},
                    {"text": "Voglio un caffè", "is_correct": False},
                    {"text": "Vorrò un caffè", "is_correct": False},
                    {"text": "Volevo un caffè", "is_correct": False}
                ],
                "explanation": "Condizionale presente de VOLERE = vorrei (educado/cortês). Voglio = presente, Vorrò = futuro."
            }
        ]
    
    def add_exercises_to_module(self, module_file: Path, exercise_type: str):
        """Adiciona exercícios a um módulo específico"""
        print(f"📝 Processando: {module_file.name}")
        
        with open(module_file, 'r', encoding='utf-8') as f:
            module = json.load(f)
        
        # Criar exercícios baseados no tipo
        if exercise_type in self.exercise_templates:
            exercises = self.exercise_templates[exercise_type]()
            module['exercises'] = exercises
            module['metadata']['exercises_created'] = True
            module['metadata']['total_exercises'] = len(exercises)
            
            # Atualizar tempo estimado (3min por exercício)
            module['estimated_time'] = 20 + (len(exercises) * 3)
            
            # Salvar módulo atualizado
            with open(module_file, 'w', encoding='utf-8') as f:
                json.dump(module, f, ensure_ascii=False, indent=2)
            
            print(f"  ✅ {len(exercises)} exercícios criados")
        else:
            print(f"  ⚠️ Tipo '{exercise_type}' não encontrado")

def main():
    creator = ExerciseCreator()
    
    # Mapeamento de módulos para tipos de exercícios
    module_mapping = {
        'modulo_01_alfabeto_formatted.json': 'alfabeto',
        'modulo_02_saudacoes_formatted.json': 'saudacoes',
        'modulo_03_verbos_formatted.json': 'verbos_base',
        'modulo_04_artigos_formatted.json': 'artigos',
        'modulo_05_numeros_formatted.json': 'numeros',
        'modulo_06_verbos_regulares_formatted.json': 'verbos_regulares',
        'modulo_07_preposicoes_formatted.json': 'preposicoes',
        'modulo_08_pronomes_formatted.json': 'pronomes',
        'modulo_09_passato_prossimo_formatted.json': 'passato',
        'modulo_10_imperfetto_formatted.json': 'imperfetto',
        'modulo_11_futuro_formatted.json': 'futuro',
        'modulo_12_condizionale_formatted.json': 'condizionale',
    }
    
    modules_dir = Path('/home/dellno/worksapace/imparalingua/backend/storage/app/imports/modules_formatted')
    
    print("🚀 Criando exercícios de qualidade para todos os módulos\n")
    
    total_exercises = 0
    for module_file, exercise_type in module_mapping.items():
        file_path = modules_dir / module_file
        if file_path.exists():
            creator.add_exercises_to_module(file_path, exercise_type)
            with open(file_path, 'r') as f:
                module = json.load(f)
                total_exercises += len(module.get('exercises', []))
        else:
            print(f"❌ Arquivo não encontrado: {module_file}")
    
    print(f"\n✅ Processo concluído!")
    print(f"📊 Total de exercícios criados: {total_exercises}")

if __name__ == '__main__':
    main()
