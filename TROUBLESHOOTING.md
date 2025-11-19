# Troubleshooting - ImparaLingua

Este documento contém soluções para problemas comuns encontrados durante o desenvolvimento.

## 🔧 Problemas Comuns e Soluções

### 1. Erro: "module is not defined in ES module scope"

**Sintomas:**
```
[ReferenceError] module is not defined in ES module scope
This file is being treated as an ES module because it has a '.js' file extension 
and '/app/package.json' contains "type": "module"
```

**Causa:**
O `package.json` do Vue 3 está configurado com `"type": "module"`, então todos os arquivos `.js` são tratados como ES Modules. Arquivos de configuração usando `module.exports` (CommonJS) causam este erro.

**Solução:**
Converter os arquivos de configuração para sintaxe ES Module:

```javascript
// ❌ ERRADO (CommonJS)
module.exports = {
  plugins: {
    tailwindcss: {},
  },
}

// ✅ CORRETO (ES Module)
export default {
  plugins: {
    tailwindcss: {},
  },
}
```

**Arquivos afetados:**
- `postcss.config.js`
- `tailwind.config.js`
- `vite.config.ts` (já usa ES Module por padrão)

**Comando para aplicar:**
```bash
docker compose restart frontend
```

---

### 2. Erro: "Cannot find module '*.vue'"

**Sintomas:**
```
Cannot find module '../views/HomeView.vue' or its corresponding type declarations.
```

**Causa:**
TypeScript não reconhece arquivos `.vue` como módulos válidos sem uma declaração de tipos.

**Solução:**
Adicionar declaração de tipos no arquivo `env.d.ts`:

```typescript
/// <reference types="vite/client" />

declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}
```

---

### 3. Permissões negadas em `node_modules/`

**Sintomas:**
```
rm: não foi possível remover 'frontend/node_modules/...': Permissão negada
```

**Causa:**
Arquivos em `node_modules` foram criados pelo container Docker como root e não podem ser deletados pelo usuário do host.

**Solução 1 (Recomendada):**
Usar volume anônimo no `docker-compose.yml`:

```yaml
frontend:
  volumes:
    - ./frontend:/app
    - /app/node_modules  # Volume anônimo
```

**Solução 2 (Correção pontual):**
```bash
sudo chown -R $(whoami):$(whoami) frontend/
```

---

### 4. Erro: "Unknown at rule @tailwind"

**Sintomas:**
```
Unknown at rule @tailwind
Unknown at rule @apply
```

**Causa:**
Linter CSS do VS Code não reconhece diretivas do Tailwind.

**Solução:**
Este é apenas um warning do linter. O PostCSS processa corretamente em runtime. Para remover os warnings, instale a extensão "Tailwind CSS IntelliSense" no VS Code.

**Alternativa:**
Adicionar no `.vscode/settings.json`:
```json
{
  "css.lint.unknownAtRules": "ignore"
}
```

---

### 5. Container frontend não inicia

**Sintomas:**
```
docker compose ps
# frontend com status "Exit 1"
```

**Diagnóstico:**
```bash
docker compose logs frontend
```

**Soluções comuns:**

**A. Erro de sintaxe no package.json:**
```bash
docker compose exec frontend npm install
```

**B. Porta 5173 já em uso:**
```bash
# Ver processos na porta
sudo lsof -i :5173
# Matar processo
sudo kill -9 [PID]
```

**C. Dependências corrompidas:**
```bash
docker compose exec frontend rm -rf node_modules package-lock.json
docker compose restart frontend
```

---

### 6. API não responde (CORS / 404)

**Sintomas:**
```
Access to XMLHttpRequest at 'http://nginx:80/api/v1/exams' has been blocked by CORS policy
```

**Causa:**
Proxy do Vite não está configurado ou backend não está respondendo.

**Verificação:**
```bash
# Testar API diretamente
curl http://localhost:8080/api/v1/exams

# Ver logs do Nginx
docker compose logs nginx

# Ver logs do Laravel
docker compose logs app
```

**Solução:**
Verificar `vite.config.ts`:

```typescript
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

---

### 7. Erro: "npm ERR! process terminated"

**Sintomas:**
```
npm error process terminated
npm error signal SIGINT
```

**Causa:**
Comando npm foi interrompido (Ctrl+C) durante execução.

**Solução:**
```bash
# Reiniciar container
docker compose restart frontend

# Se persistir, rebuild
docker compose up -d --build frontend
```

---

### 8. Migrações Laravel não executam

**Sintomas:**
```
SQLSTATE[42S02]: Base table or view not found
```

**Verificação:**
```bash
# Ver status das migrations
docker compose exec app php artisan migrate:status

# Ver conexão com banco
docker compose exec app php artisan tinker
# > DB::connection()->getPdo();
```

**Solução 1 - Permissões:**
```bash
sudo chmod -R 755 backend/database/migrations
```

**Solução 2 - Executar migrations:**
```bash
docker compose exec app php artisan migrate
```

**Solução 3 - Reset completo:**
```bash
docker compose exec app php artisan migrate:fresh --seed
```

---

### 9. Banco de dados não conecta

**Sintomas:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Verificação:**
```bash
# Verificar se container do MySQL está rodando
docker compose ps db

# Verificar logs
docker compose logs db

# Testar conexão
docker compose exec app php artisan tinker
# > DB::connection()->getPdo();
```

**Solução:**
Verificar `.env` do Laravel:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=app_italiano_db
DB_USERNAME=app_user
DB_PASSWORD=user_password
```

**Importante:** `DB_HOST=db` (nome do serviço no docker-compose), **não** `localhost`!

---

### 10. Erro: "Target class [Controller] does not exist"

**Sintomas:**
```
Target class [ExamController] does not exist
```

**Causa:**
Namespace incorreto no arquivo de rotas ou controller não existe.

**Solução:**
Verificar namespace nas rotas `api.php`:

```php
use App\Http\Controllers\Api\ExamController;

Route::apiResource('exams', ExamController::class);
```

---

## 🔍 Comandos de Diagnóstico

### Verificar status geral
```bash
docker compose ps
docker compose logs --tail=50
```

### Verificar logs específicos
```bash
docker compose logs frontend -f
docker compose logs app -f
docker compose logs nginx -f
docker compose logs db -f
```

### Verificar conectividade entre containers
```bash
# Do app para db
docker compose exec app ping db

# Do frontend para nginx
docker compose exec frontend ping nginx
```

### Verificar variáveis de ambiente
```bash
# Laravel
docker compose exec app php artisan config:show

# Frontend
docker compose exec frontend printenv
```

### Limpar cache Laravel
```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear
```

### Rebuild completo
```bash
# Parar tudo
docker compose down

# Remover volumes (CUIDADO: apaga dados do banco!)
docker compose down -v

# Rebuild
docker compose up -d --build --force-recreate
```

---

### 11. Componente não exibe alternativas de múltipla escolha

**Sintomas:**
- Questão aparece mas não mostra as alternativas A/B/C/D
- Apenas título, contexto e badges são exibidos

**Causa:**
Mismatch entre nome do campo na API (`answers` ou `answers_ordered`) e o que o componente Vue espera.

**Solução:**
Garantir que o backend retorne `answers_ordered` nos endpoints:

```php
// backend/app/Http/Controllers/Api/ExamController.php
public function questions(string $id): JsonResponse
{
    $exam = Exam::with(['questionsOrdered.answersOrdered', 'questionsOrdered.category'])
        ->findOrFail($id);

    $questions = $exam->questionsOrdered->map(function ($question) {
        return $question->toArray();
    });

    return response()->json([
        'success' => true,
        'data' => [
            'exam' => $exam->only(['id', 'name', 'level', 'year']),
            'questions' => $questions,
        ],
    ]);
}

// backend/app/Http/Controllers/Api/QuestionController.php
public function index(Request $request): JsonResponse
{
    $query = Question::with(['exam', 'category', 'answersOrdered']);
    // ... resto do código
}
```

**Verificação:**
```bash
# A resposta deve ter 'answers_ordered' não 'answers'
curl http://localhost:8080/api/v1/exams/2/questions | grep -o "answers_ordered"
```

---

## 📋 Checklist de Problemas

Quando algo não funciona, siga esta ordem:

1. ✅ Todos os containers estão rodando? (`docker compose ps`)
2. ✅ Há erros nos logs? (`docker compose logs`)
3. ✅ As portas estão disponíveis? (`lsof -i :5173`, `lsof -i :8080`)
4. ✅ O `.env` está correto?
5. ✅ As migrations foram executadas? (`php artisan migrate:status`)
6. ✅ As dependências estão instaladas? (`node_modules`, `vendor`)
7. ✅ O cache foi limpo?
8. ✅ Tentou reiniciar os containers?

---

## 🆘 Último Recurso

Se nada funcionar:

```bash
# 1. Parar tudo
docker compose down

# 2. Limpar volumes (apaga dados!)
docker compose down -v

# 3. Limpar cache do Docker
docker system prune -a

# 4. Rebuild completo
docker compose up -d --build --force-recreate

# 5. Reinstalar dependências
docker compose exec app composer install
docker compose exec frontend npm install

# 6. Executar migrations
docker compose exec app php artisan migrate:fresh --seed
```

---

## 📞 Suporte

Se o problema persistir:

1. Verifique os logs detalhados
2. Consulte a documentação oficial:
   - [Laravel](https://laravel.com/docs)
   - [Vue.js](https://vuejs.org/guide)
   - [Vite](https://vitejs.dev/guide)
   - [Docker Compose](https://docs.docker.com/compose)
3. Verifique issues no GitHub do projeto

---

**Última atualização:** 15/11/2025
