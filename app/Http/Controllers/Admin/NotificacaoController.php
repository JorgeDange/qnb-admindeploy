<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Auth;

class NotificacaoController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $notificacoes = Notificacao::where('admin_id', $admin->id)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.notificacoes.index', compact('notificacoes'));
    }

    public function ler(Notificacao $notificacao)
    {
        $notificacao->marcarComoLida();
        if ($notificacao->url) {
            return redirect($notificacao->url);
        }
        return back();
    }

    public function lerTodas()
    {
        $admin = Auth::guard('admin')->user();
        Notificacao::where('admin_id', $admin->id)
            ->where('lida', false)
            ->update(['lida' => true, 'lida_em' => now()]);

        return back();
    }

    public function contagem()
    {
        $admin = Auth::guard('admin')->user();
        $contagem = Notificacao::where('admin_id', $admin->id)->where('lida', false)->count();
        return response()->json(['contagem' => $contagem]);
    }
}
