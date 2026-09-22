# Instruções de Deploy — QNB Apps (Laravel 12)

> **Guia completo baseado em erros reais encontrados durante o deploy do qnb-imobiliaria em hosting partilhada cPanel.**
> Este documento serve para qualquer IA ou pessoa que vá fazer deploy de uma das apps QNB.

---

## Arquitetura

Duas apps Laravel partilham a mesma BD MySQL:

| App | Domínio | Fila |
|-----|---------|------|
| **qnb-imobiliaria** | `imobiliaria.qnbangola.ao` | `publico` |
| **qnb-admin** | `admin.qnbangola.com` | `admin` |

**Stack:** Laravel 12 + PHP 8.2+ + MySQL + Blade (sem frontend framework, sem Vite)

---

## Pré-requisitos no Servidor

| Item | Versão/Obrigatório |
|------|-------------------|
| PHP | 8.2+ (8.3 recomendado) |
| MySQL | 8.0+ |
| Composer | 2.x |
| Extensões PHP | `pdo_mysql`, `mbstring`, `openssl`, `ctype`, `json`, `gd`, `fileinfo`, `zip`, `intl` |
| `proc_open` | Pode estar desativado em hosting partilhada |
| HTTPS | Obrigatório (Let's Encrypt / AutoSSL) |

---

## ERROS COMUNS E COMO EVITÁLOS

### ERRO 1: `proc_open` disabled — composer install no servidor falha

**Sintoma:** `proc_open() has been disabled for security reasons` ao correr `composer install` no servidor.

**Causa:** Hosting partilhada bloqueia `proc_open`.

**Solução:**
1. Correr `composer install --no-dev --optimize-autoloader` **LOCALMENTE**
2. Committar a pasta `vendor/` no git
3. No `.gitignore`, **REMOVER** a linha `/vendor`
4. Push para o servidor — o vendor vai com o código

```bash
# LOCALMENTE:
composer install --no-dev --optimize-autoloader
git add vendor/
git commit -m "feat: adicionar vendor/ para deploy"
# Remover /vendor do .gitignore
git push
```

> **NOTA:** Quando o vendor já estiver no servidor, pode voltar a adicionar `/vendor` ao `.gitignore` para não enviar updates desnecessários. O vendor só precisa de ser atualizado quando as dependências mudarem.

---

### ERRO 2: `Method Attachment::from does not exist` (Laravel 12)

**Sintoma:** `BadMethodCallException: Method Illuminate\Mail\Mailables\Attachment::from does not exist`

**Causa:** Laravel 12 mudou a API de attachments. `Attachment::from()` já não existe.

**Solução:** Usar `Attachment::fromPath()`:

```php
// ERRADO (Laravel 11 e anteriores):
Attachment::from($caminho)->as('file.pdf')->withMime('application/pdf');

// CORRETO (Laravel 12):
Attachment::fromPath($caminho)->as('file.pdf')->withMime('application/pdf');
```

**Ficheiros afetados:** Todos os Mailables que enviam PDFs:
- `app/Mail/FaturaEmitidaMail.php`
- `app/Mail/PagamentoConfirmado.php`
- `app/Mail/PagamentoConfirmadoMail.php`

---

### ERRO 3: `535 Incorrect authentication data` (SMTP)

**Sintoma:** `Failed to authenticate on SMTP server` com código 535.

**Causa:** Password SMTP incorreta ou caracteres especiais mal escapingados no `.env`.

**Solução:**
1. Verificar credenciais SMTP no cPanel > Email Accounts
2. Passwords com caracteres especiais (`&*?!)`) devem estar **entre aspas duplas** no `.env`:

```env
MAIL_USERNAME="imobiliara@qnbangola.ao"
MAIL_PASSWORD="&*sD9?su7I!"
```

3. Depois de alterar o `.env` no servidor:
```bash
php artisan config:cache
```

---

### ERRO 4: `Mail::queue()` não funciona — emails nunca chegam

**Sintoma:** Emails são criados na tabela `jobs` mas nunca são enviados.

**Causa:** Hosting partilhada **não tem queue worker** a correr. `Mail::queue()` coloca emails na fila, mas ninguém os processa.

**Solução:** Mudar `Mail::queue()` para `Mail::send()` em **TODO** o projeto:

```php
// ERRADO (fica preso na fila):
Mail::to($email)->queue(new MeuMailable($data));

// CORRETO (envia imediatamente):
Mail::to($email)->send(new MeuMailable($data));
```

**Ficheiros para alterar:**
- `app/Services/PagamentoService.php`
- `app/Services/ClienteAuthService.php`
- `app/Actions/Faturas/AprovarPagamentoAction.php`
- `app/Actions/Faturas/EmitirFaturaAction.php`
- `app/Actions/Faturas/SubmeterComprovativoAction.php`
- `app/Console/Commands/ExpirarFaturas.php`

**NOTA:** Se quiser manter `queue`, precisa de configurar um queue worker:
```bash
php artisan queue:work --queue=publico --stop-when-empty --tries=3 --max-time=55
```

---

### ERRO 5: Erro 419 — CSRF token / Sessão

**Sintoma:** Página de login dá 419 (Page Expired).

**Causa:** `SESSION_DOMAIN` não coincide com o domínio do site.

**Exemplo real:**
```env
# ERRADO — domínio não existe:
SESSION_DOMAIN=qnbangola.com

# CORRETO — domínio exato do site:
SESSION_DOMAIN=imobiliaria.qnbangola.ao
```

**Regra:** `SESSION_DOMAIN` deve ser **exatamente** o domínio do site (sem `https://`, sem `/`).

---

### ERRO 6: CSS/JS não carrega — página sem estilo

**Sintoma:** HTML carrega mas CSS/JS não aplicam.

**Causas possíveis:**
1. **Browser cache** — resolver com Ctrl+Shift+R (hard refresh) ou testar em janela incógnita
2. **Document Root errada** — apontar para `/public`, não para a raiz do projeto
3. **`.htaccess` em falta** — verificar que `public/.htaccess` existe no servidor
4. **Permissões** — `storage/` e `bootstrap/cache/` com `chmod -R 775`

**Verificar no servidor:**
```bash
ls -la public/.htaccess
ls -la storage/
ls -la bootstrap/cache/
```

---

### ERRO 7: Views em branco / erro de cache

**Sintoma:** `InvalidArgumentException: View [xxx] not found`

**Causa:** Pasta `storage/framework/views/` não existe ou sem permissões.

**Solução:**
```bash
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/logs
chmod -R 775 storage/framework
chmod -R 775 storage/logs
```

---

## GUIA DE DEPLOY PASSO A PASSO

### 1. Preparar o código LOCALMENTE

```bash
# Instalar dependências (sem dev)
composer install --no-dev --optimize-autoloader

# Limpar caches
php artisan config:clear && php artisan route:clear && php artisan view:clear

# Correr testes (devem passar todos)
php artisan test
```

### 2. Configurar o .env no servidor

Copiar `.env.production` para `.env` no servidor e ajustar:

```env
APP_NAME="QNB Imobiliária"
APP_ENV=production
APP_KEY=                          # Gerar com: php artisan key:generate --force
APP_DEBUG=false
APP_URL=https://SUBDOMINIO.DOMINIO.ao

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=SEU_BANCO
DB_USERNAME=SEU_USER
DB_PASSWORD="SUA_PASSWORD"

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=SUBDOMINIO.DOMINIO.ao    # ← EXATAMENTE o domínio do site
SESSION_SECURE_COOKIE=true

QUEUE_CONNECTION=database
CACHE_STORE=database
DB_QUEUE=publico                          # ESTA APP usa "publico"

MAIL_MAILER=smtp
MAIL_HOST=mail.DOMINIO.ao
MAIL_PORT=465
MAIL_USERNAME="email@DOMINIO.ao"
MAIL_PASSWORD="PASSWORD_SEGURA"
MAIL_ENCRYPTION=ssl
MAIL_SCHEME=null
MAIL_FROM_ADDRESS="email@DOMINIO.ao"
MAIL_FROM_NAME="${APP_NAME}"

LOG_CHANNEL=daily
LOG_LEVEL=error
```

> **IMPORTANTE:** Se houver outro app (qnb-admin), o `DB_QUEUE` deve ser `admin`, não `publico`.

### 3. Criar a BD e tabelas

```bash
# Criar BD no cPanel > MySQL Databases
# Criar user e grant ALL PRIVILEGES na BD

# Correr migrations
php artisan migrate --force

# Seed APENAS planos e amenidades (NUNCA db:seed completo em produção!)
php artisan db:seed --force --class=PlanosSeeder
php artisan db:seed --force --class=AmenidadesSeeder
```

### 4. Configurar storage e permissões

```bash
# Criar link simbólico
php artisan storage:link

# Criar pastas necessárias
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/logs

# Permissões
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 5. Configurar o domínio (cPanel)

- **Document Root:** Apontar para `/public` (ex: `public/`)
- **SSL:** Ativar AutoSSL ou Let's Encrypt
- **Redirect:** Forçar HTTP → HTTPS

### 6. Caches de produção

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Verificar o site

```bash
# Health check
curl -s https://SUBDOMINIO.DOMINIO.ao/health

# Login API
curl -X POST https://SUBDOMINIO.DOMINIO.ao/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@qnbangola.com","password":"demo1234"}'
```

### 8. Fila e Cron (opcional, mas recomendado)

**Se usar Mail::queue (não recomendado em hosting partilhada):**
```bash
# Cron job para queue worker
* * * * * cd /caminho/projeto && php artisan queue:work --stop-when-empty --tries=3 --max-time=55 --queue=publico
```

**Scheduler (para comandos agendados):**
```bash
* * * * * cd /caminho/projeto && php artisan schedule:run
```

---

## CHECKLIST DE DEPLOY

| # | Item | Comando/Verificação |
|---|------|-------------------|
| 1 | PHP 8.2+ | `php -v` |
| 2 | Extensões PHP | `php -m | grep -E "pdo_mysql\|mbstring\|openssl\|gd\|zip\|intl"` |
| 3 | `.env` configurado | `cat .env | head -20` |
| 4 | APP_KEY definido | `php artisan tinker --execute="echo config('app.key')"` |
| 5 | DB ligado | `php artisan db:show` |
| 6 | Migrations correram | `php artisan migrate:status` |
| 7 | `storage/` writable | `ls -la storage/` |
| 8 | `bootstrap/cache/` writable | `ls -la bootstrap/cache/` |
| 9 | `storage:link` existe | `ls -la public/storage` |
| 10 | Document Root aponta para `public/` | cPanel > Domains > Document Root |
| 11 | SSL ativo | Abrir `https://...` sem erro |
| 12 | Site carrega | Abrir `https://...` e ver CSS/JS |
| 13 | Login funciona | Testar `demo@qnbangola.com` / `demo1234` |
| 14 | Emails enviam | Testar registo ou recuperação de password |
| 15 | Health check OK | `curl https://.../health` |
| 16 | APP_DEBUG=false | `grep APP_DEBUG .env` |
| 17 | Session funciona | Login não dá 419 |
| 18 | SESSION_DOMAIN correto | Coincide com o domínio do site |

---

## CREDENCIAIS DE TESTE

| App | Email | Password |
|-----|-------|----------|
| Painel anunciante | `demo@qnbangola.com` | `demo1234` |
| Cliente | `cliente@qnbangola.com` | `cliente1234` |
| Admin (qnb-admin) | `admin@qnbangola.com` | `admin1234` |

---

## REFERÊNCIA RÁPIDA

### Pastas essenciais no servidor
```
/home/usuario/dominio.ao/          ← raiz do projeto (FORA de public_html)
├── app/
├── bootstrap/cache/               ← chmod 775
├── config/
├── database/
├── public/                        ← Document Root do domínio
│   ├── .htaccess
│   ├── index.php
│   └── assets/
├── resources/
├── routes/
├── storage/                       ← chmod 775
│   ├── app/public/
│   ├── framework/views/
│   ├── framework/cache/
│   ├── framework/sessions/
│   └── logs/
├── vendor/
└── .env
```

### Comandos úteis
```bash
# Ver logs
tail -f storage/logs/laravel.log

# Limpar tudo e recachear
php artisan config:clear && php artisan route:clear && php artisan view:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Forçar logout de todos (se necessário)
php artisan session:flush

# Verificar estado da BD
php artisan db:show
php artisan migrate:status

# Testar SMTP
php artisan tinker --execute="Illuminate\Support\Facades\Mail::raw('Teste', function(\$m){\$m->to('email@teste.com')->subject('Teste');});"
```

---

## ERRO: `config/view.php` em falta

**Sintoma:** `InvalidArgumentException: View [xxx] not found` ou erro de cache de views.

**Causa:** O ficheiro `config/view.php` não estava no repositório (esquecido).

**Solução:** Criar `config/view.php` com o conteúdo padrão do Laravel:

```php
<?php

return [
    'paths' => [
        resource_path('views'),
    ],
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),
];
```

Commit + push + no servidor: `php artisan view:cache`

---

> **Nota final:** Este guia foi escrito com base em erros reais encontrados durante o deploy do qnb-imobiliaria. Qualquer pessoa ou IA que for fazer deploy de uma das apps QNB deve seguir estes passos para evitar os mesmos problemas.
