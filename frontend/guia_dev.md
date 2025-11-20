
## 19. Correção da Exibição dos Exercícios no Frontend (20/11/2025)

### Problema Identificado
Os exercícios estavam sendo exibidos com objetos JSON completos ao invés do texto:
```
Exercício 1
A) { "id": 3786, "text": "al", "is_correct": true}
```

### Causa Raiz
O componente Vue `LessonView.vue` estava renderizando o objeto `option` diretamente, ao invés de acessar a propriedade `option.text`.

A API retorna a estrutura:
```json
{
  "options": [
    {"id": 3786, "text": "al", "is_correct": true},
    {"id": 3787, "text": "nel", "is_correct": false}
  ]
}
```

### Correções Aplicadas

#### 1. Renderização das Opções
**Arquivo**: `frontend/src/views/LessonView.vue`

Alterado de:
```vue
<span class="flex-1">{{ option }}</span>
```

Para:
```vue
<span class="flex-1">{{ option.text || option }}</span>
```

#### 2. Função de Verificação de Resposta Correta
Alterado de:
```javascript
const getCorrectAnswerIndex = (exerciseIndex) => {
  const exercise = lesson.value.exercises[exerciseIndex]
  if (!exercise?.options || !exercise?.correct_answer) return -1
  
  return exercise.options.findIndex(option => 
    option.trim().toLowerCase() === exercise.correct_answer.trim().toLowerCase()
  )
}
```

Para:
```javascript
const getCorrectAnswerIndex = (exerciseIndex) => {
  const exercise = lesson.value.exercises[exerciseIndex]
  if (!exercise?.options) return -1
  
  return exercise.options.findIndex(option => option.is_correct === true)
}
```

#### 3. Exibição da Resposta Correta no Feedback
Alterado de:
```vue
<strong>Resposta correta:</strong> {{ exercise.correct_answer }}
```

Para:
```vue
<strong>Resposta correta:</strong> {{ exercise.options[getCorrectAnswerIndex(index)]?.text || exercise.options[getCorrectAnswerIndex(index)] }}
```

#### 4. Compatibilidade com Nomes de Propriedades
Adicionado suporte para nomes de propriedades da API:
```vue
{{ exercise.question_text || exercise.question }}
{{ getExerciseIcon(exercise.question_type || exercise.type) }}
```

### Estrutura de Dados da API

**Endpoint**: `GET /api/v1/lessons/{id}`

**Estrutura de Exercise**:
```json
{
  "id": 1209,
  "question_text": "Completa: 'Vado ___ cinema stasera con gli amici.'",
  "question_type": "multiple_choice",
  "difficulty": 2,
  "order": 1,
  "explanation": "A + IL = AL. Vado AL cinema. Usa-se 'a' para indicar direção/destino com lugares públicos.",
  "options": [
    {
      "id": 3786,
      "text": "al",
      "is_correct": true
    },
    {
      "id": 3787,
      "text": "nel",
      "is_correct": false
    }
  ]
}
```

### Resultado
Agora os exercícios são exibidos corretamente:
```
Exercício 1
A) al
B) nel
C) del
D) sul
```

Com feedback adequado ao responder:
- ✓ Opção correta destacada em verde
- ✗ Opção incorreta destacada em vermelho
- Explicação exibida após resposta
- Resposta correta mostrada claramente

### Arquivos Modificados
- `frontend/src/views/LessonView.vue` (3 correções aplicadas)

### Testes Realizados
```bash
# Verificar estrutura da API
curl http://localhost:8080/api/v1/lessons/36 | jq '.exercises[0]'

# Verificar renderização no frontend
# Acessar: http://localhost:5174/lesson/36
# Confirmar que as opções são exibidas como texto simples
```

