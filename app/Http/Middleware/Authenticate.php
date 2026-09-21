<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        return route('admin.login');
    }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Sessão expirada.'], 401);
        }

        return redirect()->route('admin.login')->with('error', 'Sessão expirada. Faça login novamente.');
    }
}
