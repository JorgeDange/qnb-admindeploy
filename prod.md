# Deploy QNB-Admin (CRM) — Histórico

## Data: 22/09/2026

---

## Correções Feitas

### 1. Laravel 12 — Attachment::from → Attachment::fromPath
- **Ficheiro:** `app/Mail/FaturaEmitidaMail.php:48`
- **Problema:** `Attachment::from()` não existe no Laravel 12
- **Correção:** `Attachment::fromPath()`

### 2. robots.txt — Bloquear Indexação
- **Ficheiro:** `public/robots.txt`
- **Antes:** `Disallow:` (permite crawling)
- **Depois:** `Disallow: /` (bloqueia tudo)

### 3. Headers de Segurança — .htaccess
- **Ficheiro:** `public/.htaccess`
- **Adicionados:**
  - `X-Robots-Tag: noindex, nofollow`
  - `X-Frame-Options: DENY`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`

### 4. config/view.php — Criado
- **Ficheiro:** `config/view.php`
- **Problema:** Ficheiro em falta (causava erro de views)
- **Conteúdo:** paths + compiled path para `storage/framework/views`

### 5. .env.example — Corrigido para Admin
- **Ficheiro:** `.env.example`
- **Correções:**
  - `APP_NAME` → "QNB Admin (CRM)"
  - `APP_URL` → https://imobiliariaequipa.qnbangola.ao
  - `SESSION_DOMAIN` → imobiliariaequipa.qnbangola.ao
  - `DB_QUEUE` → admin (não "publico")
  - `FRONTEND_URL` → https://imobiliaria.qnbangola.ao
  - `ADMIN_URL` → https://imobiliariaequipa.qnbangola.ao
  - Credenciais removidas (placeholders)

### 6. Foto de Perfil Admin
- **Ficheiro:** `resources/views/admin/layouts/admin.blade.php:44`
- **Antes:** `team-1.jpg`
- **Depois:** `logo-c.svg`

---

## Configuração Final (.env)

| Variável | Valor |
|----------|-------|
| APP_NAME | QNB Admin (CRM) |
| APP_URL | https://imobiliariaequipa.qnbangola.ao |
| SESSION_DOMAIN | imobiliariaequipa.qnbangola.ao |
| DB_QUEUE | admin |
| FRONTEND_URL | https://imobiliaria.qnbangola.ao |
| ADMIN_URL | https://imobiliariaequipa.qnbangola.ao |
| DB_DATABASE | qnbangolaco_qnb_imobiliaria (mesma da imobiliaria) |
| DB_USERNAME | qnbangolaco_jorgedange |
| MAIL_HOST | mail.qnbangola.ao |
| MAIL_PORT | 465 |

---

## Comandos de Deploy

```bash
# Local
composer install --no-dev --optimize-autoloader

# Servidor
cp .env.example .env
php artisan key:generate --force
# Editar .env: DB_PASSWORD, MAIL_PASSWORD
php artisan storage:link
mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs
chmod -R 775 storage/ bootstrap/cache/
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Testes

- **34 passed / 4 skipped** (PHPUnit 11.5)
- Todos os testes passam após correções

---

## Regras Importantes

1. **NUNCA correr `php artisan migrate`** — schema gerido pela imobiliaria
2. **APP_KEY deve ser DIFERENTE** da da imobiliaria
3. **DB_QUEUE = admin** (não "publico")
4. **Filas:** admin (esta app) / publico (imobiliaria)
