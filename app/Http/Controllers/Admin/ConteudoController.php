<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depoimento;
use App\Models\Faq;
use App\Models\Parceiro;
use App\Models\Setting;
use Illuminate\Http\Request;

class ConteudoController extends Controller
{
    // ==================== SETTINGS ====================

    public function settings()
    {
        $settings = Setting::all()->pluck('valor', 'chave');
        return view('admin.conteudo.settings', compact('settings'));
    }

    public function settingsSalvar(Request $request)
    {
        $validated = $request->validate([
            'site_nome' => 'nullable|string|max:255',
            'email_principal' => 'nullable|email|max:255',
            'telefone_principal' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'endereco' => 'nullable|string|max:500',
            'facebook' => 'nullable|url|max:500',
            'instagram' => 'nullable|url|max:500',
            'linkedin' => 'nullable|url|max:500',
            'youtube' => 'nullable|url|max:500',
        ]);

        foreach ($validated as $chave => $valor) {
            Setting::set($chave, $valor);
        }

        return redirect()->back()->with('success', 'Definições guardadas.');
    }

    // ==================== DEPOIMENTOS ====================

    public function depoimentos()
    {
        $depoimentos = Depoimento::orderBy('ordem')->get();
        return view('admin.conteudo.depoimentos', compact('depoimentos'));
    }

    public function depoimentoSalvar(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cargo' => 'nullable|string|max:255',
            'texto' => 'required|string|max:1000',
            'estrelas' => 'required|integer|min:1|max:5',
            'ativo' => 'nullable',
        ]);

        $validated['ativo'] = $request->boolean('ativo');

        Depoimento::create($validated);
        return redirect()->route('admin.depoimentos')->with('success', 'Depoimento criado.');
    }

    public function depoimentoApagar(Depoimento $depoimento)
    {
        $depoimento->delete();
        return redirect()->back()->with('success', 'Depoimento apagado.');
    }

    // ==================== PARCEIROS ====================

    public function parceiros()
    {
        $parceiros = Parceiro::orderBy('ordem')->get();
        return view('admin.conteudo.parceiros', compact('parceiros'));
    }

    public function parceiroSalvar(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'url' => 'nullable|url|max:500',
            'ativo' => 'nullable',
        ]);

        $validated['ativo'] = $request->boolean('ativo');

        Parceiro::create($validated);
        return redirect()->route('admin.parceiros')->with('success', 'Parceiro criado.');
    }

    public function parceiroApagar(Parceiro $parceiro)
    {
        $parceiro->delete();
        return redirect()->back()->with('success', 'Parceiro apagado.');
    }

    // ==================== FAQ ====================

    public function faq()
    {
        $faqs = Faq::orderBy('ordem')->orderBy('id')->get();
        return view('admin.conteudo.faq', compact('faqs'));
    }

    public function faqSalvar(Request $request, ?Faq $faq = null)
    {
        $validated = $request->validate([
            'pergunta' => 'required|string|max:500',
            'resposta' => 'required|string|max:5000',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'nullable',
        ]);

        $validated['ativo'] = $request->boolean('ativo');

        if ($faq) {
            $faq->update($validated);
        } else {
            Faq::create($validated);
        }

        return redirect()->route('admin.faq')->with('success', $faq ? 'FAQ atualizada.' : 'FAQ criada.');
    }

    public function faqToggle(Faq $faq)
    {
        $faq->update(['ativo' => !$faq->ativo]);
        return back()->with('success', 'Estado da FAQ alterado.');
    }

    public function faqApagar(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faq')->with('success', 'FAQ apagada.');
    }
}
