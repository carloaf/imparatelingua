# ImparaLingua - Aplicação de Estudo de Idiomas

Aplicação web para estudar e aprender línguas estrangeiras (italiano) através de questionários sobre gramática, vocabulário e interpretação de texto, baseada nas provas CILS.

## 🚀 Status do Projeto

✅ **Aplicação Completa e Funcional!**

- Estrutura de diretórios criada
- Docker configurado com 4 containers:
  - **app**: PHP 8.1-FPM com Laravel 10
  - **nginx**: Servidor web (porta 8080)
  - **db**: MySQL 8.0 (porta 33061)
  - **frontend**: Node 20 com Vue 3 + Vite (porta 5173)
- Laravel instalado e funcionando
- Vue.js 3 configurado com TypeScript
- Banco de dados configurado e conectado
- Migrations executadas com sucesso
- API REST completa e testada
- Frontend com componentes interativos
- TailwindCSS configurado

## 📋 Pré-requisitos

- Docker
- Docker Compose

## 🛠️ Instalação e Uso

### 1. Clonar o repositório
```bash
cd imparalingua
```

### 2. Iniciar os containers
```bash
docker compose up -d
```

### 3. Verificar status dos containers
```bash
docker compose ps
```

### 4. Acessar a aplicação
- **Frontend (Vue.js)**: http://localhost:5173
- **API (Laravel)**: http://localhost:8080/api/v1
- **Banco de Dados**: localhost:33061

## 📁 Estrutura do Projeto

```
imparalingua/
├── backend/              # API Laravel
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── routes/
│   └── ...
├── frontend/             # Interface Vue.js (a ser implementado)
├── docker/
│   └── nginx/
│       └── default.conf  # Configuração Nginx
├── docker-compose.yml    # Orquestração dos containers
└── guia_dev.md          # Guia de desenvolvimento
```

## 🔧 Comandos Úteis

### Executar comandos no container Laravel
```bash
# Artisan
docker compose exec app php artisan [comando]

# Composer
docker compose exec app composer [comando]

# Migrations
docker compose exec app php artisan migrate

# Criar migration
docker compose exec app php artisan make:migration [nome]

# Criar controller
docker compose exec app php artisan make:controller [NomeController]
```

### Gerenciar containers
```bash
# Parar containers
docker compose stop

# Reiniciar containers
docker compose restart

# Ver logs
docker compose logs [nome-do-servico]

# Acessar bash do container
docker compose exec app bash
```

## 🗄️ Configuração do Banco de Dados

- **Host**: db (dentro dos containers) / localhost (máquina host)
- **Porta**: 3306 (interna) / 33061 (externa)
- **Database**: app_italiano_db
- **Usuário**: app_user
- **Senha**: user_password
- **Root Password**: root_password

## 🎯 Status de Desenvolvimento

### ✅ Concluído (15/11/2025)

**Backend:**
- ✅ Setup inicial do Docker (PHP, Nginx, MySQL, Node)
- ✅ Laravel 10 instalado e configurado
- ✅ Banco de dados completo:
  - Migrations: exams, categories, questions, answers, user_progress
  - Models com relacionamentos
  - Seeders com dados de exemplo
- ✅ API REST completa:
  - CRUD de exames e categorias
  - CRUD de questões com respostas
  - Sistema de resposta a questões
  - Estatísticas de progresso do usuário

**Frontend:**
- ✅ Vue 3 com TypeScript e Vite
- ✅ TailwindCSS v3 configurado
- ✅ Vue Router com rotas configuradas
- ✅ Axios para comunicação com API
- ✅ Componentes principais:
  - ExamList (listagem de exames)
  - QuestionCard (questões interativas)
  - ProgressStats (dashboard de estatísticas)
- ✅ Views completas:
  - Home (página inicial com hero e features)
  - ExamList (listagem de exames)
  - Quiz (interface completa de questionário)
- ✅ Integração Frontend + Backend funcionando
- ✅ Sistema de feedback visual (correto/incorrecto)
- ✅ Proxy configurado para API

### 📋 Próximos Passos

1. **Autenticação:**
   - Sistema de login/registro
   - JWT ou Laravel Sanctum
   - Proteção de rotas
   - Perfil de usuário

2. **Melhorias de UX:**
   - Loading states
   - Animações de transição
   - Toast notifications
   - Modal de confirmação

3. **Funcionalidades:**
   - Importação de provas CILS reais
   - Sistema de gamificação (pontos, badges)
   - Gráficos avançados de progresso
   - Modo de revisão (rever erros)
   - Timer para questões
   - Exportar relatórios PDF

4. **Performance:**
   - Cache de respostas
   - Paginação da API
   - Lazy loading de componentes
   - PWA (Progressive Web App)

## 🚀 API Endpoints

A API está disponível em `http://localhost:8080/api/v1/`

### Exames
- `GET /exams` - Listar todos os exames
- `GET /exams/{id}` - Detalhes de um exame
- `GET /exams/{id}/questions` - Questões de um exame
- `POST /exams` - Criar novo exame
- `PUT /exams/{id}` - Atualizar exame
- `DELETE /exams/{id}` - Deletar exame

### Categorias
- `GET /categories` - Listar categorias
- `GET /categories/{id}` - Detalhes de uma categoria
- `GET /categories/{id}/questions` - Questões de uma categoria

### Questões
- `GET /questions` - Listar questões (com filtros)
- `GET /questions/{id}` - Detalhes de uma questão
- `POST /questions` - Criar nova questão
- `POST /questions/{id}/answer` - Responder uma questão

### Progresso
- `GET /user/progress` - Histórico de respostas
- `GET /user/statistics` - Estatísticas do usuário

## 💾 Dados de Exemplo

O banco vem populado com:
- 3 categorias: Gramática, Vocabulário, Interpretação
- 2 exames: CILS A1 e CILS A2
- 8 questões variadas:
  - Múltipla escolha (4 alternativas)
  - Preencher lacuna (digite a resposta)
  - Verdadeiro/Falso
- 1 usuário de teste (test@example.com)

## 🤝 Contribuindo

Este é um projeto em desenvolvimento. Consulte o arquivo `guia_dev.md` para detalhes sobre o processo de desenvolvimento.

## 📄 Licença

Este projeto está sob a licença MIT.
