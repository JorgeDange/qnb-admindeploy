<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Faturas\AprovarPagamentoAction;
use App\Actions\Faturas\CancelarFaturaAction;
use App\Actions\Faturas\RejeitarPagamentoAction;
use App\Http\Controllers\Controller;
use App\Models\Fatura;
use App\Services\FaturaService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaturaController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService,
        private FaturaService $faturaService,
    ) {}

    public function index(Request $request)
    {
        $estado = $request->input('estado');

        $query = Fatura::with(['imobiliaria', 'linhas']);

        if ($estado) {
            $query->where('estado', $estado);
        }

        $faturas = $query->orderByDesc('emitida_em')->paginate(15);

        return view('admin.faturas.index', compact('faturas', 'estado'));
    }

    public function show(Fatura $fatura)
    {
        $fatura->load(['pagamento', 'imobiliaria', 'linhas', 'aprovadaPor', 'rejeitadaPor']);
        return view('admin.faturas.show', compact('fatura'));
    }

    public function download(Fatura $fatura)
    {
        return $this->invoiceService->download($fatura);
    }

    public function downloadRecibo(Fatura $fatura)
    {
        if (!$fatura->isPaga()) {
            abort(404);
        }

        return $this->faturaService->downloadRecibo($fatura);
    }

    public function reenviar(Fatura $fatura)
    {
        $this->invoiceService->gerarPdf($fatura);
        return back()->with('success', 'Fatura reenviada com sucesso.');
    }

    public function aprovar(Fatura $fatura)
    {
        if (!$fatura->isAguardaAprovacao()) {
            return back()->with('error', 'Apenas faturas em espera podem ser aprovadas.');
        }

        $admin = Auth::guard('admin')->user();
        $action = app(AprovarPagamentoAction::class);
        $action->execute($fatura, $admin->id);

        return back()->with('success', 'Pagamento aprovado, recibo gerado e plano ativado.');
    }

    public function rejeitar(Request $request, Fatura $fatura)
    {
        if (!$fatura->isAguardaAprovacao()) {
            return back()->with('error', 'Apenas faturas em espera podem ser rejeitadas.');
        }

        $validated = $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $admin = Auth::guard('admin')->user();
        $action = app(RejeitarPagamentoAction::class);
        $action->execute($fatura, $admin->id, $validated['motivo']);

        return back()->with('success', 'Pagamento rejeitado. A imobiliária foi notificada.');
    }

    public function cancelar(Request $request, Fatura $fatura)
    {
        if ($fatura->isPaga() || $fatura->isCancelada() || $fatura->isExpirada()) {
            return back()->with('error', 'Não é possível cancelar esta fatura.');
        }

        $admin = Auth::guard('admin')->user();
        $action = app(CancelarFaturaAction::class);
        $action->execute($fatura, $admin->id);

        return back()->with('success', 'Fatura cancelada com sucesso.');
    }
}
