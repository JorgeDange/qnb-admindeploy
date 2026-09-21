<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendImovelAprovadoEmail;
use App\Models\Imovel;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImovelController extends Controller
{
    public function index(Request $request)
    {
        $query = Imovel::with('imobiliaria');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('titulo', 'like', "%{$busca}%")
                  ->orWhere('referencia', 'like', "%{$busca}%");
            });
        }

        $imoveis = $query->latest()->paginate(15);
        return view('admin.imoveis.index', compact('imoveis'));
    }

    public function show(Imovel $imovel)
    {
        $imovel->load('imobiliaria', 'fotos', 'amenidades', 'canais', 'mensagens');
        return view('admin.imoveis.show', compact('imovel'));
    }

    public function aprovar(Imovel $imovel)
    {
        $imovel->update(['estado' => 'aprovado']);
        SendImovelAprovadoEmail::dispatch($imovel, 'aprovado');
        return redirect()->back()->with('success', 'Imóvel aprovado.');
    }

    public function rejeitar(Request $request, Imovel $imovel)
    {
        $imovel->update(['estado' => 'rejeitado']);
        SendImovelAprovadoEmail::dispatch($imovel, 'rejeitado');
        return redirect()->back()->with('success', 'Imóvel rejeitado.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:imoveis,id',
            'acao' => 'required|in:aprovar,rejeitar,suspender,apagar',
        ]);

        $ids = $request->ids;
        $sucesso = 0;
        $falha = 0;

        DB::transaction(function () use ($ids, $request, &$sucesso, &$falha) {
            foreach (Imovel::whereIn('id', $ids)->get() as $imovel) {
                try {
                    match ($request->acao) {
                        'aprovar' => $imovel->update(['estado' => 'aprovado']),
                        'rejeitar' => $imovel->update(['estado' => 'rejeitado']),
                        'suspender' => $imovel->update(['estado' => 'suspenso']),
                        'apagar' => $imovel->delete(),
                    };
                    ActivityLogService::log("bulk_{$request->acao}_imovel", $imovel);
                    $sucesso++;
                } catch (\Throwable $e) {
                    $falha++;
                }
            }
        });

        return back()->with('success', "{$sucesso} processados, {$falha} falharam.");
    }
}
