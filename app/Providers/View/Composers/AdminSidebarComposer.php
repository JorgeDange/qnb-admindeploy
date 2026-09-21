<?php

namespace App\Providers\View\Composers;

use App\Models\Admin;
use App\Models\Avaliacao;
use App\Models\Denuncia;
use App\Models\Imobiliaria;
use App\Models\ImobiliariaPlano;
use App\Models\Imovel;
use App\Models\Mensagem;
use App\Models\Pagamento;
use App\Models\PedidoAtivacao;
use App\Models\Visita;
use Illuminate\Support\Facades\Cache;

class AdminSidebarComposer
{
    /**
     * Partilha os badges de contagem usados no sidebar do painel admin.
     * Corrige o 500 "Undefined variable $msgNaoLidas" que afetava todas as páginas.
     *
     * As 10 contagens são agrupadas numa única entrada de cache (60s):
     * o sidebar renderiza em todas as páginas admin, pelo que sem cache
     * seriam 10 queries extra por request.
     */
    public function compose($view)
    {
        $contagens = Cache::remember('admin.sidebar.contagens', 60, function () {
            return [
                'impPend' => Imobiliaria::where('estado', 'pendente')->count(),
                'imoPend' => Imovel::where('estado', 'pendente')->count(),
                'pedNovos' => PedidoAtivacao::where('estado', 'novo')->count(),
                'denPend' => Denuncia::where('estado', 'pendente')->count(),
                'avalPend' => Avaliacao::where('estado', 'pendente')->count(),
                'msgNaoLidas' => Mensagem::where('lida', false)->count(),
                'visHoje' => Visita::whereDate('data_visita', today())
                    ->where('estado', '!=', 'cancelada')
                    ->count(),
                'subAtivas' => ImobiliariaPlano::where('estado', 'ativa')->count(),
                'pagPend' => Pagamento::where('estado', 'pendente')->count(),
                'adminCount' => Admin::where('ativo', true)->count(),
            ];
        });

        $view->with($contagens);
    }
}
