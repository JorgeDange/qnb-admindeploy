<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin || !$admin->ativo) {
            auth()->guard('admin')->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Conta inativa ou sem permissão.');
        }

        if (!empty($roles) && !in_array($admin->role, $roles)) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Sem permissão para esta ação.');
        }

        return $next($request);
    }
}
