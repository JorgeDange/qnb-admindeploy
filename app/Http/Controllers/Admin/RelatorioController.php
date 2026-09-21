<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Imobiliaria;
use App\Models\Imovel;
use App\Models\Mensagem;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function index()
    {
        $estatisticas = [
            'imoveis_por_estado' => Imovel::select('estado', DB::raw('count(*) as total'))->groupBy('estado')->get(),
            'imoveis_por_tipo' => Imovel::select('tipo', DB::raw('count(*) as total'))->groupBy('tipo')->get(),
            'imoveis_por_provincia' => Imovel::select('provincia', DB::raw('count(*) as total'))->groupBy('provincia')->orderBy('total', 'desc')->take(10)->get(),
            'imobiliarias_por_estado' => Imobiliaria::select('estado', DB::raw('count(*) as total'))->groupBy('estado')->get(),
            'mensagens_por_mes' => $this->mensagensPorMes(),
        ];

        return view('admin.relatorios.index', compact('estatisticas'));
    }

    public function exportarImoveis(Request $request)
    {
        $query = Imovel::with('imobiliaria');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }

        ActivityLogService::log('exportar_imoveis', null, [
            'filtros' => $request->only(['estado', 'provincia']),
        ]);

        $filename = 'imoveis_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Referência', 'Título', 'Tipo', 'Preço', 'Moeda', 'Estado', 'Província', 'Município', 'Imobiliária', 'Criado em']);
            $query->chunk(500, function ($imoveis) use ($out) {
                foreach ($imoveis as $i) {
                    fputcsv($out, [
                        $i->id, $i->referencia, $i->titulo, $i->tipo,
                        $i->preco, $i->moeda, $i->estado, $i->provincia, $i->municipio,
                        $i->imobiliaria->nome ?? '—', $i->created_at->format('d/m/Y H:i'),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportarImobiliarias(Request $request)
    {
        $query = Imobiliaria::withCount('imoveis');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        ActivityLogService::log('exportar_imobiliarias', null, [
            'filtros' => $request->only(['estado']),
        ]);

        $filename = 'imobiliarias_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Nome', 'NIF', 'Email', 'Telefone', 'Província', 'Município', 'Estado', 'Imóveis', 'Criado em']);
            $query->chunk(500, function ($imobiliarias) use ($out) {
                foreach ($imobiliarias as $im) {
                    fputcsv($out, [
                        $im->id, $im->nome, $im->nif, $im->email, $im->telefone,
                        $im->provincia, $im->municipio, $im->estado,
                        $im->imoveis_count, $im->created_at->format('d/m/Y H:i'),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function complianceSessoes()
    {
        // A tabela sessions não tem coluna user_type; identifica-se admins por user_id
        // e considera-se ativa a sessão com atividade nos últimos 10 minutos.
        $sessoes = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(10)->getTimestamp())
            ->orderByDesc('last_activity')
            ->paginate(30);

        return view('admin.compliance.sessoes', compact('sessoes'));
    }

    /**
     * Últimos 12 meses (incl. vazios) — substitui DATE_FORMAT (MySQL-only)
     * por agrupamento em PHP, permitindo correr relatórios em SQLite nos testes.
     */
    private function mensagensPorMes()
    {
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $meses[now()->subMonths($i)->format('Y-m')] = 0;
        }
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

        // O view itera objetos com ->mes e ->total; devolve coleção de objetos
        return collect($meses)->map(fn ($total, $mes) => (object) ['mes' => $mes, 'total' => $total])->values();
    }
}
