# Guia de Exames CILS B1

## Sobre os Exames CILS

CILS (Certificazione di Italiano come Lingua Straniera) é o certificado de italiano como língua estrangeira oficial da Università per Stranieri di Siena. É reconhecido internacionalmente e atesta competência na língua italiana.

### Níveis CILS

O CILS segue o Quadro Europeu Comum de Referência para Línguas (QCER):
- **A1** - Iniciante (Contato)
- **A2** - Elementar (Sobrevivência)
- **B1** - Intermediário (Limiar) ⭐ **Foco deste projeto**
- **B2** - Intermediário Superior (Progresso)
- **C1** - Avançado (Eficácia)
- **C2** - Proficiência (Maestria)

## Nível B1 - Requisitos

### Competências Linguísticas

No nível B1, o candidato deve ser capaz de:

**Compreensão:**
- Compreender os pontos essenciais de uma conversa clara sobre assuntos familiares
- Compreender textos escritos de uso corrente relacionados ao trabalho, escola, lazer
- Entender descrições de eventos, sentimentos e desejos em cartas pessoais

**Expressão:**
- Lidar com a maioria das situações de viagem em países de língua italiana
- Produzir textos simples sobre temas conhecidos ou de interesse pessoal
- Descrever experiências, eventos, sonhos, esperanças e ambições
- Justificar brevemente opiniões e planos

**Interação:**
- Participar de conversas sobre temas familiares, de interesse pessoal ou cotidiano
- Trocar informações de forma simples e direta sobre assuntos rotineiros

## Estrutura dos Exames CILS

### Sessões Anuais

Os exames CILS acontecem duas vezes por ano:
- **Sessione di Giugno** (Junho)
- **Sessione di Dicembre** (Dezembro)

### Partes do Exame B1

O exame CILS B1 é composto de 5 partes:

1. **Ascolto** (Compreensão Oral) - 30 minutos
   - Compreensão de textos orais sobre temas cotidianos
   
2. **Comprensione della Lettura** (Compreensão de Leitura) - 70 minutos
   - Leitura e compreensão de textos de diferentes tipologias
   
3. **Analisi delle Strutture di Comunicazione** (Análise das Estruturas) - 70 minutos
   - Gramática
   - Vocabulário
   - Uso correto das estruturas linguísticas
   
4. **Produzione Scritta** (Produção Escrita) - 70 minutos
   - Redação de textos de diferentes tipos
   
5. **Produzione Orale** (Produção Oral) - 10 minutos
   - Conversação com examinadores

## Temas Comuns no B1

### Gramática

#### Tempos Verbais
- Presente, Passato Prossimo, Imperfetto
- Futuro Semplice
- Condizionale Presente
- **Congiuntivo Presente e Imperfetto** ⭐ (muito importante)
- Imperativo

#### Estruturas Gramaticais
- Pronomi diretti e indiretti
- Pronomi combinati
- Pronomi relativi (che, cui, quale)
- Particelle pronominali (ci, ne)
- Preposizioni semplici e articolate
- Periodo ipotetico (1º e 2º tipo)

### Vocabulário

#### Áreas Temáticas
- Vida cotidiana e rotina
- Trabalho e profissões
- Tempo livre e hobbies
- Viagens e turismo
- Compras e serviços
- Saúde e bem-estar
- Educação e formação
- Relacionamentos pessoais
- Casa e habitação
- Alimentação
- Meios de transporte
- Meios de comunicação

#### Expressões Idiomáticas
- Modi di dire comuns
- Phrasal expressions
- Colocações típicas

## Como Preparar Questões para B1

### Princípios Gerais

1. **Contextualização**: Sempre forneça contexto adequado
2. **Realismo**: Use situações da vida real
3. **Clareza**: Enunciados devem ser claros e diretos
4. **Dificuldade Progressiva**: Variar de 1 (fácil) a 5 (difícil)
5. **Justificativas Educativas**: Explique não apenas a resposta certa, mas também por que as outras estão erradas

### Tipos de Questões

#### 1. Múltipla Escolha (Multiple Choice)
Ideal para:
- Gramática (tempos verbais, preposições)
- Vocabulário (sinônimos, antônimos)
- Compreensão de texto
- Expressões idiomáticas

**Exemplo:**
```json
{
  "question_text": "Completa: Penso che Maria _____ ragione.",
  "question_type": "multiple_choice",
  "difficulty": 3,
  "context": "Congiuntivo presente com verbos de opinião",
  "explanation": "Verbos de opinião exigem congiuntivo...",
  "answers": [
    {"answer_text": "abbia", "is_correct": true, "justification": "..."},
    {"answer_text": "ha", "is_correct": false, "justification": "..."}
  ]
}
```

#### 2. Preencher Lacuna (Fill in the Blank)
Ideal para:
- Preposições
- Artigos
- Pronomes
- Conjugações verbais específicas

**Exemplo:**
```json
{
  "question_text": "Vado _____ medico domani.",
  "question_type": "fill_in_blank",
  "difficulty": 2,
  "context": "Preposizioni: andare da + professione",
  "answers": [
    {"answer_text": "dal", "is_correct": true, "justification": "..."}
  ]
}
```

#### 3. Verdadeiro/Falso (True/False)
Ideal para:
- Verificar conhecimento de regras gramaticais
- Compreensão de textos curtos
- Conceitos linguísticos

### Distribuição Recomendada por Exame B1

Para um exame completo de B1 (foco em gramática e vocabulário):

- **40%** - Gramática (tempos verbais, estruturas)
- **30%** - Vocabulário e expressões
- **30%** - Interpretação de texto

### Níveis de Dificuldade

**Nível 1-2** (Fácil):
- Estruturas básicas do B1
- Vocabulário comum
- Situações simples

**Nível 3** (Médio):
- Estruturas típicas do B1
- Congiuntivo presente
- Expressões idiomáticas comuns

**Nível 4-5** (Difícil):
- Congiuntivo imperfetto
- Períodos hipotéticos
- Nuances linguísticas
- Expressões menos comuns

## Estrutura de Arquivo JSON para Exame CILS B1

```json
{
  "exam": {
    "name": "CILS B1 - Dicembre 2022",
    "level": "B1",
    "year": 2022,
    "description": "Certificazione di Italiano come Lingua Straniera - Livello B1 - Sessione di Dicembre 2022",
    "is_official": true,
    "session": "Dicembre",
    "exam_code": "CILS_B1_DIC_2022",
    "source_url": "https://cils.unistrasi.it" // opcional
  },
  "questions": [
    // Array de questões (mínimo 30-40 para um exame completo)
  ]
}
```

## Checklist para Criação de Exame B1

- [ ] Nome do exame inclui nível e sessão
- [ ] `is_official: true` se for exame real CILS
- [ ] `session` especificada (Giugno ou Dicembre)
- [ ] `exam_code` único e descritivo
- [ ] Mínimo de 30 questões para exame completo
- [ ] Distribuição adequada de dificuldades (1-5)
- [ ] Mix de tipos de questão (múltipla escolha, lacuna, V/F)
- [ ] Todas as questões têm `context` explicativo
- [ ] Todas as questões têm `explanation` educacional
- [ ] Todas as respostas têm `justification` detalhada
- [ ] Gramática: foco em congiuntivo, preposições, pronomes
- [ ] Vocabulário: temas do cotidiano, expressões idiomáticas
- [ ] Interpretação: textos autênticos de 100-200 palavras

## Recursos para Criar Conteúdo B1

### Temas Gramaticais Prioritários

1. **Congiuntivo Presente**
   - Verbos de opinião (pensare, credere, supporre)
   - Verbos de desejo (volere, desiderare, sperare)
   - Expressões impessoais (è necessario che, bisogna che)

2. **Congiuntivo Imperfetto**
   - Períodos hipotéticos irreais
   - Concordância de tempos

3. **Preposições**
   - Preposições simples e articuladas
   - Verbos que regem preposições específicas
   - Expressões com preposições

4. **Pronomes**
   - Diretos e indiretos
   - Combinados
   - Relativos (che, cui)
   - Partículas (ci, ne)

### Vocabulário Temático B1

**Trabalho:**
- cercare lavoro, fare un colloquio, curriculum
- mestiere, professione, occupazione
- stipendio, orario, ferie

**Viagem:**
- prenotare, alloggio, biglietto
- aeroporto, stazione, porto
- valigia, bagaglio, documenti

**Saúde:**
- medico, farmacia, ospedale
- sintomi, malattia, cura
- appuntamento, ricetta, medicina

**Educação:**
- scuola, università, corso
- studiare, imparare, esame
- laurea, diploma, certificato

## Exemplos de Questões B1

Veja os arquivos:
- `backend/storage/app/imports/exemplo_questoes.json` - Exemplo básico
- `backend/storage/app/imports/cils_b1_dic_2022.json` - Exame CILS completo

## Comandos Úteis

```bash
# Importar exame CILS B1
docker compose exec app php artisan questions:import cils_b1_dic_2022.json

# Substituir exame existente
docker compose exec app php artisan questions:import cils_b1_dic_2022.json --replace

# Verificar exames no banco
docker compose exec app php artisan tinker --execute="App\Models\Exam::where('level', 'B1')->get(['name', 'session', 'year', 'is_official'])"
```

## Referências

- [CILS Unistrasi](https://cils.unistrasi.it) - Site oficial
- [Quadro Europeu Comum de Referência](https://www.coe.int/en/web/common-european-framework-reference-languages)
- [Grammatica Italiana](https://www.italian-verbs.com)
- [Dizionario Online](https://www.treccani.it)

## Próximos Passos

1. ✅ Estrutura para exames CILS implementada
2. ✅ Campos específicos (is_official, session, exam_code)
3. ✅ Badge visual para exames oficiais
4. ✅ Exemplo de exame B1 Dicembre 2022
5. 🔄 Expandir com mais questões (meta: 40-50 questões por exame)
6. 📋 Adicionar exames de outras sessões (Giugno 2022, Dicembre 2021, etc.)
7. 📋 Criar modo de estudo por tópico gramatical
8. 📋 Adicionar exercícios de áudio (Ascolto)
9. 📋 Sistema de simulados completos

---

**Foco do Projeto:** Nível B1 - Intermediário
**Objetivo:** Preparação completa para certificação CILS B1
