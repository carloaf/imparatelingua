# Tipos de Questões - ImparaLingua

A aplicação suporta 3 tipos diferentes de questões para tornar o aprendizado mais dinâmico e completo.

## 📝 Tipos de Questões

### 1. Múltipla Escolha (`multiple_choice`)

Questões com 4 alternativas (A, B, C, D) onde o usuário escolhe a opção correta.

**Exemplo:**
```
Pergunta: Complete a frase: Io ____ italiano.
Contexto: Verbo essere (ser/estar) no presente

A. sono
B. sei
C. è
D. siamo
```

**Como responder:**
- Clique na alternativa que você considera correta
- Receba feedback imediato (verde para certo, vermelho para errado)

---

### 2. Preencher Lacuna (`fill_in_blank`)

Questões onde o usuário deve digitar a resposta correta em um campo de texto.

**Exemplo:**
```
Pergunta: Complete com o artigo correto: ___ libro è molto interessante.
Contexto: Artigos definidos em italiano

[Digite sua resposta aqui...]
```

**Como responder:**
- Digite a resposta no campo de texto
- Pressione Enter ou clique em "Confirmar"
- A resposta não diferencia maiúsculas/minúsculas
- Receba feedback mostrando se acertou ou qual era a resposta correta

**Dicas:**
- Digite exatamente como a palavra apareceria na frase
- Não precisa de pontuação adicional
- Acentos são importantes!

---

### 3. Verdadeiro ou Falso (`true_false`)

Questões onde o usuário decide se a afirmação é verdadeira ou falsa.

**Exemplo:**
```
Pergunta: O plural de "ragazzo" é "ragazzi"
Contexto: Formação de plural em italiano

✓ Verdadeiro
✗ Falso
```

**Como responder:**
- Clique em "Verdadeiro" ou "Falso"
- Receba feedback imediato

---

## 🎯 Características Comuns

Todos os tipos de questões compartilham:

### 📊 Informações Exibidas
- **Contexto**: Informação adicional ou gramática relacionada
- **Dificuldade**: Indicada por estrelas (⭐ a ⭐⭐⭐⭐⭐)
- **Tipo**: Badge colorido indicando o tipo da questão
- **Feedback**: Mensagem clara sobre acerto ou erro

### ✅ Feedback Visual
- **Verde**: Resposta correta
- **Vermelho**: Resposta incorreta
- **Resposta correta**: Sempre mostrada após responder

### 🔒 Validação
- Não é possível mudar a resposta após confirmar
- Botão "Próxima" só fica ativo após responder
- Progresso salvo automaticamente na API

---

## 💡 Exemplos de Uso por Categoria

### Gramática
```javascript
// Múltipla Escolha
"Complete: Ieri io ____ al cinema."
Opções: sono andato, ho andato, andavo, vado

// Preencher Lacuna
"Complete com o verbo 'essere': Io ___ brasiliano."
Resposta: sono

// Verdadeiro/Falso
"O verbo 'avere' usa 'avere' como auxiliar no passato prossimo"
Resposta: Verdadeiro
```

### Vocabulário
```javascript
// Múltipla Escolha
"Como se diz 'bom dia' em italiano?"
Opções: Buongiorno, Buonasera, Ciao, Arrivederci

// Preencher Lacuna
"Complete a palavra: Uma cor do céu é ___ (azul em italiano)"
Resposta: azzurro

// Verdadeiro/Falso
"'Casa' significa 'casa' em italiano"
Resposta: Verdadeiro
```

### Interpretação de Texto
```javascript
// Múltipla Escolha
Texto: "Maria è una ragazza italiana..."
"De acordo com o texto, Maria gosta de:"
Opções: Ler livros e ouvir música, ...

// Preencher Lacuna
Texto: "Il gatto è sul tavolo"
"Onde está o gato? O gato está no ___"
Resposta: tavolo

// Verdadeiro/Falso
Texto: "Luca ha venti anni"
"Luca tem 20 anos"
Resposta: Verdadeiro
```

---

## 🎓 Criando Novas Questões

### Via Seeder (Desenvolvimento)

```php
// Múltipla Escolha
$question = Question::create([
    'exam_id' => $exam->id,
    'category_id' => $category->id,
    'question_text' => 'Sua pergunta aqui',
    'question_type' => 'multiple_choice',
    'difficulty' => 2,
    'context' => 'Contexto opcional',
    'order' => 1
]);

// Criar 4 alternativas
Answer::create([
    'question_id' => $question->id,
    'answer_text' => 'Opção A',
    'is_correct' => true,
    'order' => 1
]);
// ... mais 3 alternativas

// Preencher Lacuna
$question = Question::create([
    'question_text' => 'Complete: ___ palavra',
    'question_type' => 'fill_in_blank',
    // ...
]);

Answer::create([
    'question_id' => $question->id,
    'answer_text' => 'resposta_correta',
    'is_correct' => true,
    'order' => 1
]);

// Verdadeiro/Falso
$question = Question::create([
    'question_text' => 'Afirmação verdadeira ou falsa',
    'question_type' => 'true_false',
    // ...
]);

Answer::create([
    'question_id' => $question->id,
    'answer_text' => 'true', // ou 'false'
    'is_correct' => true,
    'order' => 1
]);

Answer::create([
    'question_id' => $question->id,
    'answer_text' => 'false', // opção contrária
    'is_correct' => false,
    'order' => 2
]);
```

### Via API (Futuro - Admin Panel)

```bash
POST /api/v1/questions
{
  "exam_id": 2,
  "category_id": 1,
  "question_text": "Sua pergunta",
  "question_type": "fill_in_blank",
  "difficulty": 3,
  "context": "Contexto",
  "answers": [
    {
      "answer_text": "resposta",
      "is_correct": true
    }
  ]
}
```

---

## 📱 Interface do Usuário

### Múltipla Escolha
```
┌─────────────────────────────────────────┐
│ Contexto opcional em azul               │
├─────────────────────────────────────────┤
│ Pergunta                  ⭐⭐ [Múltipla] │
├─────────────────────────────────────────┤
│ ○ A. Opção 1                            │
│ ○ B. Opção 2                            │
│ ○ C. Opção 3                            │
│ ○ D. Opção 4                            │
└─────────────────────────────────────────┘
```

### Preencher Lacuna
```
┌─────────────────────────────────────────┐
│ Contexto opcional em azul               │
├─────────────────────────────────────────┤
│ Pergunta                  ⭐ [Preencher]  │
├─────────────────────────────────────────┤
│ [Digite sua resposta...] [Confirmar]    │
│ 💡 Pressione Enter ou clique confirmar   │
└─────────────────────────────────────────┘
```

### Verdadeiro/Falso
```
┌─────────────────────────────────────────┐
│ Contexto opcional em azul               │
├─────────────────────────────────────────┤
│ Pergunta              ⭐⭐⭐ [V/F]         │
├─────────────────────────────────────────┤
│ ○ ✓ Verdadeiro                          │
│ ○ ✗ Falso                               │
└─────────────────────────────────────────┘
```

---

## 🔍 Boas Práticas

### Para Múltipla Escolha
- ✅ Crie 4 alternativas plausíveis
- ✅ Apenas 1 alternativa correta
- ✅ Evite alternativas muito óbvias
- ✅ Use distratores comuns (erros frequentes)

### Para Preencher Lacuna
- ✅ Resposta deve ser uma palavra ou expressão curta
- ✅ Evite respostas ambíguas
- ✅ Considere variações (maiúsculas/minúsculas)
- ✅ Forneça contexto suficiente

### Para Verdadeiro/Falso
- ✅ Afirmações claras e objetivas
- ✅ Evite pegadinhas muito complexas
- ✅ Use para testar conceitos específicos
- ✅ Forneça contexto quando necessário

---

## 📊 Estatísticas

O sistema rastreia:
- Tipo de questão respondida
- Taxa de acerto por tipo
- Tempo médio por tipo (futuro)
- Questões mais difíceis por tipo

---

**Última atualização:** 16/11/2025
