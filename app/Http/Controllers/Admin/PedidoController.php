<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendAssinaturaLiberadaEmail;
use App\Models\ImobiliariaPlano;
use App\Models\PedidoAtivacao;
use App\Models\Plano;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $query = PedidoAtivacao::with('imobiliaria');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->latest()->paginate(15);
        $stats = [
            'novo' => PedidoAtivacao::where('estado', 'novo')->count(),
            'em_negociacao' => PedidoAtivacao::where('estado', 'em_negociacao')->count(),
            'proposta_enviada' => PedidoAtivacao::where('estado', 'proposta_enviada')->count(),
            'fechado' => PedidoAtivacao::where('estado', 'fechado')->count(),
            'perdido' => PedidoAtivacao::where('estado', 'perdido')->count(),
        ];

        return view('admin.pedidos.index', compact('pedidos', 'stats'));
    }

    public function show(PedidoAtivacao $pedido)
    {
        $pedido->load('imobiliaria.plano.plano');
        $planos = Plano::where('ativo', true)->orderBy('ordem')->get();
        return view('admin.pedidos.show', compact('pedido', 'planos'));
    }

    public function atualizarEstado(Request $request, PedidoAtivacao $pedido)
    {
        $validated = $request->validate([
            'estado' => 'required|in:novo,em_negociacao,proposta_enviada,fechado,perdido',
        ]);

        $pedido->update($validated);
        return redirect()->back()->with('success', 'Estado atualizado.');
    }

    public function liberarPlano(Request $request, PedidoAtivacao $pedido)
    {
        $validated = $request->validate([
            'plano_id' => 'required|exists:planos,id',
            'dias_validade' => 'required|integer|min:1',
        ]);

        $plano = Plano::find($validated['plano_id']);

        // Desativar plano anterior se existir
        ImobiliariaPlano::where('imobiliaria_id', $pedido->imobiliaria_id)
            ->where('ativo', true)
            ->update(['ativo' => false]);

        // Criar nova assinatura
        $assinatura = ImobiliariaPlano::create([
            'imobiliaria_id' => $pedido->imobiliaria_id,
            'plano_id' => $plano->id,
            'posts_usados' => 0,
            'data_inicio' => now(),
            'data_expiracao' => now()->addDays($validated['dias_validade']),
            'ativo' => true,
        ]);

        $pedido->update(['estado' => 'fechado']);

        SendAssinaturaLiberadaEmail::dispatch($assinatura);

        return redirect()->back()->with('success', 'Plano liberado com sucesso.');
    }
}
