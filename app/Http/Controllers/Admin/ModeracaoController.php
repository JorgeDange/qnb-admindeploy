<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use App\Models\Denuncia;
use App\Models\Visita;
use App\Services\Cliente\ClienteNotificacaoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeracaoController extends Controller
{
    // ==================== DENÚNCIAS ====================

    public function denuncias(Request $request)
    {
        $query = Denuncia::with(['imovel', 'imobiliaria']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('motivo')) {
            $query->where('motivo', $request->motivo);
        }

        $denuncias = $query->orderByDesc('created_at')->paginate(15);
        $estatisticas = [
            'pendente' => Denuncia::where('estado', 'pendente')->count(),
            'em_analise' => Denuncia::where('estado', 'em_analise')->count(),
            'resolvida' => Denuncia::where('estado', 'resolvida')->count(),
        ];

        return view('admin.denuncias.index', compact('denuncias', 'estatisticas'));
    }

    public function denunciaShow(Denuncia $denuncia)
    {
        $denuncia->load(['imovel', 'imobiliaria', 'resolvidoPor']);
        return view('admin.denuncias.show', compact('denuncia'));
    }

    public function denunciaResolver(Request $request, Denuncia $denuncia)
    {
        $validated = $request->validate([
            'resolucao' => 'required|string|max:2000',
        ]);

        $admin = Auth::guard('admin')->user();
        $denuncia->resolver($validated['resolucao'], $admin->id);

        // Notificar cliente in-app (C7)
        if ($denuncia->cliente_id) {
            ClienteNotificacaoService::enviar(
                $denuncia->cliente_id,
                'denuncia',
                'Denúncia resolvida',
                'A sua denúncia foi analisada e resolvida: ' . $validated['resolucao'],
                route('cliente.denuncias')
            );

            \App\Services\PushNotificationService::enviar(
                $denuncia->cliente_id,
                'Denúncia resolvida',
                'A sua denúncia foi analisada e resolvida: ' . $validated['resolucao'],
                route('cliente.denuncias')
            );
        }

        return redirect()->back()->with('success', 'Denúncia resolvida.');
    }

    public function denunciaArquivar(Denuncia $denuncia)
    {
        $denuncia->update(['estado' => 'arquivada']);

        // Notificar cliente in-app (C7)
        if ($denuncia->cliente_id) {
            ClienteNotificacaoService::enviar(
                $denuncia->cliente_id,
                'denuncia',
                'Denúncia arquivada',
                'A sua denúncia foi arquivada sem resolução após análise da nossa equipa.',
                route('cliente.denuncias')
            );

            \App\Services\PushNotificationService::enviar(
                $denuncia->cliente_id,
                'Denúncia arquivada',
                'A sua denúncia foi arquivada sem resolução após análise da nossa equipa.',
                route('cliente.denuncias')
            );
        }

        return redirect()->back()->with('success', 'Denúncia arquivada.');
    }

    // ==================== AVALIAÇÕES ====================

    public function avaliacoes(Request $request)
    {
        $query = Avaliacao::with(['imovel', 'imobiliaria']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $avaliacoes = $query->orderByDesc('created_at')->paginate(15);
        $estatisticas = [
            'pendente' => Avaliacao::where('estado', 'pendente')->count(),
            'aprovada' => Avaliacao::where('estado', 'aprovada')->count(),
            'rejeitada' => Avaliacao::where('estado', 'rejeitada')->count(),
        ];

        return view('admin.avaliacoes.index', compact('avaliacoes', 'estatisticas'));
    }

    public function avaliacaoAprovar(Avaliacao $avaliacao)
    {
        $avaliacao->update(['estado' => 'aprovada']);

        // Notificar cliente in-app (C7)
        if ($avaliacao->cliente_id) {
            ClienteNotificacaoService::enviar(
                $avaliacao->cliente_id,
                'avaliacao',
                'Avaliação aprovada',
                'A sua avaliação do imóvel "' . $avaliacao->imovel->titulo . '" foi aprovada e já está visível no site.',
                route('cliente.avaliacoes')
            );

            \App\Services\PushNotificationService::enviar(
                $avaliacao->cliente_id,
                'Avaliação aprovada',
                'A sua avaliação do imóvel "' . $avaliacao->imovel->titulo . '" foi aprovada e já está visível no site.',
                route('cliente.avaliacoes')
            );
        }

        return redirect()->back()->with('success', 'Avaliação aprovada.');
    }

    public function avaliacaoRejeitar(Request $request, Avaliacao $avaliacao)
    {
        $validated = $request->validate([
            'motivo_rejeicao' => 'nullable|string|max:500',
        ]);

        $avaliacao->update([
            'estado' => 'rejeitada',
            'motivo_rejeicao' => $validated['motivo_rejeicao'] ?? null,
        ]);

        // Notificar cliente in-app (C7)
        if ($avaliacao->cliente_id) {
            $motivoTexto = $validated['motivo_rejeicao'] ? ': ' . $validated['motivo_rejeicao'] : '.';

            ClienteNotificacaoService::enviar(
                $avaliacao->cliente_id,
                'avaliacao',
                'Avaliação rejeitada',
                'A sua avaliação do imóvel "' . $avaliacao->imovel->titulo . '" foi rejeitada' . $motivoTexto . ' Pode corrigi-la e reenviá-la.',
                route('cliente.avaliacoes')
            );

            \App\Services\PushNotificationService::enviar(
                $avaliacao->cliente_id,
                'Avaliação rejeitada',
                'A sua avaliação do imóvel "' . $avaliacao->imovel->titulo . '" foi rejeitada' . $motivoTexto,
                route('cliente.avaliacoes')
            );
        }

        return redirect()->back()->with('success', 'Avaliação rejeitada.');
    }

    public function avaliacaoApagar(Avaliacao $avaliacao)
    {
        $avaliacao->delete();
        return redirect()->route('admin.avaliacoes')->with('success', 'Avaliação apagada.');
    }

    // ==================== VISITAS ====================

    public function visitas(Request $request)
    {
        $query = Visita::with(['imovel', 'imobiliaria']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('periodo')) {
            $query->whereDate('data_visita', '>=', now()->subDays((int) $request->periodo));
        }

        $visitas = $query->orderBy('data_visita', 'desc')->paginate(15);
        $estatisticas = [
            'hoje' => Visita::whereDate('data_visita', today())->count(),
            'semana' => Visita::whereBetween('data_visita', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'pendente' => Visita::where('estado', 'pendente')->count(),
        ];

        return view('admin.visitas.index', compact('visitas', 'estatisticas'));
    }

    public function visitaEstado(Request $request, Visita $visita)
    {
        $validated = $request->validate([
            'estado' => 'required|in:pendente,confirmada,concluida,cancelada',
        ]);

        $visita->update($validated);
        return redirect()->back()->with('success', 'Estado da visita atualizado.');
    }
}
