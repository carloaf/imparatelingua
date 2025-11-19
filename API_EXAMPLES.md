# Exemplos de Uso da API ImparaLingua

Base URL: `http://localhost:8080/api/v1`

## 🎓 Exames

### Listar todos os exames
```bash
curl http://localhost:8080/api/v1/exams
```

### Ver detalhes de um exame específico
```bash
curl http://localhost:8080/api/v1/exams/1
```

### Ver questões de um exame
```bash
curl http://localhost:8080/api/v1/exams/1/questions
```

### Criar novo exame
```bash
curl -X POST http://localhost:8080/api/v1/exams \
  -H "Content-Type: application/json" \
  -d '{
    "name": "CILS B1",
    "level": "B1",
    "year": 2024,
    "description": "Certificação de Italiano - Nível B1"
  }'
```

## 📚 Categorias

### Listar todas as categorias
```bash
curl http://localhost:8080/api/v1/categories
```

### Ver questões de uma categoria
```bash
curl http://localhost:8080/api/v1/categories/1/questions
```

### Criar nova categoria
```bash
curl -X POST http://localhost:8080/api/v1/categories \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pronúncia",
    "slug": "pronuncia",
    "description": "Exercícios de pronúncia e fonética"
  }'
```

## ❓ Questões

### Listar todas as questões
```bash
curl http://localhost:8080/api/v1/questions
```

### Filtrar questões por categoria
```bash
curl "http://localhost:8080/api/v1/questions?category_id=1"
```

### Filtrar questões por exame
```bash
curl "http://localhost:8080/api/v1/questions?exam_id=1"
```

### Filtrar questões por dificuldade
```bash
curl "http://localhost:8080/api/v1/questions?difficulty=1"
```

### Ver detalhes de uma questão específica
```bash
curl http://localhost:8080/api/v1/questions/1
```

### Criar nova questão com respostas
```bash
curl -X POST http://localhost:8080/api/v1/questions \
  -H "Content-Type: application/json" \
  -d '{
    "exam_id": 1,
    "category_id": 1,
    "question_text": "Come si dice \"casa\" in italiano?",
    "question_type": "multiple_choice",
    "difficulty": 1,
    "context": "Vocabulário básico",
    "order": 1,
    "answers": [
      {
        "answer_text": "casa",
        "is_correct": true,
        "order": 1
      },
      {
        "answer_text": "cassa",
        "is_correct": false,
        "order": 2
      },
      {
        "answer_text": "cosa",
        "is_correct": false,
        "order": 3
      },
      {
        "answer_text": "causa",
        "is_correct": false,
        "order": 4
      }
    ]
  }'
```

### Responder uma questão
```bash
curl -X POST http://localhost:8080/api/v1/questions/1/answer \
  -H "Content-Type: application/json" \
  -d '{
    "answer_id": 1,
    "user_id": 1
  }'
```

**Resposta de sucesso (resposta correta):**
```json
{
  "success": true,
  "message": "Resposta correta!",
  "data": {
    "is_correct": true,
    "selected_answer": {
      "id": 1,
      "question_id": 1,
      "answer_text": "sono",
      "is_correct": true,
      "order": 1
    },
    "correct_answer": {
      "id": 1,
      "question_id": 1,
      "answer_text": "sono",
      "is_correct": true,
      "order": 1
    }
  }
}
```

**Resposta de erro (resposta incorreta):**
```json
{
  "success": true,
  "message": "Resposta incorreta",
  "data": {
    "is_correct": false,
    "selected_answer": {
      "id": 2,
      "question_id": 1,
      "answer_text": "sei",
      "is_correct": false,
      "order": 2
    },
    "correct_answer": {
      "id": 1,
      "question_id": 1,
      "answer_text": "sono",
      "is_correct": true,
      "order": 1
    }
  }
}
```

## 📊 Progresso do Usuário

### Ver histórico de respostas
```bash
curl "http://localhost:8080/api/v1/user/progress?user_id=1"
```

### Ver estatísticas do usuário
```bash
curl "http://localhost:8080/api/v1/user/statistics?user_id=1"
```

**Resposta das estatísticas:**
```json
{
  "success": true,
  "data": {
    "overall": {
      "total_answered": 10,
      "correct_answers": 7,
      "incorrect_answers": 3,
      "accuracy": 70.00
    },
    "by_category": [
      {
        "name": "Gramática",
        "total": 5,
        "correct": 4,
        "accuracy": 80.00
      },
      {
        "name": "Vocabulário",
        "total": 3,
        "correct": 2,
        "accuracy": 66.67
      },
      {
        "name": "Interpretação de Texto",
        "total": 2,
        "correct": 1,
        "accuracy": 50.00
      }
    ],
    "by_level": [
      {
        "level": "A1",
        "total": 6,
        "correct": 5,
        "accuracy": 83.33
      },
      {
        "level": "A2",
        "total": 4,
        "correct": 2,
        "accuracy": 50.00
      }
    ]
  }
}
```

## 🔄 Atualizar e Deletar

### Atualizar um exame
```bash
curl -X PUT http://localhost:8080/api/v1/exams/1 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "CILS A1 - Edição 2025",
    "year": 2025
  }'
```

### Deletar um exame
```bash
curl -X DELETE http://localhost:8080/api/v1/exams/1
```

### Atualizar uma questão
```bash
curl -X PUT http://localhost:8080/api/v1/questions/1 \
  -H "Content-Type: application/json" \
  -d '{
    "difficulty": 2,
    "context": "Contexto atualizado"
  }'
```

### Deletar uma questão
```bash
curl -X DELETE http://localhost:8080/api/v1/questions/1
```

## 🧪 Testando com json_pp (pretty print)

Para visualizar as respostas de forma mais legível:

```bash
curl -s http://localhost:8080/api/v1/exams | json_pp
curl -s http://localhost:8080/api/v1/categories | json_pp
curl -s http://localhost:8080/api/v1/questions/1 | json_pp
curl -s "http://localhost:8080/api/v1/user/statistics?user_id=1" | json_pp
```

## 📝 Notas

- Todas as rotas retornam JSON
- O campo `success` indica se a operação foi bem-sucedida
- Os dados estão sempre no campo `data`
- Mensagens de erro ou sucesso estão no campo `message`
- Por enquanto, o `user_id` é passado manualmente. Futuramente será substituído por autenticação JWT
