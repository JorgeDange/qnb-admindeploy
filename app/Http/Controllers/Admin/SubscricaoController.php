<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Imobiliaria;
use App\Models\ImobiliariaPlano;
use App\Models\Plano;
use Illuminate\Http\Request;

class SubscricaoController extends Controller
{
    public function index(Request $request)
    {
        $query = ImobiliariaPlano::with(['imobiliaria', 'plano']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('imobiliaria_id')) {
            $query->where('imobiliaria_id', $request->imobiliaria_id);
        }

        $subscricoes = $query->orderByDesc('created_at')->paginate(15);
        $imobiliarias = Imobiliaria::orderBy('nome')->get();

        return view('admin.subscricoes.index', compact('subscricoes', 'imobiliarias'));
    }

    public function form(?ImobiliariaPlano $subscricao = null)
    {
        $imobiliarias = Imobiliaria::orderBy('nome')->get();
        $planos = Plano::where('ativo', true)->orderBy('ordem')->get();

        return view('admin.subscricoes.form', compact('subscricao', 'imobiliarias', 'planos'));
    }

    public function salvar(Request $request, ?ImobiliariaPlano $subscricao = null)
    {
        $validated = $request->validate([
            'imobiliaria_id' => 'required|exists:imobiliarias,id',
            'plano_id' => 'required|exists:planos,id',
            'data_inicio' => 'required|date',
            'data_expiracao' => 'required|date|after:data_inicio',
            'posts_usados' => 'nullable|integer|min:0',
            'estado' => 'required|in:ativa,expirada,cancelada,pendente',
            'renovacao_automatica' => 'boolean',
        ]);

        $validated['renovacao_automatica'] = $request->boolean('renovacao_automatica');
        $validated['ativo'] = in_array($validated['estado'], ['ativa', 'pendente']);

        if ($subscricao) {
            $subscricao->update($validated);
        } else {
            $subscricao = ImobiliariaPlano::create($validated);
        }

        return redirect()->route('admin.subscricoes')->with('success', $subscricao ? 'Subscrição atualizada.' : 'Subscrição criada.');
    }

    public function renovar(ImobiliariaPlano $subscricao)
    {
        $subscricao->renovar();
        $subscricao->update(['estado' => 'ativa', 'ativo' => true]);

        return back()->with('success', 'Subscrição renovada. Nova data de expiração: ' . $subscricao->data_expiracao->format('d/m/Y'));
    }

    public function cancelar(ImobiliariaPlano $subscricao)
    {
        $subscricao->update(['estado' => 'cancelada', 'ativo' => false]);

        return back()->with('success', 'Subscrição cancelada.');
    }

    public function apagar(ImobiliariaPlano $subscricao)
    {
        $subscricao->delete();
        return redirect()->route('admin.subscricoes')->with('success', 'Subscrição apagada.');
    }
}
