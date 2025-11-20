
Aqui está o guia completo, passo a passo. Copie e cole o conteúdo abaixo em um novo arquivo chamado `SETUP_GUIDE.md` na raiz do seu projeto.

---

# Guia de Setup: ImparaTeLingua - Aplicação de Estudo de Idiomas com Docker, Laravel e Vue.js

Este documento descreve o processo de configuração inicial para um ambiente de desenvolvimento local completo, utilizando Docker. A arquitetura proposta separa o **Backend** (API) do **Frontend** (Interface do Usuário), seguindo as melhores práticas de desenvolvimento de software.

**ImparaTeLingua** é uma aplicação dedicada ao estudo e aprendizado de línguas estrangeiras, com foco especial no idioma italiano.

## 1. Arquitetura e Tecnologias

*   **Orquestração:** Docker e Docker Compose. Possuo docker compose instalado na minha máquina na versão v2 que usa sintaxe de comando `docker compose` (sem hífen).
*   **Backend (API):** PHP 8+ com Laravel, servido por PHP-FPM.
*   **Frontend (SPA):** Vue.js 3 (gerenciado com Node.js).
*   **Banco de Dados:** MySQL 8.
*   **Servidor Web:** Nginx (atuando como proxy reverso).

## 2. Pré-requisitos

Antes de começar, certifique-se de ter os seguintes softwares instalados em sua máquina:
*   [Docker](https://www.docker.com/get-started)
*   [Docker Compose](https://docs.docker.com/compose/install/) (geralmente já vem com o Docker Desktop)

## 3. Passo a Passo da Configuração

### Passo 3.1: Estrutura de Diretórios

Primeiro, crie a seguinte estrutura de pastas e arquivos para o seu projeto. Você pode nomear a pasta raiz como `imparatelingua` ou o nome que preferir.

```
/imparatelingua/
|
├── backend/                # Código da API Laravel ficará aqui
|
├── frontend/               # Código da interface Vue.js ficará aqui
|
├── docker/                 # Arquivos de configuração específicos do Docker
|   └── nginx/
|       └── default.conf    # Configuração do Nginx
|
└── docker-compose.yml      # Arquivo principal do Docker Compose
```

### Passo 3.2: Arquivo `docker-compose.yml`

Este é o arquivo orquestrador. Ele define todos os serviços (containers) que nossa aplicação precisa para funcionar.

**Crie o arquivo `/imparatelingua/docker-compose.yml` com o seguinte conteúdo:**

```yaml
version: '3.8'

services:
  # Serviço do Backend (API com Laravel/PHP)
  app:
    build:
      context: ./backend
      dockerfile: Dockerfile
    container_name: imparatelingua-app
    restart: unless-stopped
    working_dir: /var/www/
    volumes:
      - ./backend:/var/www
    networks:
      - imparatelingua-network

  # Serviço do Servidor Web (Nginx)
  nginx:
    image: nginx:1.19-alpine
    container_name: imparatelingua-nginx
    restart: unless-stopped
    ports:
      - "8080:80" # Acessaremos a aplicação em http://localhost:8080
    volumes:
      - ./backend:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
      - db
    networks:
      - imparatelingua-network

  # Serviço do Banco de Dados (MySQL)
  db:
    image: mysql:8.0
    container_name: imparatelingua-db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: imparatelingua_db
      MYSQL_ROOT_PASSWORD: root_password # Use uma senha segura em produção
      MYSQL_PASSWORD: user_password    # Use uma senha segura em produção
      MYSQL_USER: app_user
    volumes:
      - imparatelingua-db-data:/var/lib/mysql
    ports:
      - "33061:3306" # Porta externa para conectar com um client de DB
    networks:
      - imparatelingua-network

# Define a rede que permitirá a comunicação entre os containers
networks:
  imparatelingua-network:
    driver: bridge

# Define o volume para persistir os dados do banco de dados
volumes:
  imparatelingua-db-data:
```

### Passo 3.3: Dockerfile do Backend (PHP)

Este arquivo contém as instruções para construir a imagem Docker do nosso ambiente PHP, instalando todas as dependências necessárias para o Laravel.

**Crie o arquivo `/imparatelingua/backend/Dockerfile` com o seguinte conteúdo:**

```dockerfile
# Use uma imagem oficial do PHP 8.1 com FPM (FastCGI Process Manager)
FROM php:8.1-fpm

# Instala dependências do sistema e extensões PHP comuns para o Laravel
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Limpa o cache para manter a imagem leve
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala o Composer (gerenciador de pacotes do PHP) globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho padrão dentro do container
WORKDIR /var/www

# Expõe a porta 9000, que é a porta padrão do PHP-FPM
EXPOSE 9000
```

### Passo 3.4: Configuração do Nginx

Este arquivo de configuração instrui o Nginx sobre como lidar com as requisições. Ele servirá os arquivos estáticos e encaminhará as requisições dinâmicas (`.php`) para o container do PHP-FPM.

**Crie o arquivo `/imparatelingua/docker/nginx/default.conf` com o seguinte conteúdo:**

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/public; # O Laravel serve a aplicação a partir da pasta 'public'
    index index.php index.html;

    # Logs de acesso e erro
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;

    # Regra principal para tratar as requisições
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Regra para passar scripts PHP para o container do PHP-FPM
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        # O nome 'app' corresponde ao nome do serviço PHP no docker-compose.yml
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
```

## 4. Iniciando o Ambiente

Com todos os arquivos de configuração no lugar, você está pronto para iniciar o ambiente.

1.  Abra seu terminal.
2.  Navegue até a pasta raiz do projeto (`/imparatelingua/`).
3.  Execute o seguinte comando:

    ```bash
    docker-compose up -d --build
    ```

    *   `up`: Inicia os containers.
    *   `-d`: Modo "detached" (roda em segundo plano).
    *   `--build`: Força a reconstrução das imagens (necessário na primeira vez).

4.  Para verificar se os containers estão rodando corretamente, execute:

    ```bash
    docker-compose ps
    ```

    Você deverá ver uma saída listando os três containers (`imparatelingua-app`, `imparatelingua-nginx`, `imparatelingua-db`) com o estado "Up" ou "running".

## 5. Resumo e Próximos Passos

Parabéns! Você acaba de criar um ambiente de desenvolvimento profissional, isolado e replicável.

**O que foi feito:**
*   Definimos três serviços (PHP, Nginx, MySQL) que se comunicam em uma rede privada.
*   Configuramos o Nginx para servir a aplicação e passar o processamento PHP para o container correto.
*   Mapeamos as pastas locais para dentro dos containers, permitindo que você edite o código em sua IDE e veja as mudanças refletidas instantaneamente.
*   Configuramos um volume para o banco de dados, garantindo que seus dados não sejam perdidos ao reiniciar os containers.

**Seu ambiente está no ar, mas ainda vazio. Os próximos passos lógicos são:**
1.  **Instalar o Laravel** dentro do container `app`.
2.  **Configurar a conexão** do Laravel com o banco de dados no container `db`.
3.  **Criar as `migrations`** para definir a estrutura das tabelas (perguntas, respostas, etc.).
4.  **Desenvolver os primeiros endpoints** da API.
5.  **Iniciar o projeto Vue.js** na pasta `frontend` e conectá-lo à API.

Estamos prontos para prosseguir para a instalação do Laravel.

---

## 6. ✅ Setup Inicial Concluído

**Status: IMPLEMENTADO EM 15/11/2025**

O ambiente foi configurado com sucesso! Todos os passos acima foram executados e testados.

### O que está funcionando:

✅ Estrutura de diretórios criada
✅ Docker Compose configurado com 3 serviços
✅ Container PHP-FPM rodando
✅ Container Nginx configurado e respondendo na porta 8080
✅ Container MySQL rodando com banco `imparatelingua_db`
✅ Laravel 10 instalado (compatível com PHP 8.1)
✅ Conexão com banco de dados estabelecida
✅ Migrations padrão do Laravel executadas
✅ Aplicação acessível em http://localhost:8080

### Comandos úteis para desenvolvimento:

```bash
# Verificar status dos containers
docker compose ps

# Ver logs de um container específico
docker compose logs app
docker compose logs nginx
docker compose logs db

# Executar comandos Artisan
docker compose exec app php artisan [comando]

# Executar migrations
docker compose exec app php artisan migrate

# Criar nova migration
docker compose exec app php artisan make:migration [nome]

# Criar controller
docker compose exec app php artisan make:controller [NomeController]

# Acessar bash do container
docker compose exec app bash

# Reinstalar dependências
docker compose exec app composer install
```

### Observações importantes:

1. **Comando Docker Compose**: Use `docker compose` (sem hífen) em vez de `docker-compose`
2. **Permissões**: As pastas `storage/` e `bootstrap/cache/` precisam ter permissões corretas (775) e pertencer ao usuário `www-data`
3. **Arquivo .env**: Já está configurado com as credenciais corretas do banco de dados

---

## 7. Próximos Passos: Desenvolvimento da Aplicação

Agora que o ambiente está funcionando, vamos desenvolver a aplicação de estudo de italiano.

### 7.1. Planejamento do Banco de Dados

Precisamos criar as seguintes tabelas:

#### Tabela: `exams` (Provas CILS)
- id
- name (ex: "CILS A1", "CILS B2")
- level (A1, A2, B1, B2, C1, C2)
- year
- created_at, updated_at

#### Tabela: `categories` (Categorias de questões)
- id
- name (grammar, vocabulary, reading)
- description
- created_at, updated_at

#### Tabela: `questions` (Questões)
- id
- exam_id (FK para exams)
- category_id (FK para categories)
- question_text (texto da pergunta)
- question_type (multiple_choice, fill_in_blank, etc.)
- difficulty (1-5)
- created_at, updated_at

#### Tabela: `answers` (Opções de resposta)
- id
- question_id (FK para questions)
- answer_text (texto da resposta)
- is_correct (boolean)
- created_at, updated_at

#### Tabela: `user_progress` (Progresso do usuário)
- id
- user_id (FK para users)
- question_id (FK para questions)
- selected_answer_id (FK para answers)
- is_correct (boolean)
- answered_at (timestamp)
- created_at, updated_at

### 7.2. Criando as Migrations

Para criar estas migrations, execute:

```bash
# Exames
docker compose exec app php artisan make:migration create_exams_table

# Categorias
docker compose exec app php artisan make:migration create_categories_table

# Questões
docker compose exec app php artisan make:migration create_questions_table

# Respostas
docker compose exec app php artisan make:migration create_answers_table

# Progresso do usuário
docker compose exec app php artisan make:migration create_user_progress_table
```

### 7.3. Criando os Models

```bash
# Models
docker compose exec app php artisan make:model Exam
docker compose exec app php artisan make:model Category
docker compose exec app php artisan make:model Question
docker compose exec app php artisan make:model Answer
docker compose exec app php artisan make:model UserProgress
```

### 7.4. Criando os Controllers para API

```bash
# Controllers
docker compose exec app php artisan make:controller Api/ExamController --api
docker compose exec app php artisan make:controller Api/QuestionController --api
docker compose exec app php artisan make:controller Api/AnswerController --api
docker compose exec app php artisan make:controller Api/UserProgressController --api
```

### 7.5. Setup do Frontend Vue.js

O próximo grande passo será configurar o frontend Vue.js na pasta `frontend/` com:

- Vue 3
- Vue Router
- Axios para comunicação com API
- TailwindCSS ou Bootstrap para estilização
- Componentes para exibir questões
- Sistema de navegação entre questões
- Feedback visual de respostas corretas/incorretas

---

## 8. Estrutura da API REST

A API seguirá o padrão RESTful:

### Endpoints planejados:

```
GET    /api/exams              - Listar todas as provas
GET    /api/exams/{id}         - Detalhes de uma prova
GET    /api/exams/{id}/questions - Questões de uma prova

GET    /api/categories         - Listar categorias
GET    /api/categories/{id}/questions - Questões de uma categoria

GET    /api/questions          - Listar questões
GET    /api/questions/{id}     - Detalhes de uma questão
POST   /api/questions/{id}/answer - Responder uma questão

GET    /api/user/progress      - Progresso do usuário
GET    /api/user/statistics    - Estatísticas do usuário
```

---

## 9. ✅ API REST Implementada

**Status: IMPLEMENTADO EM 15/11/2025**

A API REST foi desenvolvida completamente e está funcionando!

### O que foi implementado:

✅ **Migrations criadas e executadas:**
- `exams` - Tabela de exames CILS
- `categories` - Categorias de questões (gramática, vocabulário, leitura)
- `questions` - Questões com contexto e dificuldade
- `answers` - Opções de resposta com flag de correta
- `user_progress` - Histórico de respostas dos usuários

✅ **Models com relacionamentos:**
- `Exam`, `Category`, `Question`, `Answer`, `UserProgress`
- Todos os relacionamentos configurados (hasMany, belongsTo)
- Fillable e casts definidos

✅ **Controllers API completos:**
- `ExamController` - CRUD + endpoint de questões
- `CategoryController` - CRUD + endpoint de questões
- `QuestionController` - CRUD + endpoint para responder questões
- `UserProgressController` - Progresso e estatísticas do usuário

✅ **Rotas API configuradas** (`/api/v1/...`):
```
GET    /api/v1/exams
GET    /api/v1/exams/{id}
GET    /api/v1/exams/{id}/questions
POST   /api/v1/exams
PUT    /api/v1/exams/{id}
DELETE /api/v1/exams/{id}

GET    /api/v1/categories
GET    /api/v1/categories/{id}
GET    /api/v1/categories/{id}/questions

GET    /api/v1/questions
GET    /api/v1/questions/{id}
POST   /api/v1/questions
POST   /api/v1/questions/{id}/answer

GET    /api/v1/user/progress
GET    /api/v1/user/statistics
```

✅ **Seeder com dados de exemplo:**
- 3 categorias (Gramática, Vocabulário, Interpretação)
- 2 exames (CILS A1 e A2)
- 8 questões variadas:
  - 4 de múltipla escolha
  - 3 de preencher lacuna
  - 1 de verdadeiro/falso
- 1 usuário de teste

### Testando a API:

```bash
# Listar todos os exames
curl http://localhost:8080/api/v1/exams

# Listar categorias
curl http://localhost:8080/api/v1/categories

# Ver uma questão específica com respostas
curl http://localhost:8080/api/v1/questions/1

# Responder uma questão
curl -X POST http://localhost:8080/api/v1/questions/1/answer \
  -H "Content-Type: application/json" \
  -d '{"answer_id": 1, "user_id": 1}'

# Ver estatísticas do usuário
curl http://localhost:8080/api/v1/user/statistics?user_id=1
```

### Estrutura do banco de dados:

```
exams
├── id
├── name (CILS A1, CILS B2, etc.)
├── level (A1-C2)
├── year
└── description

categories
├── id
├── name (Gramática, Vocabulário, Leitura)
├── slug
└── description

questions
├── id
├── exam_id (FK)
├── category_id (FK)
├── question_text
├── question_type (multiple_choice, fill_in_blank, true_false)
├── difficulty (1-5)
├── context (texto adicional)
└── order

answers
├── id
├── question_id (FK)
├── answer_text
├── is_correct (boolean)
└── order

user_progress
├── id
├── user_id (FK)
├── question_id (FK)
├── selected_answer_id (FK)
├── is_correct (boolean)
└── answered_at
```

---

## 10. Próximos Passos: Frontend Vue.js

Agora que a API está completa, o próximo passo é desenvolver o frontend.

### 10.1. Setup do Frontend Vue.js

O frontend será desenvolvido na pasta `/frontend` com:

1. **Instalação do Vue 3:**
```bash
cd frontend
npm create vue@latest .
```

2. **Configurações necessárias:**
- Vue Router para navegação
- Axios para comunicação com API
- Tailwind CSS para estilização
- Pinia para gerenciamento de estado (opcional)

3. **Estrutura de componentes:**
```
frontend/
├── src/
│   ├── components/
│   │   ├── ExamList.vue       # Lista de exames disponíveis
│   │   ├── QuestionCard.vue   # Card de questão com alternativas
│   │   ├── AnswerFeedback.vue # Feedback de resposta correta/incorreta
│   │   └── ProgressStats.vue  # Estatísticas do usuário
│   ├── views/
│   │   ├── Home.vue           # Página inicial
│   │   ├── ExamView.vue       # Visualização de exame
│   │   ├── QuizView.vue       # Interface de quiz
│   │   └── ProfileView.vue    # Perfil e estatísticas
│   ├── services/
│   │   └── api.js             # Configuração do Axios
│   └── router/
│       └── index.js           # Rotas da aplicação
```

4. **Integração com Docker:**
- Adicionar serviço `frontend` no `docker-compose.yml`
- Configurar Vite para desenvolvimento
- Proxy para API do Laravel

### 10.2. Funcionalidades do Frontend

**Tela Inicial:**
- Lista de exames disponíveis (A1, A2, B1, etc.)
- Filtro por categoria
- Estatísticas gerais do usuário

**Tela de Quiz:**
- Exibição da questão com contexto
- 4 alternativas (A, B, C, D)
- Feedback imediato (certo/errado)
- Botão "Próxima questão"
- Barra de progresso

**Tela de Estatísticas:**
- Total de questões respondidas
- Taxa de acerto geral
- Performance por categoria
- Performance por nível
- Gráficos visuais

### 10.3. Comandos para criar o Frontend

```bash
# 1. Criar projeto Vue na pasta frontend
docker run --rm -v $(pwd)/frontend:/app -w /app node:18 npm create vue@latest .

# 2. Instalar dependências
docker run --rm -v $(pwd)/frontend:/app -w /app node:18 npm install

# 3. Adicionar Axios e TailwindCSS
docker run --rm -v $(pwd)/frontend:/app -w /app node:18 npm install axios
docker run --rm -v $(pwd)/frontend:/app -w /app node:18 npm install -D tailwindcss postcss autoprefixer

# 4. Inicializar Tailwind
docker run --rm -v $(pwd)/frontend:/app -w /app node:18 npx tailwindcss init -p
```

---

## 11. Comandos de Manutenção

### Banco de Dados:

```bash
# Resetar banco de dados (apaga tudo e recria)
docker compose exec app php artisan migrate:fresh --seed

# Criar nova migration
docker compose exec app php artisan make:migration nome_da_migration

# Executar migrations pendentes
docker compose exec app php artisan migrate

# Executar seeders
docker compose exec app php artisan db:seed
```

### Cache e otimização:

```bash
# Limpar cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear

# Otimizar para produção
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
```

### Testes:

```bash
# Executar testes
docker compose exec app php artisan test
```

---

## 12. Atualizações Futuras

Este documento será atualizado conforme o desenvolvimento progride. Mantenha-o como referência para o estado atual do projeto.

### Roadmap:
- [x] Setup inicial do Docker
- [x] Instalação do Laravel
- [x] Criação do banco de dados
- [x] Implementação da API REST
- [x] Setup do Frontend Vue.js
- [x] Integração Frontend + Backend
- [x] Repositório Git criado e publicado no GitHub
- [x] Branch de desenvolvimento (dev) criada
- [x] Sistema de Lições Interativas (Backend + Frontend)
- [x] Lições de nível B1 importadas (Verbos Modais, Reflexivos, Avverbi di Frequenza)
- [x] Estilização avançada do conteúdo das lições
- [ ] Sistema de autenticação
- [ ] Importação de provas CILS reais
- [ ] Sistema de gamificação
- [ ] Deploy em produção

## 14. ✅ Controle de Versão com Git/GitHub

**Status: IMPLEMENTADO EM 19/11/2025**

### Repositório Configurado:

✅ **Estrutura Git**
- Repositório: https://github.com/carloaf/imparatelingua
- Branch principal: `main` (produção)
- Branch de desenvolvimento: `dev`
- Commit inicial: 162 arquivos, 27.934 linhas de código

✅ **Branches Estratégicas:**

**`main` (Produção)**
- Código estável e testado
- Protected branch (recomendado)
- Deploy automático (futuro)
- Apenas via Pull Request

**`dev` (Desenvolvimento)**
- Branch ativa para desenvolvimento
- Testes e features novas
- Merge na main após aprovação
- Base para feature branches

### Workflow de Desenvolvimento:

```bash
# 1. Sempre trabalhar na branch dev ou feature
git checkout dev

# 2. Criar feature branch para nova funcionalidade
git checkout -b feature/nome-da-feature

# 3. Desenvolver e fazer commits
git add .
git commit -m "feat: Descrição da funcionalidade"

# 4. Push da feature branch
git push -u origin feature/nome-da-feature

# 5. Criar Pull Request no GitHub (feature → dev)

# 6. Após aprovação e merge, atualizar dev local
git checkout dev
git pull

# 7. Deletar feature branch local
git branch -d feature/nome-da-feature

# 8. Quando dev estiver estável, merge para main
git checkout main
git merge dev
git push
```

### Convenções de Commit:

Usando [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` - Nova funcionalidade
- `fix:` - Correção de bug
- `docs:` - Documentação
- `style:` - Formatação (sem mudança de código)
- `refactor:` - Refatoração de código
- `test:` - Testes
- `chore:` - Manutenção, build, CI/CD
- `perf:` - Melhorias de performance

**Exemplos:**
```bash
git commit -m "feat: Adiciona suporte a questões de áudio"
git commit -m "fix: Corrige highlight em questões fill_in_blank"
git commit -m "docs: Atualiza guia de importação de questões"
git commit -m "refactor: Otimiza carregamento de questões na API"
```

### Comandos Úteis:

```bash
# Ver branches locais e remotas
git branch -a

# Ver status
git status

# Ver diferenças
git diff

# Ver histórico
git log --oneline --graph --all

# Sincronizar com remoto
git fetch --all
git pull

# Limpar branches locais já mergeadas
git branch --merged | grep -v "\*" | xargs -n 1 git branch -d

# Desfazer último commit (mantém alterações)
git reset --soft HEAD~1

# Atualizar branch dev com mudanças da main
git checkout dev
git merge main
```

### Proteção de Branches (Recomendado no GitHub):

1. Acesse: `Settings` → `Branches` → `Add rule`
2. Branch name pattern: `main`
3. Marque:
   - ✅ Require a pull request before merging
   - ✅ Require approvals (1 approval)
   - ✅ Dismiss stale pull request approvals
   - ✅ Require status checks to pass
   - ✅ Require conversation resolution before merging

### Estrutura de Branches Futura:

```
main (produção)
  ↑
dev (desenvolvimento)
  ↑
feature/authentication
feature/audio-support
feature/gamification
fix/highlight-issue
docs/api-documentation
```

---

## 13. ✅ Lições de Nível B1 Importadas

**Status: IMPLEMENTADO EM 20/11/2025**

Novas lições focadas em conteúdo B1 foram importadas do arquivo ConteudoItaliano2025.txt!

### O que foi implementado:

✅ **3 Novas Lições de Nível B1:**

**Lição 4: Verbos Modais (Volere, Dovere, Potere)**
- Conteúdo: Verbos modais italianos (volere, dovere, potere, sapere)
- 7 exercícios de múltipla escolha
- Dificuldade: 3/5
- Tempo estimado: 35 minutos
- Foco: Expressão de volontà, necessità e possibilità

**Lição 5: Verbos Reflexivos (Verbi Riflessivi)**
- Conteúdo: Verbi riflessivi e pronomi riflessivi
- 6 exercícios de múltipla escolha
- Dificuldade: 3/5
- Tempo estimado: 30 minutos
- Foco: Routine quotidiana e ações reflexivas

**Lição 6: Avverbi di Frequenza e Routine**
- Conteúdo: Advérbios de frequência e expressões temporais
- 6 exercícios de múltipla escolha
- Dificuldade: 2/5
- Tempo estimado: 25 minutos
- Foco: Descrição de hábitos e rotina diária

✅ **Estilização Avançada do Conteúdo:**

Foram adicionados estilos CSS personalizados para melhor visualização:

- **Caixas de Introdução**: Background gradient roxo/azul
- **Seções de Verbos**: Background cinza claro com borda azul
- **Caixas de Regras**: Fundo azul claro com borda
- **Dicas CILS B1**: Fundo amarelo com borda laranja
- **Avisos Importantes**: Fundo vermelho claro
- **Exemplos**: Fundo verde claro com borda
- **Tabelas**: Estilizadas com cabeçalho azul
- **Listas**: Com ícones de check verde
- **Timeline**: Background verde claro com fonte monospace

### Estrutura do Conteúdo HTML:

As lições agora usam HTML formatado com classes CSS:

```html
<h2>Título Principal</h2>
<div class="intro">Introdução destacada</div>
<h3>Subtítulo com ícone</h3>
<div class="verbs-section">Seção de verbos</div>
<div class="rule-box">Regras importantes</div>
<div class="tip-box">Dicas para CILS B1</div>
<p class="example">Exemplo prático</p>
```

### Como executar o seeder:

```bash
# Importar lições B1
docker compose exec app php artisan db:seed --class=LessonsB1Seeder
```

### Comandos úteis:

```bash
# Ver lições no banco de dados
docker compose exec app php artisan tinker --execute="echo json_encode(\App\Models\Lesson::with('course')->get()->map(fn(\$l) => ['id' => \$l->id, 'title' => \$l->title, 'difficulty' => \$l->difficulty, 'time' => \$l->estimated_time]));"

# Testar API de lições
curl http://localhost:8080/api/v1/lessons/7?user_id=1
```

### Atualização: Sistema de Exercícios Interativos (19/11/2025)

✅ **Exercícios agora são totalmente interativos!**

Anteriormente os exercícios mostravam a resposta correta imediatamente. Agora o sistema funciona como um quiz real:

**Funcionalidades implementadas:**
- ✅ Usuário deve clicar em uma alternativa (A, B, C, D)
- ✅ Feedback visual imediato após responder:
  - Verde ✓ para resposta correta
  - Vermelho ✗ para resposta incorreta
  - Destaque da resposta correta em verde
- ✅ Contador de progresso: "3/7 exercícios"
- ✅ Taxa de acerto calculada automaticamente
- ✅ Mensagens motivacionais: "🎉 Correto!" ou "❌ Incorreto"
- ✅ Explicação do conceito após responder (quando disponível)
- ✅ Não é possível mudar a resposta após selecionar
- ✅ Border colorida no card do exercício (verde/vermelha)
- ✅ Ícone 🏆 quando completa todos os exercícios

**Interface melhorada:**
```
Exercícios (3/7)

Exercício 1
Claudia e Giovanni ________ partire per le vacanze.

[A) vogliono]  ← Clicável antes de responder
[B) voglio]    ← Hover azul
[C) vuole]     ← Desabilitado após responder
[D) volete]

✓ Correto!
Resposta correta: vogliono

💡 Explicação:
Com nomi plurali (loro), usa-se "vogliono"
```

**Estatísticas ao final:**
```
Progresso: 7/7 exercícios 🏆
Taxa de acerto: 85% (6/7 corretas)
```

### Próximos Passos:

- [ ] Importar mais lições (Passato Prossimo, Futuro, Condizionale)
- [x] Adicionar exercícios interativos ✅ CONCLUÍDO
- [ ] Importar provas CILS B1 oficiais
- [ ] Sistema de salvamento do progresso no backend
- [ ] Estatísticas de progresso por nível
- [ ] Timer opcional para exercícios
- [ ] Sistema de estrelas/pontos por desempenho

---

## 14. ✅ Frontend Vue.js Implementado

**Status: IMPLEMENTADO EM 15/11/2025**

O frontend Vue.js foi desenvolvido completamente e está rodando!

### O que foi implementado:

✅ **Projeto Vue 3 criado com Vite**
- TypeScript configurado
- Vue Router instalado
- Pinia para gerenciamento de estado

✅ **Container Docker para frontend:**
```yaml
frontend:
  image: node:20-alpine
  container_name: imparatelingua-frontend
  working_dir: /app
  volumes:
    - ./frontend:/app
    - /app/node_modules  # Volume anônimo para evitar conflitos de permissão
  ports:
    - "5173:5173"
  command: sh -c "npm install && npm run dev -- --host"
  depends_on:
    - nginx
  networks:
    - app-italiano-network
```

✅ **Configuração do Vite com proxy para API:**
```typescript
// vite.config.ts
server: {
  host: true,
  port: 5173,
  proxy: {
    '/api': {
      target: 'http://nginx:80',
      changeOrigin: true,
    },
  },
}
```

✅ **Serviço API com Axios:**
- Base URL configurada
- Interceptors para tratamento de erros
- Serviços para exams, categories, questions, progress

✅ **Componentes desenvolvidos:**
- **ExamList.vue** - Lista de exames com filtros e cards estilizados
- **QuestionCard.vue** - Card de questão interativo com suporte a 3 tipos:
  - **Múltipla Escolha**: Alternativas A/B/C/D clicáveis
  - **Preencher Lacuna**: Campo de texto para digitar resposta
  - **Verdadeiro/Falso**: Opções verdadeiro ou falso
  - Exibição de contexto e enunciado
  - Feedback visual (verde para correto, vermelho para errado)
  - Desabilita após responder
  - Indicador de dificuldade (estrelas)
  - Badge do tipo de questão
- **ProgressStats.vue** - Dashboard de estatísticas:
  - Total de questões respondidas
  - Taxa de acerto geral
  - Performance por categoria
  - Performance por nível
  - Barras de progresso coloridas

✅ **Views criadas:**
- **HomeView.vue** - Página inicial com:
  - Hero section com gradiente
  - Cards de funcionalidades
  - Estatísticas integradas
- **ExamListView.vue** - Página de listagem de exames
- **QuizView.vue** - Interface completa de quiz:
  - Barra de progresso
  - Navegação entre questões
  - Contador de questões
  - Botão "Finalizar Quiz" com resumo
  - Integração com API para enviar respostas

✅ **Rotas configuradas:**
```typescript
{
  path: '/',
  name: 'home',
  component: HomeView
},
{
  path: '/exams',
  name: 'exams',
  component: ExamListView
},
{
  path: '/exam/:id',
  name: 'exam',
  component: QuizView
}
```

✅ **TailwindCSS v3 configurado:**
- Cores personalizadas (primary, success, error, warning)
- Classes utilitárias disponíveis
- PostCSS configurado (ES Module)
- `postcss.config.js` e `tailwind.config.js` usando `export default`

### Acessando a aplicação:

```bash
# Frontend (Vue.js)
http://localhost:5173

# Backend API (Laravel)
http://localhost:8080/api/v1/...
```

### Estrutura final do frontend:

```
frontend/
├── src/
│   ├── assets/
│   │   └── main.css           # Tailwind CSS
│   ├── components/
│   │   ├── ExamList.vue       # ✅ Lista de exames
│   │   ├── QuestionCard.vue   # ✅ Card interativo de questão
│   │   └── ProgressStats.vue  # ✅ Dashboard de estatísticas
│   ├── services/
│   │   └── api.js             # ✅ Configuração Axios + serviços
│   ├── views/
│   │   ├── HomeView.vue       # ✅ Página inicial
│   │   ├── ExamListView.vue   # ✅ Listagem de exames
│   │   └── QuizView.vue       # ✅ Interface de quiz
│   ├── router/
│   │   └── index.ts           # ✅ Rotas configuradas
│   ├── App.vue
│   └── main.ts
├── tailwind.config.js         # ✅ Configuração do Tailwind
├── postcss.config.js          # ✅ PostCSS
├── vite.config.ts             # ✅ Vite + Proxy
├── tsconfig.json
├── package.json
└── env.d.ts                   # ✅ Declarações TypeScript para .vue

```

### Comandos úteis do frontend:

```bash
# Verificar logs do frontend
docker compose logs frontend

# Instalar nova dependência
docker compose exec frontend npm install [pacote]

# Acessar terminal do container
docker compose exec frontend sh

# Reiniciar container frontend
docker compose restart frontend

# Rebuild do frontend
docker compose up -d --build frontend
```

### Funcionalidades implementadas:

✅ **Listagem de exames**
- Cards com informações do exame
- Badge de nível (A1-C2) com cores
- Contador de questões
- Botão "Iniciar Exame"
- Botão discreto para excluir o exame e todo o seu conteúdo diretamente no card

✅ **Interface de Quiz**
- Exibição de contexto (quando disponível)
- Questão com formatação clara
- 4 alternativas (A/B/C/D)
- Feedback visual imediato
- Barra de progresso
- Navegação sequencial
- Botão finalizar com resumo
- Nas provas de lacunas, apenas a frase correspondente à questão atual recebe destaque visual, guiando o estudo sem poluir o restante do texto

✅ **Dashboard de estatísticas**
- Total de questões
- Acertos e erros
- Taxa de acerto (%)
- Performance por categoria
- Performance por nível (A1-C2)
- Barras de progresso coloridas

✅ **Comunicação com API**
- Listagem de exames da API
- Carregamento de questões
- Envio de respostas
- Carregamento de estatísticas
- Tratamento de erros

✅ **Atualizações CILS B1 (Nov/2025)**
- Prova 4 de Analisi delle Strutture convertida para questões de múltipla escolha para manter consistência com o app
- JSON oficial (`cils_b1_dicembre_2017.json`) corrigido (session em maiúsculo) e reimportado com `--replace`

## Sistema de Importação de Conteúdo

✅ **Estrutura de Importação**
- Formato JSON para importação de questões
- Campos `explanation` nas questões (explicação educacional do conceito)
- Campos `justification` nas respostas (explicação do porquê está correta/incorreta)
- Suporte para exames oficiais CILS com campos específicos
- Validação completa de estrutura JSON
- Transações para garantir integridade dos dados

✅ **Comando Artisan**
```bash
php artisan questions:import {arquivo.json} [--replace]
```
- `--replace`: Substitui exame existente com mesmo nome e ano
- Validação de categorias existentes
- Barra de progresso durante importação
- Relatório detalhado de importação
- Tratamento de erros com rollback automático

✅ **Campos Educacionais**
- **Explanation** (Questões): Explicação do conceito gramatical ou tópico da questão
- **Justification** (Respostas): Explicação detalhada do porquê a resposta está correta ou incorreta
- **Context** (Questões): Contexto gramatical ou situacional da questão

✅ **Interface Atualizada**
- Exibição da justificativa da resposta selecionada após responder
- Exibição da explicação do conceito após responder
- Análise completa de todas as alternativas (múltipla escolha)
- Feedback visual diferenciado para cada tipo de informação
- Layout educacional aprimorado

✅ **Exames Oficiais CILS**
- Suporte para identificação de exames oficiais CILS
- Campo `is_official` para destacar exames certificados
- Campo `session` para especificar Giugno ou Dicembre
- Campo `exam_code` para código único (ex: CILS_B1_DIC_2022)
- Badge especial "CILS" na interface para exames oficiais
- Foco no nível B1 conforme requisitos do projeto
- **Exame oficial importado**: CILS UNO-B1 Dicembre 2017 (37 questões completas)

✅ **Categorias CILS Oficiais**
- **Ascolto** - Compreensão auditiva
- **Comprensione della Lettura** - Compreensão de leitura
- **Analisi delle Strutture di Comunicazione** - Análise das estruturas (gramática/léxico)
- **Produzione Scritta** - Produção escrita
- **Produzione Orale** - Produção oral

✅ **Tipos de Questão Suportados**
- `multiple_choice` - Múltipla escolha tradicional
- `fill_in_blank` - Preencher lacuna simples
- `true_false` - Verdadeiro ou falso
- `multiple_selection` - Seleção múltipla (mais de uma resposta correta)
- `matching` - Associação/correspondência
- `ordering` - Ordenação de elementos
- `fill_in_the_blanks` - Preencher múltiplas lacunas
- `multiple_choice_cloze` - Múltipla escolha contextualizada (cloze test)
- `reorder_text` - Reordenar texto/frases

✅ **Documentação**
- Guia completo de importação (`backend/IMPORT_GUIDE.md`)
- Arquivo de exemplo com 5 questões (`backend/storage/app/imports/exemplo_questoes.json`)
- Exame CILS B1 Dicembre 2022 completo (`backend/storage/app/imports/cils_b1_dic_2022.json`)
- Exemplos de todos os tipos de questão
- Boas práticas de criação de conteúdo
- Resolução de problemas comuns

### Formato JSON de Importação

```json
{
  "exam": {
    "name": "CILS B1 - Dicembre 2022",
    "level": "B1",
    "year": 2022,
    "description": "Certificazione di Italiano come Lingua Straniera - Livello B1 - Sessione di Dicembre 2022",
    "is_official": true,
    "session": "Dicembre",
    "exam_code": "CILS_B1_DIC_2022"
  },
  "questions": [
    {
      "category": "gramatica",
      "question_text": "Texto da questão",
      "question_type": "multiple_choice|fill_in_blank|true_false",
      "difficulty": 1-5,
      "context": "Contexto gramatical",
      "order": 1,
      "explanation": "Explicação educacional do conceito",
      "answers": [
        {
          "answer_text": "Resposta",
          "is_correct": true,
          "order": 1,
          "justification": "Justificativa da resposta"
        }
      ]
    }
  ]
}
```

### Suporte Frontend por Tipo de Questão

✅ **Totalmente Implementados:**
- `multiple_choice` - Interface com opções A, B, C, D clicáveis
- `fill_in_blank` - Campo de entrada de texto com validação
- `true_false` - Botões Verdadeiro/Falso
- `multiple_selection` - Interface com checkboxes para múltiplas respostas
  - Permite selecionar/desselecionar múltiplas opções
  - Botão de confirmação mostra contagem de selecionadas
  - Validação compara todas as respostas corretas
  - Visual feedback (verde para corretas, vermelho para incorretas)
- `fill_in_the_blanks` - **NOVO!** Preencher múltiplas lacunas numeradas
  - Campos de texto individuais para cada lacuna
  - Validação case-insensitive
  - Feedback visual por lacuna (verde/vermelho)
  - Scroll para questões com muitas lacunas
  - Usado em: Analisi delle strutture - Prova n. 1 e 2
- `multiple_choice_cloze` - Cloze test com dropdowns
  - Para lacunas independentes (Prova n. 3) usa o array `answers` padrão (A, B, C, D)
  - Mantém suporte a estruturas avançadas com `options` quando disponível
  - Feedback visual por seleção e resumo da resposta correta
  - Contexto completo do parágrafo exibido no topo da carta
- `ordering` - **NOVO!** Sequenciamento com inputs numéricos
  - Lista todas as frases embaralhadas com campo numérico para ordenar
  - Feedback imediato por trecho após envio e indicação da posição correta
  - Ideal para Comprensione (Provas 3 e 4) e outras atividades de reconstrução de texto
- `matching` - **NOVO!** Associação de frases com situações
  - Interface com dropdowns para associação
  - Exibição clara da frase e opções de situação
  - Feedback mostra resposta correta quando erra
  - Usado em: Analisi delle strutture - Prova n. 4

🔄 **Ainda não implementados:**
- `reorder_text` - Arrastar e soltar ou inputs numerados

### Funcionalidades Recentes

✅ **Suporte Completo para Questões CILS de Analisi delle Strutture (2025-01-18)**
- Implementados 3 novos tipos de questão especializadas
- `fill_in_the_blanks`: Preencher múltiplas lacunas (Prova n. 1 e 2)
  - Campos individuais numerados
  - Validação automática case-insensitive
  - Feedback por lacuna
- `multiple_choice_cloze`: Cloze test com múltipla escolha (Prova n. 3)
  - Dropdown por lacuna com 4 opções
  - Contexto adicional para cada lacuna
- `matching`: Associação de frases com situações (Prova n. 4)
  - Interface intuitiva com dropdowns
  - Feedback detalhado com resposta correta
- Documentação completa em `ESTRUTURA_QUESTOES.md`

✅ **Ordenação e Cloze Otimizados (2025-11-18)**
- `ordering`: campos numéricos por trecho, validação automática e exibição da posição correta
- `multiple_choice_cloze`: agora lê diretamente o array `answers` (A-D) quando cada lacuna é uma questão independente e mantém suporte a formatos com `options`
- Prompt oficial (`PROMPT_GERACAO_JSON.md`) atualizado com instruções extras (parágrafos completos nas lacunas, letras obrigatórias e estrutura única para cloze)
- Guia enfatiza replicar o parágrafo completo no campo `context` para Provas 1 e 2

✅ **Navegação Flexível (2025-01-18)**
- Usuários podem pular questões sem responder
- Botão "Próxima" muda para "Pular" quando questão não respondida
- Permite revisitar questões anteriores a qualquer momento
- Facilita estratégia de responder primeiro as questões mais fáceis

✅ **CILS B1 Dicembre 2017 Importado**
- 37 questões oficiais do exame CILS
- Distribuição: 15 Ascolto, 9 Comprensione della Lettura, 13 Analisi delle Strutture
- Todas as questões com justificativas e explicações
- Pronto para importar questões de Analisi delle Strutture nos novos formatos

### Sistema de Extração Automática de PDFs (NOVO!)

✅ **Extração de Texto de PDFs (2025-01-18)**
- Suporte para PDFs com texto selecionável (extração direta)
- Suporte para PDFs escaneados/imagem (OCR com Tesseract)
- Idiomas: Italiano e Português
- Ferramentas instaladas:
  - `poppler-utils` (pdftotext, pdftoppm)
  - `tesseract-ocr` com idiomas italiano e português
  - `imagemagick` para processamento de imagens
  - `ghostscript` para manipulação de PDFs
- Pacotes PHP: `spatie/pdf-to-text`, `thiagoalessio/tesseract_ocr`

✅ **Comandos Implementados:**
```bash
# Extrair texto do PDF
php artisan pdf:extract "arquivo.pdf"           # Extração direta
php artisan pdf:extract "arquivo.pdf" --ocr     # Com OCR (para PDFs escaneados)

# Parsear texto extraído e gerar JSON AUTOMATICAMENTE
php artisan cils:parse "arquivo_extracted.txt"                    # Todas as seções
php artisan cils:parse "arquivo_extracted.txt" --output=custom.json
php artisan cils:parse "arquivo_extracted.txt" --category=Ascolto # Apenas uma categoria
```

✅ **Funcionalidades:**
- Conversão automática de PDF para imagens (para OCR)
- Barra de progresso durante processamento
- Detecção automática de:
  - Tipo de prova (Ascolto, Comprensione, Analisi)
  - Nível do exame (A1-C2)
  - Ano e sessão (Giugno/Dicembre)
  - Estrutura de questões (Prova n. 1, 2, 3...)
  - Questões numeradas com opções A, B, C, D
  - Instruções e contexto das provas
- Geração automática de JSON estruturado
  - Questões extraídas com tipo correto (multiple_choice)
  - Respostas ordenadas automaticamente
  - Metadados do exame completos
  - Estrutura pronta para importação
- Preview do texto extraído
- Processamento por categoria específica (--category)

⚠️ **Requer Revisão Manual (Reduzida!):**
- ✅ Metadados do exame extraídos automaticamente
- ✅ Questões detectadas e estruturadas corretamente
- ✅ Opções A/B/C/D extraídas e ordenadas
- ✅ Contexto e instruções capturados
- ✅ Todos os tipos de questão CILS implementados (10 tipos)
- ⚠️ Respostas corretas precisam ser marcadas (is_correct: true)
- ⚠️ Justificativas e explicações devem ser adicionadas manualmente

📊 **Taxa de Extração Automática:**
- **Ascolto**: 15/15 questões (100%) - 7 + 7 + 1 seleção múltipla
- **Comprensione**: 8/9 questões (89%) - 6 + 1 seleção + 1 ordenação
- **Analisi**: 52/61 questões (85%) - 15 artigos + 20 verbos + 10/15 cloze + 7/10 matching
- **Total**: ~75 de 85 questões (~88% de sucesso automático)

🔧 **Tipos de Questão Implementados:**
1. `multiple_choice` - Múltipla escolha simples (A/B/C/D)
2. `multiple_selection` - Seleção múltipla (escolher N de M opções)
3. `ordering` - Reordenação de partes de texto
4. `fill_in_blank` - Preenchimento livre (artigos/verbos)
5. `multiple_choice_cloze` - Lacunas com opções múltiplas
6. `matching` - Associação de expressões com contextos

⚠️ **Limitações Conhecidas:**
- OCR pode mesclar questões adjacentes (exemplo: questões 3+4 da Comprensione)
- Variações de formatação afetam regex (pipes, espaçamento)
- Questões divididas entre páginas podem não ser capturadas completamente
- Requer revisão manual para ~12% das questões

📖 **Documentação Completa:**
- `backend/storage/app/imports/GUIA_EXTRACAO_PDF.md`
- `backend/storage/app/imports/ESTRUTURA_QUESTOES.md`

### Próximas melhorias:

- [ ] IA para detectar respostas corretas automaticamente
- [ ] Sistema de autenticação (login/registro)
- [ ] Persistência de progresso por usuário
- [ ] Suporte a áudio para questões de Ascolto
- [ ] Implementar tipo de questão restante (`reorder_text`)
- [ ] Modo de estudo por categoria
- [ ] Sistema de gamificação (badges, pontos)
- [ ] Gráficos mais avançados
- [ ] Timer para questões
- [ ] Modo de revisão (rever questões erradas)
- [ ] Exportar relatórios de desempenho
- [ ] Interface web para importação de questões
- [ ] Interface web para revisão de JSON extraído de PDF
- [ ] Validador de JSON online
- [ ] Sistema de templates de questões
- [ ] Processamento em batch de múltiplos PDFs

---

## 15. ✅ Sistema de Lições Interativas Implementado

**Status: IMPLEMENTADO EM 19/11/2025**

O sistema de lições interativas foi desenvolvido completamente com backend e frontend integrados!

### Backend Implementado:

✅ **Estrutura do Banco de Dados:**
- **Tabela `courses`**: Armazena cursos de italiano
  - Campos: id, title, slug, description, level (A1-C2), image_url, is_active, order
  - Relacionamento: hasMany lessons
  
- **Tabela `lessons`**: Armazena lições individuais
  - Campos: course_id (FK), title, slug, content_italian (longText), content_portuguese (longText), exercises (JSON), lesson_type (enum), difficulty (1-5), estimated_time, order
  - Suporta conteúdo bilíngue completo
  - Exercícios armazenados em formato JSON flexível
  
- **Tabela `user_lesson_progress`**: Rastreia progresso do usuário
  - Campos: user_id, lesson_id, status (not_started/in_progress/completed), time_spent, completion_percentage, exercises_completed, exercises_correct
  - Timestamps: started_at, completed_at, last_accessed_at
  - Auto-tracking: progresso atualizado automaticamente ao acessar lição

✅ **Models com Relacionamentos:**
- `Course`: hasMany lessons, userProgress method
- `Lesson`: belongsTo course, hasMany userProgress, progressForUser method
- `UserLessonProgress`: belongsTo user and lesson

✅ **API Controllers:**
- **CourseController**:
  - `index()`: Lista todos os cursos ativos
  - `show($id)`: Detalhes do curso com lições e progresso do usuário
  
- **LessonController**:
  - `show($id)`: Retorna lição completa com tracking automático
  - `updateProgress($id)`: Atualiza progresso (tempo, percentual, exercícios)
  - `complete($id)`: Marca lição como 100% concluída

✅ **Rotas API (`/api/v1/...`):**
```
GET    /api/v1/courses           - Lista cursos
GET    /api/v1/courses/{id}      - Detalhes curso + lições + progresso
GET    /api/v1/lessons/{id}      - Conteúdo lição + tracking
PUT    /api/v1/lessons/{id}/progress  - Atualizar progresso
POST   /api/v1/lessons/{id}/complete  - Marcar como concluída
```

✅ **Seeder com Dados Reais:**
- Curso: "Italiano Básico 2025" (Nível A1, 3 lições, 95min total)
- Lição 1: Alfabeto e Pronúncia (30min, dificuldade 1)
- Lição 2: Saudações e Apresentações (25min, dificuldade 1)
- Lição 3: Verbos ESSERE e AVERE (40min, dificuldade 2)
- Conteúdo extraído de ConteudoItaliano2025.txt (3.872 linhas)

### Frontend Implementado:

✅ **Views Vue.js:**
- **CourseListView.vue**: Listagem de cursos disponíveis
  - Cards com informações do curso (título, descrição, nível)
  - Badges coloridos por nível (A1-C2)
  - Estatísticas: total de lições e tempo estimado
  - Gradientes visuais atraentes
  - Link para detalhes do curso
  
- **CourseDetailView.vue**: Detalhes do curso com lista de lições
  - Header do curso com informações completas
  - Lista de lições com índice numérico
  - Badges por tipo de lição (teoria, gramática, vocabulário, etc.)
  - Barra de progresso visual por lição
  - Status da lição (não iniciado, em progresso, concluído)
  - Botão para iniciar/revisar lição
  
- **LessonView.vue**: Interface de estudo da lição
  - Sistema de tabs para alternar entre italiano e português
  - Tab "Conteúdo em Italiano": Texto em italiano completo
  - Tab "Explicação em Português": Explicações detalhadas
  - Seção de exercícios com visualização clara
  - Exibição de respostas corretas
  - Ícones por tipo de exercício
  - Botão "Marcar como Concluído" integrado com API
  - Barra de progresso em tempo real

✅ **Serviços API (api.js):**
```javascript
// courseService
getAll()                    - Lista cursos
getById(id, userId)         - Detalhes + progresso

// lessonService
getById(id, userId)         - Conteúdo + tracking
updateProgress(id, data)    - Atualizar progresso
complete(id, userId)        - Marcar concluída
```

✅ **Rotas Vue Router:**
```
/courses              → CourseListView
/courses/:id          → CourseDetailView
/lesson/:id           → LessonView
```

✅ **HomeView Atualizada:**
- Dois cards principais:
  - **Cursos Estruturados** (azul): Link para /courses
  - **Provas CILS** (roxo): Link para /exams
- Features destacando conteúdo bilíngue e exercícios práticos

### Funcionalidades Implementadas:

✅ **Conteúdo Bilíngue:**
- Italiano: Conteúdo completo da lição em italiano
- Português: Explicações detalhadas em português
- Fácil alternância entre idiomas com tabs

✅ **Tracking Automático de Progresso:**
- Status atualizado ao acessar lição (not_started → in_progress)
- Campo `last_accessed_at` registra último acesso
- Barra de progresso visual (0-100%)
- Botão para marcar como concluído

✅ **Exercícios Contextualizados:**
- Tipos suportados: pronunciation, fill_blank, multiple_choice, translate
- Visualização de opções (quando aplicável)
- Resposta correta destacada
- Ícones identificando tipo de exercício

✅ **Design Responsivo:**
- Gradientes atraentes (blue-to-indigo)
- Cards com sombras e efeitos hover
- Badges coloridos por nível e tipo
- Loading states e error handling
- Animações suaves nas transições

### Testado e Validado:

✅ API retorna dados corretamente:
```bash
# Listar cursos
curl http://localhost:8080/api/v1/courses
# Resultado: 1 curso com 3 lições, 95min total

# Detalhes do curso
curl http://localhost:8080/api/v1/courses/1?user_id=1
# Resultado: Curso com array de 3 lições + progresso

# Detalhes da lição
curl http://localhost:8080/api/v1/lessons/1?user_id=1
# Resultado: Conteúdo italiano/português + exercícios + progresso
```

✅ Frontend acessível:
- http://localhost:5173 - Interface Vue.js
- http://localhost:8080/api/v1/... - API Laravel

### Próximos Passos Sugeridos:

- [ ] Importar mais lições do ConteudoItaliano2025.txt (páginas 4-7+)
- [ ] Criar parser automático para importar lições do arquivo .txt
- [ ] Implementar sistema de exercícios interativos (não apenas visualização)
- [ ] Adicionar validação de respostas dos exercícios
- [ ] Sistema de pontuação e feedback
- [ ] Gráficos de progresso por curso
- [ ] Certificados de conclusão