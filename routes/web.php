<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ConteudoController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmpresaConfigController;
use App\Http\Controllers\Admin\FaturaController as AdminFaturaController;
use App\Http\Controllers\Admin\ImobiliariaController as AdminImobiliariaController;
use App\Http\Controllers\Admin\ImovelController as AdminImovelController;
use App\Http\Controllers\Admin\LogController as AdminLogController;
use App\Http\Controllers\Admin\MensagemController as AdminMensagemController;
use App\Http\Controllers\Admin\ModeracaoController;
use App\Http\Controllers\Admin\NotificacaoController as AdminNotificacaoController;
use App\Http\Controllers\Admin\PagamentoController as AdminPagamentoController;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Admin\PlanoController as AdminPlanoController;
use App\Http\Controllers\Admin\RelatorioController as AdminRelatorioController;
use App\Http\Controllers\Admin\SubscricaoController as AdminSubscricaoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Raiz do CRM: identifica a sessão e leva o utilizador a uma rota válida
Route::get('/', function () {
    return Auth::guard('admin')->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
})->name('home');

// Qualquer URL desconhecida (ex.: /admin/dashboard) → rota válida em vez de 404
Route::fallback(function () {
    return Auth::guard('admin')->check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'loginPost'])->name('login.post')->middleware('throttle:admin-login');

    Route::middleware('admin.auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

        // Imobiliárias
        Route::middleware('admin.role:super_admin,comercial')->group(function () {
            Route::get('/imobiliarias', [AdminImobiliariaController::class, 'index'])->name('imobiliarias');
            Route::get('/imobiliarias/{imobiliaria}', [AdminImobiliariaController::class, 'show'])->name('imobiliarias.show');
            Route::post('/imobiliarias/{imobiliaria}/aprovar', [AdminImobiliariaController::class, 'aprovar'])->name('imobiliarias.aprovar');
            Route::post('/imobiliarias/{imobiliaria}/suspender', [AdminImobiliariaController::class, 'suspender'])->name('imobiliarias.suspender');
            Route::post('/imobiliarias/{imobiliaria}/reativar', [AdminImobiliariaController::class, 'reativar'])->name('imobiliarias.reativar');
            Route::post('/imobiliarias/{imobiliaria}/bloquear', [AdminImobiliariaController::class, 'bloquear'])->name('imobiliarias.bloquear');
        });

        // Imóveis
        Route::middleware('admin.role:super_admin,comercial')->group(function () {
            Route::get('/imoveis', [AdminImovelController::class, 'index'])->name('imoveis');
            Route::get('/imoveis/{imovel}', [AdminImovelController::class, 'show'])->name('imoveis.show');
            Route::post('/imoveis/{imovel}/aprovar', [AdminImovelController::class, 'aprovar'])->name('imoveis.aprovar');
            Route::post('/imoveis/{imovel}/rejeitar', [AdminImovelController::class, 'rejeitar'])->name('imoveis.rejeitar');
        });

        // Pedidos de ativação
        Route::middleware('admin.role:super_admin,comercial')->group(function () {
            Route::get('/pedidos', [AdminPedidoController::class, 'index'])->name('pedidos');
            Route::get('/pedidos/{pedido}', [AdminPedidoController::class, 'show'])->name('pedidos.show');
            Route::post('/pedidos/{pedido}/estado', [AdminPedidoController::class, 'atualizarEstado'])->name('pedidos.estado');
            Route::post('/pedidos/{pedido}/liberar-plano', [AdminPedidoController::class, 'liberarPlano'])->name('pedidos.liberar');
        });

        // Planos
        Route::middleware('admin.role:super_admin')->group(function () {
            Route::get('/planos', [AdminPlanoController::class, 'index'])->name('planos');
            Route::get('/planos/novo', [AdminPlanoController::class, 'form'])->name('planos.novo');
            Route::post('/planos', [AdminPlanoController::class, 'salvar'])->name('planos.salvar');
            Route::get('/planos/{plano}/editar', [AdminPlanoController::class, 'form'])->name('planos.editar');
            Route::put('/planos/{plano}', [AdminPlanoController::class, 'salvar'])->name('planos.atualizar');
            Route::delete('/planos/{plano}', [AdminPlanoController::class, 'apagar'])->name('planos.apagar');
            Route::post('/planos/{plano}/toggle', [AdminPlanoController::class, 'toggle'])->name('planos.toggle');
        });

        // Subscrições
        Route::middleware('admin.role:super_admin,comercial')->group(function () {
            Route::get('/subscricoes', [AdminSubscricaoController::class, 'index'])->name('subscricoes');
            Route::get('/subscricoes/nova', [AdminSubscricaoController::class, 'form'])->name('subscricoes.nova');
            Route::post('/subscricoes', [AdminSubscricaoController::class, 'salvar'])->name('subscricoes.salvar');
            Route::get('/subscricoes/{subscricao}/editar', [AdminSubscricaoController::class, 'form'])->name('subscricoes.editar');
            Route::put('/subscricoes/{subscricao}', [AdminSubscricaoController::class, 'salvar'])->name('subscricoes.atualizar');
            Route::post('/subscricoes/{subscricao}/renovar', [AdminSubscricaoController::class, 'renovar'])->name('subscricoes.renovar');
            Route::post('/subscricoes/{subscricao}/cancelar', [AdminSubscricaoController::class, 'cancelar'])->name('subscricoes.cancelar');
            Route::delete('/subscricoes/{subscricao}', [AdminSubscricaoController::class, 'apagar'])->name('subscricoes.apagar');
        });

        // Administradores
        Route::middleware('admin.role:super_admin')->group(function () {
            Route::get('/admins', [AdminUserController::class, 'index'])->name('admins');
            Route::get('/admins/novo', [AdminUserController::class, 'form'])->name('admins.novo');
            Route::post('/admins', [AdminUserController::class, 'salvar'])->name('admins.salvar');
            Route::get('/admins/{admin}/editar', [AdminUserController::class, 'form'])->name('admins.editar');
            Route::put('/admins/{admin}', [AdminUserController::class, 'salvar'])->name('admins.atualizar');
            Route::delete('/admins/{admin}', [AdminUserController::class, 'apagar'])->name('admins.apagar');
            Route::post('/admins/{admin}/toggle', [AdminUserController::class, 'toggle'])->name('admins.toggle');
        });

        // Logs de Atividade
        Route::middleware('admin.role:super_admin')->group(function () {
            Route::get('/logs', [AdminLogController::class, 'index'])->name('logs');
            Route::get('/logs/{log}', [AdminLogController::class, 'show'])->name('logs.show');
        });

        // Notificações
        Route::get('/notificacoes', [AdminNotificacaoController::class, 'index'])->name('notificacoes');
        Route::post('/notificacoes/{notificacao}/ler', [AdminNotificacaoController::class, 'ler'])->name('notificacoes.ler');
        Route::post('/notificacoes/ler-todas', [AdminNotificacaoController::class, 'lerTodas'])->name('notificacoes.ler-todas');
        Route::get('/notificacoes/contagem', [AdminNotificacaoController::class, 'contagem'])->name('notificacoes.contagem');

        // Exportar Dados
        Route::middleware('admin.role:super_admin,comercial')->group(function () {
            Route::get('/exportar/imoveis', [AdminRelatorioController::class, 'exportarImoveis'])->name('exportar.imoveis');
            Route::get('/exportar/imobiliarias', [AdminRelatorioController::class, 'exportarImobiliarias'])->name('exportar.imobiliarias');
        });

        // Mensagens Diretas
        Route::middleware('admin.role:super_admin,comercial')->group(function () {
            Route::get('/mensagens', [AdminMensagemController::class, 'index'])->name('mensagens');
            Route::get('/mensagens/nova', [AdminMensagemController::class, 'form'])->name('mensagens.nova');
            Route::post('/mensagens', [AdminMensagemController::class, 'enviar'])->name('mensagens.enviar');
            Route::get('/mensagens/{thread}', [AdminMensagemController::class, 'thread'])->name('mensagens.thread');
            Route::post('/mensagens/{thread}/responder', [AdminMensagemController::class, 'responder'])->name('mensagens.responder');
            Route::post('/mensagens/{thread}/fechar', [AdminMensagemController::class, 'fechar'])->name('mensagens.fechar');
        });

        // Ações em Massa
        Route::middleware('admin.role:super_admin,comercial')->group(function () {
            Route::post('/bulk/imoveis', [AdminImovelController::class, 'bulk'])->name('bulk.imoveis');
            Route::post('/bulk/imobiliarias', [AdminImobiliariaController::class, 'bulk'])->name('bulk.imobiliarias');
        });

        // Pagamentos e Faturação
        Route::middleware('admin.role:super_admin,comercial')->group(function () {
            Route::get('/pagamentos', [AdminPagamentoController::class, 'index'])->name('pagamentos');
            Route::get('/pagamentos/{pagamento}', [AdminPagamentoController::class, 'show'])->name('pagamentos.show');
            Route::get('/faturas', [AdminFaturaController::class, 'index'])->name('faturas');
            Route::get('/faturas/{fatura}', [AdminFaturaController::class, 'show'])->name('faturas.show');
            Route::get('/faturas/{fatura}/download', [AdminFaturaController::class, 'download'])->name('faturas.download');
            Route::get('/faturas/{fatura}/recibo', [AdminFaturaController::class, 'downloadRecibo'])->name('faturas.recibo');
            Route::post('/faturas/{fatura}/reenviar', [AdminFaturaController::class, 'reenviar'])->name('faturas.reenviar');
            Route::post('/faturas/{fatura}/aprovar', [AdminFaturaController::class, 'aprovar'])->name('faturas.aprovar');
            Route::post('/faturas/{fatura}/rejeitar', [AdminFaturaController::class, 'rejeitar'])->name('faturas.rejeitar');
            Route::post('/faturas/{fatura}/cancelar', [AdminFaturaController::class, 'cancelar'])->name('faturas.cancelar');
        });

        // Configurações da Empresa
        Route::middleware('admin.role:super_admin')->group(function () {
            Route::get('/empresa-config', [\App\Http\Controllers\Admin\EmpresaConfigController::class, 'edit'])->name('empresa-config');
            Route::put('/empresa-config', [\App\Http\Controllers\Admin\EmpresaConfigController::class, 'update'])->name('empresa-config.update');
            Route::post('/empresa-config/logo', [\App\Http\Controllers\Admin\EmpresaConfigController::class, 'uploadLogo'])->name('empresa-config.logo');
        });

        // Conteúdo
        Route::middleware('admin.role:super_admin,moderador')->group(function () {
            Route::get('/settings', [ConteudoController::class, 'settings'])->name('settings');
            Route::post('/settings', [ConteudoController::class, 'settingsSalvar'])->name('settings.salvar');
            Route::get('/depoimentos', [ConteudoController::class, 'depoimentos'])->name('depoimentos');
            Route::post('/depoimentos', [ConteudoController::class, 'depoimentoSalvar'])->name('depoimentos.salvar');
            Route::delete('/depoimentos/{depoimento}', [ConteudoController::class, 'depoimentoApagar'])->name('depoimentos.apagar');
            Route::get('/parceiros', [ConteudoController::class, 'parceiros'])->name('parceiros');
            Route::post('/parceiros', [ConteudoController::class, 'parceiroSalvar'])->name('parceiros.salvar');
            Route::delete('/parceiros/{parceiro}', [ConteudoController::class, 'parceiroApagar'])->name('parceiros.apagar');
        });

        // Relatórios
        Route::middleware('admin.role:super_admin,comercial')->group(function () {
            Route::get('/relatorios', [AdminRelatorioController::class, 'index'])->name('relatorios');
        });

        // Denúncias
        Route::middleware('admin.role:super_admin,moderador')->group(function () {
            Route::get('/denuncias', [ModeracaoController::class, 'denuncias'])->name('denuncias');
            Route::get('/denuncias/{denuncia}', [ModeracaoController::class, 'denunciaShow'])->name('denuncias.show');
            Route::post('/denuncias/{denuncia}/resolver', [ModeracaoController::class, 'denunciaResolver'])->name('denuncias.resolver');
            Route::post('/denuncias/{denuncia}/arquivar', [ModeracaoController::class, 'denunciaArquivar'])->name('denuncias.arquivar');
        });

        // Avaliações
        Route::middleware('admin.role:super_admin,moderador')->group(function () {
            Route::get('/avaliacoes', [ModeracaoController::class, 'avaliacoes'])->name('avaliacoes');
            Route::post('/avaliacoes/{avaliacao}/aprovar', [ModeracaoController::class, 'avaliacaoAprovar'])->name('avaliacoes.aprovar');
            Route::post('/avaliacoes/{avaliacao}/rejeitar', [ModeracaoController::class, 'avaliacaoRejeitar'])->name('avaliacoes.rejeitar');
            Route::delete('/avaliacoes/{avaliacao}', [ModeracaoController::class, 'avaliacaoApagar'])->name('avaliacoes.apagar');
        });

        // Visitas
        Route::middleware('admin.role:super_admin,comercial,moderador')->group(function () {
            Route::get('/visitas', [ModeracaoController::class, 'visitas'])->name('visitas');
            Route::post('/visitas/{visita}/estado', [ModeracaoController::class, 'visitaEstado'])->name('visitas.estado');
        });

        // FAQ
        Route::middleware('admin.role:super_admin,moderador')->group(function () {
            Route::get('/faq', [ConteudoController::class, 'faq'])->name('faq');
            Route::post('/faq', [ConteudoController::class, 'faqSalvar'])->name('faq.salvar');
            Route::post('/faq/{faq}/toggle', [ConteudoController::class, 'faqToggle'])->name('faq.toggle');
            Route::delete('/faq/{faq}', [ConteudoController::class, 'faqApagar'])->name('faq.apagar');
        });

        // Compliance
        Route::middleware('admin.role:super_admin')->group(function () {
            Route::get('/compliance/sessoes', [AdminRelatorioController::class, 'complianceSessoes'])->name('compliance.sessoes');
        });
    });
});

// Health Check (público) — diagnóstico de PHP/sessão sem expor segredos
Route::get('/health', function () {
    $checks = [
        'database' => fn () => \Illuminate\Support\Facades\DB::connection()->getPdo() ? 'ok' : 'erro',
        'cache' => fn () => \Illuminate\Support\Facades\Cache::store('file')->put('health', 1) ? 'ok' : 'erro',
        'queue' => fn () => class_exists(\Illuminate\Queue\QueueManager::class) ? 'ok' : 'erro',
        'storage' => fn () => is_writable(storage_path()) ? 'ok' : 'erro',
        'sessions_dir' => fn () => is_writable(storage_path('framework/sessions')) ? 'ok' : 'erro',
        'sessions_table' => function () {
            try {
                return \Illuminate\Support\Facades\Schema::hasTable(config('session.table')) ? 'ok' : 'em falta';
            } catch (\Throwable) {
                return 'erro';
            }
        },
        'php_ok' => version_compare(PHP_VERSION, '8.2.0', '>=') ? 'ok' : ('ANTIGO ' . PHP_VERSION),
    ];

    $result = [];
    $allOk = true;
    foreach ($checks as $name => $check) {
        try {
            $result[$name] = $check();
            if ($result[$name] !== 'ok') {
                $allOk = false;
            }
        } catch (\Throwable $e) {
            $result[$name] = 'erro: ' . $e->getMessage();
            $allOk = false;
        }
    }

    return response()->json([
        'status' => $allOk ? 'healthy' : 'degraded',
        'php' => PHP_VERSION,
        'session' => [
            'driver' => config('session.driver'),
            'domain' => config('session.domain'),
            'secure' => config('session.secure'),
            'encrypt' => config('session.encrypt'),
        ],
        'app_url' => config('app.url'),
        'checks' => $result,
        'timestamp' => now()->toIso8601String(),
    ], $allOk ? 200 : 503);
});
