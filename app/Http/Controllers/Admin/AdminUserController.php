<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = Admin::orderBy('nome')->get();
        return view('admin.admins.index', compact('admins'));
    }

    public function form(?Admin $admin = null)
    {
        return view('admin.admins.form', compact('admin'));
    }

    public function salvar(Request $request, ?Admin $admin = null)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . ($admin?->id ?? ''),
            'password' => ($admin ? 'nullable' : 'required') . '|string|min:8|confirmed',
            'role' => 'required|in:super_admin,comercial,moderador',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->boolean('ativo');

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($admin) {
            $admin->update($validated);
        } else {
            $admin = Admin::create($validated);
        }

        return redirect()->route('admin.admins')->with('success', $admin ? 'Administrador atualizado.' : 'Administrador criado.');
    }

    public function apagar(Admin $admin)
    {
        if ($admin->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'Não pode apagar a sua própria conta.');
        }

        $superAdminCount = Admin::where('role', 'super_admin')->whereNull('deleted_at')->count();
        if ($admin->role === 'super_admin' && $superAdminCount <= 1) {
            return back()->with('error', 'Não é possível apagar o último super_admin.');
        }

        $admin->delete();
        return redirect()->route('admin.admins')->with('success', 'Administrador apagado.');
    }

    public function toggle(Admin $admin)
    {
        if ($admin->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'Não pode desativar a sua própria conta.');
        }

        $admin->update(['ativo' => !$admin->ativo]);
        return back()->with('success', 'Estado do administrador alterado.');
    }
}
