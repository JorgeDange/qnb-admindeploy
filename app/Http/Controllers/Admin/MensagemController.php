<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMensagem;
use App\Models\Imobiliaria;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensagemController extends Controller
{
    public function index()
    {
        $threads = AdminMensagem::whereNull('thread_id')
            ->with(['imobiliaria', 'ultimaResposta.imobiliaria', 'ultimaResposta.admin'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.mensagens.index', compact('threads'));
    }

    public function form()
    {
        $imobiliarias = Imobiliaria::where('estado', 'aprovada')->orderBy('nome')->get();
        return view('admin.mensagens.form', compact('imobiliarias'));
    }

    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'imobiliaria_id' => 'required|exists:imobiliarias,id',
            'assunto' => 'required|string|max:255',
            'texto' => 'required|string|max:5000',
        ]);

        $admin = Auth::guard('admin')->user();

        $msg = AdminMensagem::create([
            'admin_id' => $admin->id,
            'imobiliaria_id' => $validated['imobiliaria_id'],
            'autor_tipo' => 'admin',
            'assunto' => $validated['assunto'],
            'texto' => $validated['texto'],
        ]);

        ActivityLogService::log('enviar_mensagem', $msg, ['imobiliaria_id' => $validated['imobiliaria_id']]);

        return redirect()->route('admin.mensagens.thread', $msg)->with('success', 'Mensagem enviada.');
    }

    public function thread(AdminMensagem $thread)
    {
        $respostas = AdminMensagem::where('thread_id', $thread->id)
            ->with(['admin', 'imobiliaria'])
            ->orderBy('created_at')
            ->get();

        $thread->load(['admin', 'imobiliaria']);

        return view('admin.mensagens.show', compact('thread', 'respostas'));
    }

    public function responder(Request $request, AdminMensagem $thread)
    {
        $validated = $request->validate([
            'texto' => 'required|string|max:5000',
        ]);

        $admin = Auth::guard('admin')->user();

        AdminMensagem::create([
            'thread_id' => $thread->id,
            'admin_id' => $admin->id,
            'imobiliaria_id' => $thread->imobiliaria_id,
            'autor_tipo' => 'admin',
            'texto' => $validated['texto'],
        ]);

        return back()->with('success', 'Resposta enviada.');
    }

    public function fechar(AdminMensagem $thread)
    {
        $thread->update(['lida' => true]);
        return back()->with('success', 'Thread fechada.');
    }
}
