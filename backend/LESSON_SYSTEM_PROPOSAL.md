# 📚 Sistema de Lições - Proposta de Implementação

## Visão Geral

Integração do conteúdo didático estruturado (ConteudoItaliano2025.txt) ao sistema ImparaTeLingua, criando um **curso progressivo e interativo** de italiano.

---

## 🎯 Objetivos

1. **Transformar conteúdo estático em lições interativas**
2. **Criar jornada de aprendizado estruturada**
3. **Integrar teoria (lições) com prática (exercícios/questões)**
4. **Rastrear progresso do usuário por lição e módulo**
5. **Gamificar o aprendizado**

---

## 📊 Arquitetura do Sistema

### Novas Tabelas do Banco de Dados

#### 1. `courses` (Cursos)
```sql
- id
- title (ex: "Italiano Básico")
- slug
- description
- level (A1, A2, B1, B2, C1, C2)
- image_url
- is_active
- order
- created_at, updated_at
```

#### 2. `modules` (Módulos de Curso)
```sql
- id
- course_id (FK)
- title (ex: "PARTE I: FUNDAMENTOS E ESTRUTURAS BÁSICAS")
- slug
- description
- order
- created_at, updated_at
```

#### 3. `lessons` (Lições)
```sql
- id
- module_id (FK)
- title (ex: "Página 1: O Alfabeto e a Pronúncia de C e G")
- slug
- content_italian (texto em italiano)
- content_portuguese (explicações em português)
- lesson_type (theory, grammar, vocabulary, pronunciation, exercise)
- difficulty (1-5)
- estimated_time (minutos)
- order
- is_premium (boolean)
- created_at, updated_at
```

#### 4. `lesson_sections` (Seções da Lição)
```sql
- id
- lesson_id (FK)
- title (ex: "O Alfabeto", "Consoantes Duplas")
- content
- section_type (text, audio, video, example, exercise, tip)
- order
- created_at, updated_at
```

#### 5. `lesson_exercises` (Exercícios da Lição)
```sql
- id
- lesson_id (FK)
- question_id (FK - referência às questões existentes)
- order
- created_at, updated_at
```

#### 6. `user_lesson_progress` (Progresso nas Lições)
```sql
- id
- user_id (FK)
- lesson_id (FK)
- status (not_started, in_progress, completed, mastered)
- time_spent (segundos)
- completion_percentage
- score (0-100)
- started_at
- completed_at
- last_accessed_at
- created_at, updated_at
```

#### 7. `user_course_progress` (Progresso nos Cursos)
```sql
- id
- user_id (FK)
- course_id (FK)
- lessons_completed
- total_lessons
- current_lesson_id (FK)
- status (not_started, in_progress, completed)
- total_time_spent
- enrolled_at
- completed_at
- created_at, updated_at
```

---

## 🎨 Interface do Usuário (Frontend)

### 1. **Página de Cursos** (`/courses`)
- Grid de cursos disponíveis
- Cards com:
  - Título e descrição
  - Nível (A1-C2)
  - Número de lições
  - Progresso do usuário (se inscrito)
  - Badge "Premium" (se aplicável)
  - Botão "Iniciar" ou "Continuar"

### 2. **Página do Curso** (`/course/:id`)
- Visão geral do curso
- Lista de módulos e lições
- Progresso geral (%)
- Tempo estimado total
- Estrutura em árvore expansível:
  ```
  📚 Italiano Básico
    📖 PARTE I: Fundamentos
      ✅ Lição 1: Alfabeto e Pronúncia
      📝 Lição 2: Fonética (em progresso)
      🔒 Lição 3: Saudações (bloqueada)
  ```

### 3. **Página da Lição** (`/lesson/:id`)

#### Layout Responsivo de 2 Colunas:

**Coluna Esquerda (Conteúdo):**
- Título da lição
- Navegação: Anterior | Índice | Próxima
- Conteúdo estruturado por seções:
  - **Teoria**: Texto explicativo
  - **Exemplos**: Lista de palavras/frases
  - **Áudio**: Player para pronúncia (futuro)
  - **Dicas do Professor**: Boxes destacados
  - **Exercícios**: Interativos inline

**Coluna Direita (Sidebar):**
- Progresso da lição (%)
- Tempo na lição
- Notas pessoais (textarea)
- Palavras-chave da lição
- Recursos relacionados
- Botão "Marcar como concluída"
- Botão "Fazer Quiz"

#### Componentes Interativos da Lição:

**a) Componente de Texto (`<LessonText>`)**
```vue
- Texto com formatação
- Destaque de palavras-chave
- Tooltips com traduções
- Modo claro/escuro
```

**b) Componente de Exemplo (`<LessonExample>`)**
```vue
- Lista de palavras/frases
- Áudio de pronúncia (ícone 🔊)
- Tradução ao passar o mouse
- Botão "Adicionar aos favoritos"
```

**c) Componente de Exercício Inline (`<LessonExercise>`)**
```vue
- Completar lacunas
- Múltipla escolha rápida
- Arrastar e soltar
- Feedback imediato
- Explicação do erro
```

**d) Componente de Dica (`<LessonTip>`)**
```vue
- Box destacado (💡)
- Dicas do professor
- Comparações PT-BR ↔️ IT
- Observações culturais
```

### 4. **Quiz Final da Lição** (`/lesson/:id/quiz`)
- Questões baseadas no conteúdo da lição
- Tipos variados (existentes no sistema)
- Feedback completo
- Resultados e explicações
- Botão "Refazer" ou "Próxima Lição"

---

## 🔄 Fluxo de Aprendizado

### Jornada do Usuário:

```
1. Usuário navega nos Cursos
   ↓
2. Seleciona "Italiano Básico"
   ↓
3. Vê estrutura do curso (módulos e lições)
   ↓
4. Clica em "Lição 1: Alfabeto"
   ↓
5. Lê conteúdo teórico
   ↓
6. Ouve pronúncias (áudio)
   ↓
7. Faz exercícios inline
   ↓
8. Faz quiz final (opcional)
   ↓
9. Marca lição como concluída
   ↓
10. Desbloqueia próxima lição
    ↓
11. Recebe pontos/badge
```

### Progressão:

- **Lições sequenciais**: Precisa completar Lição N para acessar Lição N+1
- **Módulos**: Completa todos módulos do curso
- **Certificado**: Ao finalizar curso (futuro)

---

## 🎮 Gamificação

### Sistema de Pontos:
- Completar lição: **50 pontos**
- Acertar exercício inline: **5 pontos**
- Acertar quiz final (100%): **100 pontos** (bônus)
- Sequência de dias consecutivos: **Multiplica pontos**

### Badges/Conquistas:
- 🎓 "Primeira Lição Completa"
- 🔥 "Sequência de 7 dias"
- 🏆 "Módulo Completo"
- ⭐ "Curso Completo"
- 🎯 "Quiz Perfeito (100%)"

### Níveis de Usuário:
- Iniciante (0-500 pts)
- Aprendiz (500-1500 pts)
- Estudante (1500-3000 pts)
- Avançado (3000-6000 pts)
- Fluente (6000+ pts)

---

## 📝 Transformação do Conteúdo

### Parser para ConteudoItaliano2025.txt

Criar comando Artisan para processar o arquivo:

```bash
php artisan lessons:parse ConteudoItaliano2025.txt
```

**Lógica do Parser:**

1. **Detectar estrutura**:
   - PARTE I/II/III → Módulos
   - Página N → Lições
   - Subseções → Seções da lição

2. **Extrair conteúdo**:
   - "Transcrição do Conteúdo" → content_italian
   - "Comentários do Professor" → content_portuguese
   - "Exercício" → Criar questions vinculadas

3. **Identificar tipo**:
   - Alfabeto, Pronúncia → lesson_type: 'pronunciation'
   - Verbos, Conjugação → lesson_type: 'grammar'
   - Vocabulário → lesson_type: 'vocabulary'

4. **Criar relacionamentos**:
   - Course → Modules → Lessons → Sections
   - Lessons → Questions (exercícios)

### Estrutura JSON Gerada:

```json
{
  "course": {
    "title": "Italiano Básico 2025",
    "level": "A1-B1",
    "description": "Curso completo de italiano do básico ao intermediário",
    "modules": [
      {
        "title": "PARTE I: FUNDAMENTOS E ESTRUTURAS BÁSICAS",
        "order": 1,
        "lessons": [
          {
            "title": "Página 1: O Alfabeto e a Pronúncia de C e G",
            "lesson_type": "pronunciation",
            "difficulty": 1,
            "estimated_time": 30,
            "sections": [
              {
                "title": "O Alfabeto Italiano",
                "section_type": "text",
                "content": "L'alfabeto italiano...",
                "order": 1
              },
              {
                "title": "Comentários do Professor",
                "section_type": "tip",
                "content": "Olá! Bem-vindo à nossa primeira aula...",
                "order": 2
              },
              {
                "title": "Exercício de Pronúncia",
                "section_type": "exercise",
                "content": "Leia em voz alta...",
                "order": 3
              }
            ]
          }
        ]
      }
    ]
  }
}
```

---

## 🚀 Roadmap de Implementação

### Fase 1: Backend (2-3 dias)
- ✅ Criar migrations para novas tabelas
- ✅ Criar Models com relacionamentos
- ✅ Criar Controllers API (CRUD)
- ✅ Criar comando de parser
- ✅ Processar ConteudoItaliano2025.txt
- ✅ Criar seeders com dados de exemplo

### Fase 2: API Endpoints (1 dia)
```
GET    /api/v1/courses
GET    /api/v1/courses/{id}
GET    /api/v1/courses/{id}/modules
POST   /api/v1/courses/{id}/enroll

GET    /api/v1/modules/{id}/lessons
GET    /api/v1/lessons/{id}
GET    /api/v1/lessons/{id}/sections
POST   /api/v1/lessons/{id}/complete

GET    /api/v1/user/courses
GET    /api/v1/user/progress
POST   /api/v1/user/lessons/{id}/start
PUT    /api/v1/user/lessons/{id}/progress
```

### Fase 3: Frontend (3-4 dias)
- ✅ Página de listagem de cursos
- ✅ Página de detalhes do curso
- ✅ Componente de lição interativa
- ✅ Componentes específicos (text, example, tip, exercise)
- ✅ Sistema de progresso visual
- ✅ Navegação entre lições
- ✅ Integração com sistema de questões existente

### Fase 4: Melhorias (contínuo)
- 🔊 Integração com TTS (Text-to-Speech) para pronúncia
- 🎤 Gravação de áudio do usuário
- 📝 Sistema de notas pessoais
- 🔖 Favoritos e marcadores
- 💬 Fórum de dúvidas por lição
- 📊 Analytics de tempo por seção
- 🎓 Certificados de conclusão

---

## 📚 Exemplo Prático

### Lição 1 Transformada:

**Título**: "O Alfabeto e a Pronúncia de C e G"

**Seção 1** (Texto teórico):
```markdown
# L'alfabeto italiano

[Tabela do alfabeto...]

Lettere Straniere: J, K, W, X, Y
```

**Seção 2** (Dica do Professor):
```markdown
💡 **Dica do Professor**

O Alfabeto: O alfabeto italiano tem 21 letras...
```

**Seção 3** (Exemplos com áudio):
```markdown
🔊 **Pratique a Pronúncia**

Som Doce [tʃ / dʒ]:
- Piacere [▶️ Ouvir]
- Ciao [▶️ Ouvir]
- Cinema [▶️ Ouvir]
```

**Seção 4** (Exercício Inline):
```markdown
✏️ **Complete as Regras**

La "CI" si pronuncia [___] davanti a e, i
e si pronuncia [___] davanti a a, o, u, h

[Verificar Resposta]
```

**Quiz Final** (3-5 questões):
1. Multiple choice: Qual pronúncia de "Cena"?
2. Fill in blank: Complete a regra de pronúncia
3. Audio: Selecione a palavra que você ouviu

---

## 🎯 Benefícios da Solução

✅ **Conteúdo rico e estruturado**
✅ **Aprendizado progressivo**
✅ **Interatividade constante**
✅ **Feedback imediato**
✅ **Gamificação motivadora**
✅ **Rastreamento de progresso**
✅ **Flexibilidade (teoria + prática)**
✅ **Escalável** (fácil adicionar novas lições)
✅ **Complementa exames CILS** (teoria antes da prática)

---

## 💡 Diferencial ImparaTeLingua

**Antes**: Apenas exames e questões (prática)
**Depois**: Curso completo + Exames (teoria + prática)

### Fluxo Completo:
```
Lição (Aprende) → Exercícios (Pratica) → Exame CILS (Testa)
```

O usuário agora pode:
1. **Aprender** gramática e vocabulário nas lições
2. **Praticar** com exercícios interativos
3. **Testar** conhecimento com exames CILS oficiais
4. **Revisar** lições quando errar questões

---

## 🔧 Próximos Passos Imediatos

Deseja que eu:

1. ✅ **Crie as migrations** para as novas tabelas?
2. ✅ **Crie os Models** com relacionamentos?
3. ✅ **Crie os Controllers** da API?
4. ✅ **Crie o comando parser** para processar o arquivo?
5. ✅ **Crie os componentes Vue** da interface?

Ou prefere começar por alguma parte específica?

---

**Vamos transformar o ImparaTeLingua em uma plataforma completa de aprendizado!** 🚀🇮🇹
