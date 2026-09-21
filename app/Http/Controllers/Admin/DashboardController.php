<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Imobiliaria;
use App\Models\Imovel;
use App\Models\Mensagem;
use App\Models\PedidoAtivacao;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $periodo = request('periodo', 30);
        $inicio = now()->subDays($periodo);
        $inicioAnterior = now()->subDays($periodo * 2);

        $stats = [
            'imobiliarias_pendentes' => Imobiliaria::where('estado', 'pendente')->count(),
            'imoveis_pendentes' => Imovel::where('estado', 'pendente')->count(),
            'pedidos_novos' => PedidoAtivacao::where('estado', 'novo')->count(),
            'total_imobiliarias' => Imobiliaria::count(),
            'total_imoveis' => Imovel::count(),
            'total_mensagens' => Mensagem::count(),
            'mensagens_nao_lidas' => Mensagem::where('lida', false)->count(),
            // KPIs com período
            'imoveis_novos' => Imovel::where('created_at', '>=', $inicio)->count(),
            'imoveis_anterior' => Imovel::whereBetween('created_at', [$inicioAnterior, $inicio])->count(),
            'imobiliarias_novas' => Imobiliaria::where('created_at', '>=', $inicio)->count(),
        ];

        $recentes = [
            'imobiliarias' => Imobiliaria::latest()->take(5)->get(),
            'imoveis' => Imovel::latest()->take(5)->get(),
            'pedidos' => PedidoAtivacao::with('imobiliaria')->latest()->take(5)->get(),
        ];

        // Dados para gráficos — série mensal em PHP (compatível com MySQL e SQLite)
        $chartData = [
            'imoveis_por_estado' => Imovel::select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado'),
            'imobiliarias_por_estado' => Imobiliaria::select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado'),
            'imoveis_por_provincia' => Imovel::select('provincia', DB::raw('count(*) as total'))->groupBy('provincia')->orderBy('total', 'desc')->take(8)->pluck('total', 'provincia'),
            'mensagens_por_mes' => $this->mensagensPorMes(),
            'imobiliarias_por_mes' => $this->imobiliariasPorMes(),
        ];

        return view('admin.dashboard', compact('stats', 'recentes', 'chartData', 'periodo'));
    }

    /**
     * Últimos 12 meses (incl. meses vazios) — substitui DATE_FORMAT (MySQL-only)
     * por agrupamento em PHP, permitindo correr o dashboard em SQLite nos testes.
     */
    private function mensagensPorMes(): array
    {
        $meses = $this->ultimosMeses(12);
        $inicio = now()->subMonths(11)->startOfMonth();

        Mensagem::where('created_at', '>=', $inicio)
            ->select('created_at')
            ->get()
            ->each(function ($m) use (&$meses) {
                $chave = $m->created_at->format('Y-m');
                if (isset($meses[$chave])) {
                    $meses[$chave]++;
                }
            });

        return $meses;
    }

    private function imobiliariasPorMes(): array
    {
        $meses = $this->ultimosMeses(12);
        $inicio = now()->subMonths(11)->startOfMonth();

        Imobiliaria::where('created_at', '>=', $inicio)
            ->select('created_at')
            ->get()
            ->each(function ($i) use (&$meses) {
                $chave = $i->created_at->format('Y-m');
                if (isset($meses[$chave])) {
                    $meses[$chave]++;
                }
            });

        return $meses;
    }

    private function ultimosMeses(int $quantidade): array
    {
        $meses = [];
        for ($i = $quantidade - 1; $i >= 0; $i--) {
            $meses[now()->subMonths($i)->format('Y-m')] = 0;
        }
        return $meses;
    }
}
