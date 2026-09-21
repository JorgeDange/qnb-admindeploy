<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pagamento::with(['imobiliaria', 'subscricao.plano']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pagamentos = $query->orderByDesc('created_at')->paginate(15);
        return view('admin.pagamentos.index', compact('pagamentos'));
    }

    public function show(Pagamento $pagamento)
    {
        $pagamento->load(['imobiliaria', 'subscricao.plano', 'fatura']);
        return view('admin.pagamentos.show', compact('pagamento'));
    }
}
