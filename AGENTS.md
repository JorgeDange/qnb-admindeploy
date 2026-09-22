# QNB-Admin — Guia para IA (qnb-admin)

> **Este ficheiro serve como contexto para IA assistente.** Lê-lo antes de qualquer tarefa neste projeto.

---

## 1. O Que É Este Projeto

**QNB-Admin** — CRM interno (admin/moderador) da plataforma QNB-Imobiliária. Aplicação Laravel 12 separada do site público, partilhando a MESMA BD MySQL por segurança.

| App | Âmbito | Domínio |
|-----|--------|---------|
| **qnb-imobiliaria** (projeto separado) | Site público + Painel do anunciante + Área do cliente | `www.qnbangola.com` |
| **qnb-admin** (este projeto) | CRM Admin/Moderador | `admin.qnbangola.com` |

**Motivação da separação:** isolamento de domínio — os admins não acedem pelo mesmo domínio do site. Regra de ouro: **MESMA BD, DOMÍNIOS DIFERENTES**.

**Stack:** Laravel 12 + PHP 8.2+ + MySQL + Blade + HTML/CSS/JS puro

---

## 2. Estado Atual — PRONTO PARA PRODUÇÃO

- **Testes:** 34 passed / 4 skipped (PHPUnit 11.5)
- **Rotas:** 96 endpoints (17 controllers, todos sob `/admin`)
- **Observers:** NENHUM registado (evita emails duplicados)
- **Migrations:** NUNCA correm nesta app (schema gerido pela imobiliaria)
- **Deploy:** Documentado em `qnb-imobiliaria/DEPLOY.md` §11

---

## 3. Estrutura de Pastas

```
qnb-admin/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Admin/              # 17 controllers do CRM
│   │   └── Middleware/
│   │       ├── AdminAuth.php       # Autenticação admin
│   │       └── EnsureAdminHasRole.php  # Controlo de acesso por role
│   ├── Mail/                       # 6 Mailables
│   ├── Models/                     # 35 models (duplicados da imobiliaria)
│   ├── Policies/
│   │   └── AdminPolicy.php         # Única policy
│   └── Services/                   # 7 services
├── database/
│   └── migrations/                 # 63 migrations (SÓ para testes SQLite)
├── resources/views/
│   ├── admin/                      # 47 views do CRM
│   ├── components/                 # modal-global, flash
│   ├── emails/                     # 7 templates de email
│   └── pdf/                        # fatura, recibo
├── routes/
│   └── web.php                     # 96 rotas (todas sob /admin)
├── tests/                          # 8 ficheiros, 34 testes
├── public/
│   └── assets/                     # CSS/JS copiados da imobiliaria
└── .env.example                    # Config para produção
```

---

## 4. Auth Guard

| Guard | Driver | Modelo | Uso |
|-------|--------|--------|-----|
| `web` | session | `Admin` | Default (renomeado de `admin`) |
| `admin` | session | `Admin` | Login do CRM |

**Password broker:** `admins` (tabela `password_reset_tokens`, 60min)

**Roles:** `super_admin`, `comercial`, `moderador`

---

## 5. Controllers — 17 total

| Controller | Rotas | Roles | Descrição |
|-----------|-------|-------|-----------|
| `AuthController` | 2 | público | Login/logout |
| `DashboardController` | 1 | todas | Dashboard principal |
| `ImobiliariaController` | 8 | super_admin, comercial | Gestão de imobiliárias |
| `ImovelController` | 6 | super_admin, comercial | Gestão de imóveis |
| `PedidoController` | 4 | super_admin, comercial | Pedidos de ativação |
| `SubscricaoController` | 4 | super_admin, comercial | Subscrições |
| `PagamentoController` | 5 | super_admin, comercial | Pagamentos |
| `FaturaController` | 6 | super_admin, comercial | Faturas (listar, ver, download, aprovar, rejeitar, cancelar) |
| `PlanoController` | 5 | super_admin | Planos de subscrição |
| `AdminUserController` | 5 | super_admin | CRUD admins |
| `MensagemController` | 4 | super_admin, comercial | Mensagens |
| `ConteudoController` | 8 | super_admin, moderador | CMS (settings, depoimentos, parceiros, FAQ) |
| `DenunciaController` | 3 | super_admin, moderador | Denúncias |
| `AvaliacaoController` | 3 | super_admin, moderador | Avaliações |
| `VisitaController` | 2 | todas | Visitas |
| `NotificacaoController` | 2 | todas | Notificações |
| `LogController` | 2 | super_admin | Logs de atividade |
| `RelatorioController` | 4 | super_admin, comercial | Relatórios + exportações |
| `EmpresaConfigController` | 1 | super_admin | Configurações empresa |

---

## 6. Middleware

| Alias | Classe | Função |
|-------|--------|--------|
| `admin.auth` | `AdminAuth` | Verifica autenticação do admin |
| `admin.role` | `EnsureAdminHasRole` | Verifica role do admin (super_admin, comercial, moderador) |

Registrados em `bootstrap/app.php`.

---

## 7. Models — 35 total

**Duplicados da qnb-imobiliaria** (mesma BD, models standalone):

| Categoria | Models |
|-----------|--------|
| Core | `Admin`, `Imobiliaria`, `Imovel`, `ImovelFoto`, `Plano`, `ImobiliariaPlano`, `PedidoAtivacao` |
| Cliente | `Cliente`, `ClienteToken`, `ClienteFavorito`, `ClientePesquisa`, `ClienteNotificacao` |
| Financeiro | `Pagamento`, `Fatura`, `FaturaLinha`, `FaturaEmailLog`, `EmpresaConfig` |
| Comunicação | `Mensagem`, `AdminMensagem`, `Notificacao`, `ImobiliariaNotificacao`, `PushToken` |
| Conteúdo | `Amenidade`, `CanalContacto`, `Depoimento`, `Faq`, `Parceiro`, `Setting` |
| Interação | `Visita`, `Avaliacao`, `Denuncia`, `Interacao` |
| Infra | `ActivityLog`, `RelatorioAgendado` |

**⚠️ Models são DUPLICADOS** — cada app tem os seus. Fonte de verdade: BD + migrations (só correm na imobiliaria).

---

## 8. Services — 7 total

| Service | Função |
|---------|--------|
| `ActivityLogService` | Log de auditoria |
| `EmpresaConfigService` | Configurações chave-valor |
| `FaturaService` | Faturas (criação, numeração, estados) |
| `InvoiceService` | Renderização PDF |
| `NotificacaoService` | Notificações |
| `PushNotificationService` | Push notifications |
| `ClienteNotificacaoService` | Notificações do cliente |

---

## 9. Mailables — 6

| Mailable | Função |
|----------|--------|
| `AssinaturaLiberada` | Subscrição ativada |
| `ComprovativoRejeitadoMail` | Comprovativo rejeitado |
| `FaturaCanceladaMail` | Fatura cancelada |
| `FaturaEmitidaMail` | Fatura emitida |
| `ImovelAprovado` | Imóvel aprovado |
| `PagamentoConfirmadoMail` | Pagamento confirmado + recibo |

---

## 10. Views — 49 templates

| Pasta | Count | Descrição |
|-------|-------|-----------|
| `admin/` | 1 | Dashboard |
| `admin/auth/` | 1 | Login |
| `admin/admins/` | 2 | Gestão de admins |
| `admin/imobiliarias/` | 2 | Gestão de imobiliárias |
| `admin/imoveis/` | 2 | Gestão de imóveis |
| `admin/pedidos/` | 2 | Pedidos de ativação |
| `admin/subscricoes/` | 2 | Subscrições |
| `admin/pagamentos/` | 2 | Pagamentos |
| `admin/faturas/` | 2 | Faturas |
| `admin/planos/` | 2 | Planos |
| `admin/mensagens/` | 3 | Mensagens |
| `admin/conteudo/` | 4 | CMS (settings, parceiros, FAQ, depoimentos) |
| `admin/denuncias/` | 2 | Denúncias |
| `admin/avaliacoes/` | 1 | Avaliações |
| `admin/visitas/` | 1 | Visitas |
| `admin/notificacoes/` | 1 | Notificações |
| `admin/logs/` | 2 | Logs de atividade |
| `admin/relatorios/` | 1 | Relatórios |
| `admin/empresa-config/` | 1 | Configurações |
| `admin/compliance/` | 1 | Sessões |
| `components/` | 2 | modal-global, flash |
| `emails/` | 7 | Templates de email |
| `pdf/` | 2 | fatura, recibo |

---

## 11. Migrations — 63 ficheiros

**⚠️ NUNCA correr `php artisan migrate` nesta app!**

Migrations existem SÓ para testes SQLite. Em produção, o schema é gerido exclusivamente pela qnb-imobiliaria.

Tabelas principais (partilhadas com a imobiliaria):
`admins`, `imobiliarias`, `imoveis`, `imovel_fotos`, `planos`, `imobiliaria_plano`, `pagamentos`, `faturas`, `fatura_linhas`, `mensagens`, `admin_mensagens`, `clientes`, `cliente_favoritos`, `visitas`, `avaliacoes`, `denuncias`, `amenidades`, `canais_contacto`, `push_tokens`, `notificacoes`, `activity_logs`

---

## 12. Rotas — 96 endpoints

Todas sob prefixo `/admin` e nome `admin.*`.

**Por role:**
| Role | Rotas | Acesso |
|------|-------|--------|
| `super_admin` | ~40 | Tudo |
| `comercial` | ~30 | Imobiliárias, imóveis, pagamentos, faturas, mensagens |
| `moderador` | ~15 | Conteúdo, denúncias, avaliações |
| público | 2 | Login (`/admin/login`) + Health (`/health`) |

---

## 13. Variáveis de Ambiente (.env)

```bash
APP_NAME="QNB Admin (CRM)"
APP_URL=https://admin.qnbangola.com
FRONTEND_URL=https://www.qnbangola.com    # Links "Ver site"
# ADMIN_URL — não é usado nesta app
DB_QUEUE=admin                            # ← fila desta app (NUNCA mudar)
# DB_* — MESMOS valores da imobiliaria (BD partilhada)
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
```

**⚠️ APP_KEY deve ser DIFERENTE da da imobiliaria!**

---

## 14. Uploads Partilhados

O disco NÃO é partilhado automaticamente. Soluções:

| Hosting | Solução |
|---------|---------|
| Mesma conta cPanel | Symlink: `ln -sfn /home/USER/qnb-app/qnb-imobiliaria/storage/app/public /home/USER/qnb-admin-app/qnb-admin/public/storage` |
| Hostings separados | S3/Cloudflare R2 (recomendado) ou rsync |

---

## 15. Endurecimento Obrigatório (§9 DEPLOY.md)

- [ ] `robots.txt`: `User-agent: *\nDisallow: /`
- [ ] Header `X-Robots-Tag: noindex, nofollow`
- [ ] Basic Auth ou IP whitelist no `.htaccess`
- [ ] HTTPS + `SESSION_SECURE_COOKIE=true`
- [ ] Headers de segurança (X-Frame-Options, X-Content-Type-Options)
- [ ] Nenhum email/página pública revela o domínio do CRM

---

## 16. Credenciais (DEV)

| Área | Login | Senha |
|------|-------|-------|
| CRM Admin | `admin@qnbangola.com` | `admin1234` |

**⚠️ NUNCA usar em produção!**

---

## 17. Comandos Úteis

```bash
# Setup
composer install
cp .env.example .env
php artisan key:generate    # APP_KEY NOVA (≠ da imobiliaria)
# ⚠️ NUNCA correr php artisan migrate!

# Desenvolvimento
php artisan serve --port=8898
php artisan view:clear && php artisan view:cache

# Testes
php artisan test

# Produção
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan queue:work --queue=admin
```

---

## 18. Regras para IA

1. **NUNCA correr `php artisan migrate`** — schema gerido pela imobiliaria
2. **NUNCA registra observers** — evita emails duplicados (2 apps, mesma BD)
3. **Models são DUPLICADOS** — não alterar aqui sem alterar também na imobiliaria
4. **Filas nomeadas:** `admin` (esta app) / `publico` (imobiliaria)
5. **Roles:** verificar `EnsureAdminHasRole` antes de adicionar rotas
6. **Uploads:** verificar partilha de disco antes de implementar features de upload
7. **NÃO criar rotas `api.php`** — o CRM é 100% Blade
8. **Testes:** correr `php artisan test` antes de commit — 34 testes devem passar
9. **Security:** `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, IP whitelist
10. **Cross-app:** usar `FRONTEND_URL` para links ao site, não hardcodar domínio
