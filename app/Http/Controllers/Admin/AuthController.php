<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $admin = Auth::guard('admin')->user();

            // Contas desativadas não podem autenticar, mesmo com credenciais válidas
            if (! $admin->ativo) {
                Auth::guard('admin')->login(false);
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['email' => 'A sua conta está desativada. Contacte o administrador.']);
            }

            // Registar último login (segurança: auditoria de acessos)
            $admin->forceFill([
                'ultimo_login' => now(),
                'ultimo_ip' => $request->ip(),
            ])->save();

            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Incrementar tentativas falhadas (usado pelo bloqueio de conta)
        $adminFalhou = Admin::where('email', $credentials['email'])->first();
        if ($adminFalhou) {
            $adminFalhou->forceFill([
                'tentativas_login' => $adminFalhou->tentativas_login + 1,
            ])->save();
        }

        return back()->withErrors(['email' => 'Credenciais inválidas.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
