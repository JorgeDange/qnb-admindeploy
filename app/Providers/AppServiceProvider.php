<?php

namespace App\Providers;

use App\Providers\View\Composers\AdminSidebarComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production') && request()->header('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }

        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') . '|' . $request->ip());
        });

        // ATENÇÃO: esta app NÃO registra observers.
        // A qnb-imobiliaria (mesma BD) é quem registra observers e dispara emails
        // de eventos do lado público. Registar aqui dispararia tudo em duplicado.

        View::composer(
            'admin.*',
            AdminSidebarComposer::class
        );
    }
}
