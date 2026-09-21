<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Imobiliaria;
use App\Jobs\SendImovelAprovadoEmail;
use App\Models\Imovel;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ImobiliariaController extends Controller
{
    public function index(Request $request)
    {
        $query = Imobiliaria::query();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhere('nif', 'like', "%{$busca}%");
            });
        }

        $imobiliarias = $query->latest()->paginate(15);
        return view('admin.imobiliarias.index', compact('imobiliarias'));
    }

    public function show(Imobiliaria $imobiliaria)
    {
        $imobiliaria->load('imoveis', 'plano.plano', 'canais');
        return view('admin.imobiliarias.show', compact('imobiliaria'));
    }

    public function aprovar(Imobiliaria $imobiliaria)
    {
        $imobiliaria->update([
            'estado' => 'aprovada',
            'aprovado_em' => now(),
        ]);

        return redirect()->back()->with('success', 'Imobiliária aprovada com sucesso.');
    }

    public function suspender(Imobiliaria $imobiliaria)
    {
        $validated = request()->validate([
            'motivo' => 'nullable|string|max:1000',
            'suspensa_ate' => 'nullable|date|after:today',
        ]);

        $admin = Auth::guard('admin')->user();

        $imobiliaria->update([
            'estado' => 'suspensa',
            'motivo_suspensao' => $validated['motivo'] ?? null,
            'suspensa_ate' => $validated['suspensa_ate'] ?? null,
            'suspensa_por' => $admin->id,
        ]);

        return redirect()->back()->with('success', 'Imobiliária suspensa.');
    }

    public function reativar(Imobiliaria $imobiliaria)
    {
        $imobiliaria->update([
            'estado' => 'aprovada',
            'motivo_suspensao' => null,
            'suspensa_ate' => null,
            'suspensa_por' => null,
        ]);

        return redirect()->back()->with('success', 'Imobiliária reativada.');
    }

    public function bloquear(Imobiliaria $imobiliaria)
    {
        $imobiliaria->update(['estado' => 'bloqueada']);
        return redirect()->back()->with('success', 'Imobiliária bloqueada.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:imobiliarias,id',
            'acao' => 'required|in:aprovar,suspender,bloquear',
        ]);

        $ids = $request->ids;
        $sucesso = 0;
        $falha = 0;

        DB::transaction(function () use ($ids, $request, &$sucesso, &$falha) {
            foreach (Imobiliaria::whereIn('id', $ids)->get() as $imob) {
                try {
                    match ($request->acao) {
                        'aprovar' => $imob->update(['estado' => 'aprovada']),
                        'suspender' => $imob->update(['estado' => 'suspensa']),
                        'bloquear' => $imob->update(['estado' => 'bloqueada']),
                    };
                    ActivityLogService::log("bulk_{$request->acao}_imobiliaria", $imob);
                    $sucesso++;
                } catch (\Throwable $e) {
                    $falha++;
                }
            }
        });

        return back()->with('success', "{$sucesso} processados, {$falha} falharam.");
    }
}
