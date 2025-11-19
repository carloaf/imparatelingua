# Guia de Importação de Questões

## Introdução

Este guia explica como importar questões para o sistema de aprendizado de italiano usando arquivos JSON.

## Formato do Arquivo JSON

O arquivo JSON deve seguir a estrutura abaixo:

```json
{
  "exam": {
    "name": "Nome do Exame",
    "level": "A1|A2|B1|B2|C1|C2",
    "year": 2024,
    "description": "Descrição do exame",
    "is_official": true,
    "session": "Giugno|Dicembre",
    "exam_code": "CILS_B1_GIU_2024"
  },
  "questions": [
    {
      "category": "gramatica|vocabulario|interpretacao|...",
      "question_text": "Texto da questão",
      "question_type": "multiple_choice|fill_in_blank|true_false",
      "difficulty": 1-5,
      "context": "Contexto ou explicação gramatical",
      "order": 1,
      "explanation": "Explicação educacional sobre o conceito da questão",
      "answers": [
        {
          "answer_text": "Texto da resposta",
          "is_correct": true|false,
          "order": 1,
          "justification": "Explicação de por que esta resposta está correta ou incorreta"
        }
      ]
    }
  ]
}
```

## Campos Obrigatórios

### Exame
- `name`: Nome do exame (string)
- `level`: Nível CEFR (A1, A2, B1, B2, C1, C2)
- `year`: Ano do exame (inteiro)
- `description`: Descrição do exame (string, opcional)
- `is_official`: Indica se é um exame oficial CILS (boolean, opcional, padrão: false)
- `session`: Sessão do exame - "Giugno" ou "Dicembre" (string, opcional)
- `exam_code`: Código único do exame, ex: CILS_B1_DIC_2022 (string, opcional)
- `source_url`: URL de referência para o exame original (string, opcional)

### Questões
- `category`: Categoria da questão (deve existir no banco)
- `question_text`: Texto da pergunta
- `question_type`: Tipo da questão (multiple_choice, fill_in_blank, true_false)
- `difficulty`: Nível de dificuldade (1 a 5)
- `context`: Contexto gramatical ou explicação (opcional)
- `order`: Ordem da questão no exame
- `explanation`: Explicação educacional do conceito (opcional mas recomendado)
- `answers`: Array de respostas

### Respostas
- `answer_text`: Texto da resposta
- `is_correct`: Se a resposta está correta (boolean)
- `order`: Ordem da resposta
- `justification`: Explicação de por que a resposta está correta/incorreta (opcional mas recomendado)

## Tipos de Questões

### 1. Multiple Choice (Múltipla Escolha)
Permite várias alternativas com uma ou mais corretas.

```json
{
  "question_type": "multiple_choice",
  "question_text": "Completa la frase: Se io _____ ricco, comprerei una casa al mare.",
  "answers": [
    {
      "answer_text": "fossi",
      "is_correct": true,
      "order": 1,
      "justification": "Correto! 'Fossi' é o congiuntivo imperfetto..."
    },
    {
      "answer_text": "sia",
      "is_correct": false,
      "order": 2,
      "justification": "Incorreto. 'Sia' é congiuntivo presente..."
    }
  ]
}
```

### 2. Fill in the Blank (Preencher Lacuna)
Questão onde o aluno deve digitar a resposta.

```json
{
  "question_type": "fill_in_blank",
  "question_text": "Quale preposizione va usata? Vado _____ scuola.",
  "answers": [
    {
      "answer_text": "a",
      "is_correct": true,
      "order": 1,
      "justification": "Correto! 'Andare a' é usado para indicar movimento..."
    }
  ]
}
```

### 3. True/False (Verdadeiro/Falso)
Questão com apenas duas opções.

```json
{
  "question_type": "true_false",
  "question_text": "Il participio passato di 'aprire' è 'aperto'",
  "answers": [
    {
      "answer_text": "true",
      "is_correct": true,
      "order": 1,
      "justification": "Correto! 'Aprire' tem particípio passado irregular 'aperto'..."
    },
    {
      "answer_text": "false",
      "is_correct": false,
      "order": 2,
      "justification": "Incorreto. Se fosse regular, seria 'aprito'..."
    }
  ]
}
```

## Como Importar

### 1. Preparar o arquivo JSON

Crie seu arquivo JSON seguindo o formato acima. Veja o exemplo completo em `storage/app/imports/exemplo_questoes.json`.

### 2. Colocar o arquivo no local correto

O arquivo deve estar em: `storage/app/imports/seu_arquivo.json`

### 3. Executar o comando de importação

```bash
# Importação básica
php artisan questions:import seu_arquivo.json

# Substituir exame existente (caso já exista um exame com o mesmo nome e ano)
php artisan questions:import seu_arquivo.json --replace
```

### 4. Verificar a importação

O comando mostrará:
- ✅ Confirmação de sucesso
- 📊 Estatísticas da importação (questões e respostas criadas)
- ❌ Erros, se houver

## Validações

O sistema valida automaticamente:
- ✅ Estrutura do JSON
- ✅ Existência das categorias referenciadas
- ✅ Tipos de questão válidos
- ✅ Níveis CEFR válidos
- ✅ Pelo menos uma resposta correta por questão
- ✅ Campos obrigatórios

## Dicas de Boas Práticas

1. **Sempre adicione justificativas**: As justificativas ajudam os alunos a entenderem seus erros.

2. **Use explicações educacionais**: O campo `explanation` deve explicar o conceito por trás da questão.

3. **Contextualize as questões**: O campo `context` ajuda a fornecer informações gramaticais importantes.

4. **Organize por dificuldade**: Use o campo `difficulty` para graduar o aprendizado.

5. **Mantenha a ordem**: Use o campo `order` para controlar a sequência das questões.

6. **Teste antes de importar**: Valide seu JSON em um validador online antes de importar.

7. **Backup**: Sempre faça backup antes de usar a flag `--replace`.

## Categorias Disponíveis

As seguintes categorias devem existir no banco de dados:
- `gramatica` - Gramática
- `vocabulario` - Vocabulário
- `interpretacao` - Interpretação de texto
- `conversacao` - Conversação
- `escrita` - Escrita
- `audicao` - Audição

Para adicionar novas categorias, use os seeders ou crie-as manualmente no banco.

## Resolução de Problemas

### Erro: "Categoria não encontrada"
Certifique-se de que a categoria existe no banco de dados. Execute:
```bash
php artisan db:seed --class=CategorySeeder
```

### Erro: "Arquivo não encontrado"
Verifique se o arquivo está em `storage/app/imports/` e se o nome está correto.

### Erro: "JSON inválido"
Valide seu JSON em um validador online e corrija os erros de sintaxe.

### Erro: "Exame já existe"
Use a flag `--replace` para substituir o exame existente:
```bash
php artisan questions:import seu_arquivo.json --replace
```

## Exemplo Completo

Veja o arquivo `storage/app/imports/exemplo_questoes.json` para um exemplo completo com:
- 5 questões de diferentes tipos
- Todos os campos preenchidos
- Justificativas detalhadas
- Explicações educacionais

## Suporte

Para mais informações ou problemas, consulte:
- Documentação do Laravel
- Arquivo `guia_dev.md` no diretório raiz do projeto
