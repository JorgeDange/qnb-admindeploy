<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plano;
use Illuminate\Http\Request;

class PlanoController extends Controller
{
    public function index()
    {
        $planos = Plano::withCount('imobiliarias')->orderBy('ordem')->orderBy('nome')->get();
        return view('admin.planos.index', compact('planos'));
    }

    public function form(?Plano $plano = null)
    {
        return view('admin.planos.form', compact('plano'));
    }

    public function salvar(Request $request, ?Plano $plano = null)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:planos,nome,' . ($plano?->id ?? ''),
            'descricao' => 'nullable|string|max:1000',
            'posts_limite' => 'required|integer|min:1',
            'dias_validade' => 'required|integer|min:1',
            'preco' => 'required|numeric|min:0',
            'moeda' => 'required|in:Kz,USD',
            'ativo' => 'boolean',
            'destaque' => 'boolean',
            'ordem' => 'nullable|integer|min:0',
        ]);

        $validated['ativo'] = $request->boolean('ativo');
        $validated['destaque'] = $request->boolean('destaque');

        if ($plano) {
            $plano->update($validated);
        } else {
            $plano = Plano::create($validated);
        }

        return redirect()->route('admin.planos')->with('success', $plano ? 'Plano atualizado.' : 'Plano criado.');
    }

    public function apagar(Plano $plano)
    {
        if ($plano->imobiliarias()->where('ativo', true)->exists()) {
            return back()->with('error', 'Não é possível apagar plano com subscrições ativas.');
        }

        $plano->delete();
        return redirect()->route('admin.planos')->with('success', 'Plano apagado.');
    }

    public function toggle(Plano $plano)
    {
        $plano->update(['ativo' => !$plano->ativo]);
        return back()->with('success', 'Estado do plano alterado.');
    }
}
