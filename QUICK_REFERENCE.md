# URLs e Comandos Úteis - ImparaLingua

## URLs da Aplicação

### Frontend (Vue.js)
```
http://localhost:5173
```
- Página inicial: http://localhost:5173/
- Lista de exames: http://localhost:5173/exams
- Quiz: http://localhost:5173/exam/:id

### Backend API (Laravel)
```
http://localhost:8080/api/v1
```

#### Endpoints disponíveis:

**Exames:**
- GET http://localhost:8080/api/v1/exams
- GET http://localhost:8080/api/v1/exams/{id}
- GET http://localhost:8080/api/v1/exams/{id}/questions

**Categorias:**
- GET http://localhost:8080/api/v1/categories
- GET http://localhost:8080/api/v1/categories/{id}
- GET http://localhost:8080/api/v1/categories/{id}/questions

**Questões:**
- GET http://localhost:8080/api/v1/questions
- GET http://localhost:8080/api/v1/questions/{id}
- POST http://localhost:8080/api/v1/questions/{id}/answer

**Progresso:**
- GET http://localhost:8080/api/v1/user/progress?user_id=1
- GET http://localhost:8080/api/v1/user/statistics?user_id=1

### Banco de Dados MySQL
```
Host: localhost
Porta: 33061
Database: app_italiano_db
User: app_user
Password: user_password
```

## Comandos Docker

### Gerenciamento de Containers

```bash
# Iniciar todos os containers
docker compose up -d

# Parar todos os containers
docker compose down

# Reiniciar um container específico
docker compose restart [app|nginx|db|frontend]

# Ver logs de um container
docker compose logs [app|nginx|db|frontend]

# Ver logs em tempo real
docker compose logs -f [app|nginx|db|frontend]

# Ver status dos containers
docker compose ps

# Rebuild dos containers
docker compose up -d --build
```

### Backend (Laravel)

```bash
# Acessar bash do container
docker compose exec app bash

# Executar comandos Artisan
docker compose exec app php artisan [comando]

# Migrations
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan make:migration [nome]

# Models e Controllers
docker compose exec app php artisan make:model [Nome]
docker compose exec app php artisan make:controller [NomeController]

# Seeders
docker compose exec app php artisan db:seed
docker compose exec app php artisan make:seeder [NomeSeeder]

# Cache
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear

# Composer
docker compose exec app composer install
docker compose exec app composer update
docker compose exec app composer require [pacote]
```

### Frontend (Vue.js)

```bash
# Acessar shell do container
docker compose exec frontend sh

# Instalar dependências
docker compose exec frontend npm install

# Instalar novo pacote
docker compose exec frontend npm install [pacote]

# Instalar pacote de desenvolvimento
docker compose exec frontend npm install -D [pacote]

# Atualizar dependências
docker compose exec frontend npm update

# Build para produção
docker compose exec frontend npm run build

# Type checking
docker compose exec frontend npm run type-check
```

## Testes da API com cURL

### Listar todos os exames
```bash
curl http://localhost:8080/api/v1/exams
```

### Ver detalhes de um exame
```bash
curl http://localhost:8080/api/v1/exams/1
```

### Listar questões de um exame
```bash
curl http://localhost:8080/api/v1/exams/1/questions
```

### Listar categorias
```bash
curl http://localhost:8080/api/v1/categories
```

### Ver uma questão específica
```bash
curl http://localhost:8080/api/v1/questions/1
```

### Responder uma questão
```bash
curl -X POST http://localhost:8080/api/v1/questions/1/answer \
  -H "Content-Type: application/json" \
  -d '{"answer_id": 1, "user_id": 1}'
```

### Ver progresso do usuário
```bash
curl http://localhost:8080/api/v1/user/progress?user_id=1
```

### Ver estatísticas do usuário
```bash
curl http://localhost:8080/api/v1/user/statistics?user_id=1
```

## Solução de Problemas

### Permissões no Laravel

```bash
# Ajustar permissões das pastas storage e cache
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Permissões nas migrations

```bash
# Sempre executar após criar/alterar migrations
sudo chmod -R 755 backend/database/migrations
```

### Reinstalar dependências

```bash
# Backend
docker compose exec app rm -rf vendor
docker compose exec app composer install

# Frontend
docker compose exec frontend rm -rf node_modules
docker compose exec frontend npm install
```

### Resetar banco de dados

```bash
# CUIDADO: Apaga todos os dados!
docker compose exec app php artisan migrate:fresh --seed
```

### Container não inicia

```bash
# Ver logs detalhados
docker compose logs [nome-do-container]

# Rebuild forçado
docker compose down
docker compose up -d --build --force-recreate
```

## Estrutura de Pastas

```
imparalingua/
├── backend/              # Laravel API
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       └── Api/  # Controllers da API
│   │   └── Models/       # Eloquent Models
│   ├── database/
│   │   ├── migrations/   # Database schema
│   │   └── seeders/      # Dados de exemplo
│   ├── routes/
│   │   └── api.php       # Rotas da API
│   └── .env              # Configurações
│
├── frontend/             # Vue.js SPA
│   ├── src/
│   │   ├── assets/       # CSS, imagens
│   │   ├── components/   # Componentes Vue
│   │   ├── views/        # Páginas
│   │   ├── services/     # API services
│   │   └── router/       # Rotas
│   ├── vite.config.ts    # Config do Vite
│   └── package.json      # Dependências
│
├── docker/
│   └── nginx/
│       └── default.conf  # Config do Nginx
│
├── docker-compose.yml    # Orquestração
├── guia_dev.md          # Guia de desenvolvimento
└── README.md            # Documentação
```

## Fluxo de Desenvolvimento

1. **Modificar código:**
   - Backend: Edite arquivos em `backend/`
   - Frontend: Edite arquivos em `frontend/src/`

2. **As mudanças são refletidas automaticamente:**
   - Laravel com volumes montados
   - Vite com hot reload

3. **Criar novas features:**
   ```bash
   # Backend: Model + Migration + Controller
   docker compose exec app php artisan make:model NomeModel -m -c
   
   # Frontend: Novo componente
   # Crie arquivo em frontend/src/components/
   ```

4. **Testar:**
   - Frontend: http://localhost:5173
   - API: http://localhost:8080/api/v1/...
   - Usar cURL ou Postman

5. **Commit:**
   ```bash
   git add .
   git commit -m "Descrição das mudanças"
   git push
   ```

## Dados de Teste

### Usuário padrão:
- ID: 1
- Email: test@example.com

### Exames disponíveis:
- CILS A1 - 2024
- CILS A2 - 2024

### Categorias:
- Gramática (grammar)
- Vocabulário (vocabulary)
- Interpretação de Texto (reading)

### Questões:
- 4 questões de exemplo com 4 alternativas cada
