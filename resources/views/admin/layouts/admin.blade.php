<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin — QNB-Imobiliária')</title>

    <link rel="stylesheet" href="{{ asset('assets/icon/flaticon_real_estate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/animate-wow/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    {{-- ?v=filemtime: cache-busting — força o browser a recarregar o asset quando o ficheiro muda --}}
    <link rel="stylesheet" href="{{ asset('painel-assets/css/painel.css').'?v='.filemtime(public_path('painel-assets/css/painel.css')) }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/logo-c.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @stack('styles')
</head>

<body data-pagina="admin">
    <div class="preloader" id="preloader">
        <div class="loader"></div>
    </div>

    <div class="ul-painel">

        <!-- TOPBAR -->
        <div class="ul-painel-topbar">
            <div class="ul-painel-topbar-left">
                <a href="{{ route('admin.dashboard') }}"><img src="{{ asset('assets/img/logo.svg') }}" alt="QNB-Imobiliária" class="logo"></a>
            </div>
            <div class="ul-painel-topbar-right">
                <span class="ul-badge-estado" style="background:rgba(255,255,255,0.15);border-color:rgba(255,255,255,0.25);">Admin</span>
                {{-- O site público vive na app qnb-imobiliaria (domínio diferente). URL configurável via .env --}}
                <a href="{{ config('app.frontend_url', '#') }}" class="ul-painel-ver-site">Ver site público</a>
            </div>
        </div>

        <div class="ul-painel-corpo">

            <!-- SIDEBAR -->
            <aside class="ul-painel-sidebar">
                @php
                    /* Visibilidade do menu por role — espelha o middleware admin.role das rotas */
                    $adminRole = Auth::guard('admin')->user()->role ?? '';
                    $verCom = in_array($adminRole, ['super_admin', 'comercial']);
                    $verMod = in_array($adminRole, ['super_admin', 'moderador']);
                @endphp
                <div class="ul-painel-sidebar-perfil">
                    <div class="ul-painel-avatar"><img src="{{ asset('assets/img/logo-c.svg') }}" alt="Admin"></div>
                    <h3 class="ul-painel-nome">{{ Auth::guard('admin')->user()->nome ?? 'Admin' }}</h3>
                    <span class="ul-painel-email">{{ Auth::guard('admin')->user()->email ?? 'admin@qnbangola.com' }}</span>
                </div>
                <ul class="ul-painel-nav">
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    </li>

                    @if($verCom || $verMod)
                    <li class="ul-painel-nav-seccao">MODERAÇÃO</li>
                    @endif

                    @if($verCom)
                    <li class="{{ request()->routeIs('admin.imobiliarias*') ? 'active' : '' }}">
                        <a href="{{ route('admin.imobiliarias') }}"><i class="bi bi-buildings"></i> Imobiliárias
                            @if($impPend > 0)<span class="ul-painel-nav-badge">{{ $impPend }}</span>@endif
                        </a>
                    </li>
                    @endif

                    @if($verCom)
                    <li class="{{ request()->routeIs('admin.imoveis*') ? 'active' : '' }}">
                        <a href="{{ route('admin.imoveis') }}"><i class="bi bi-house-door"></i> Imóveis
                            @if($imoPend > 0)<span class="ul-painel-nav-badge">{{ $imoPend }}</span>@endif
                        </a>
                    </li>
                    @endif

                    @if($verCom)
                    <li class="{{ request()->routeIs('admin.pedidos*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pedidos') }}"><i class="bi bi-inbox"></i> Pedidos
                            @if($pedNovos > 0)<span class="ul-painel-nav-badge">{{ $pedNovos }}</span>@endif
                        </a>
                    </li>
                    @endif

                    @if($verMod)
                    <li class="{{ request()->routeIs('admin.denuncias*') ? 'active' : '' }}">
                        <a href="{{ route('admin.denuncias') }}"><i class="bi bi-flag"></i> Denúncias
                            @if($denPend > 0)<span class="ul-painel-nav-badge">{{ $denPend }}</span>@endif
                        </a>
                    </li>
                    @endif

                    @if($verMod)
                    <li class="{{ request()->routeIs('admin.avaliacoes*') ? 'active' : '' }}">
                        <a href="{{ route('admin.avaliacoes') }}"><i class="bi bi-star"></i> Avaliações
                            @if($avalPend > 0)<span class="ul-painel-nav-badge">{{ $avalPend }}</span>@endif
                        </a>
                    </li>
                    @endif

                    {{-- ═══ CLIENTES ═══ --}}
                    <li class="ul-painel-nav-seccao">CLIENTES</li>

                    @if($verCom)
                    <li class="{{ request()->routeIs('admin.mensagens*') ? 'active' : '' }}">
                        <a href="{{ route('admin.mensagens') }}"><i class="bi bi-envelope"></i> Mensagens
                            @if($msgNaoLidas > 0)<span class="ul-painel-nav-badge">{{ $msgNaoLidas }}</span>@endif
                        </a>
                    </li>
                    @endif

                    <li class="{{ request()->routeIs('admin.visitas*') ? 'active' : '' }}">
                        <a href="{{ route('admin.visitas') }}"><i class="bi bi-calendar-event"></i> Visitas
                            @if($visHoje > 0)<span class="ul-painel-nav-badge">{{ $visHoje }}</span>@endif
                        </a>
                    </li>

                    @if($verCom)
                    <li class="ul-painel-nav-seccao">COMERCIAL</li>
                    @endif

                    @if($adminRole === 'super_admin')
                    <li class="{{ request()->routeIs('admin.planos*') ? 'active' : '' }}">
                        <a href="{{ route('admin.planos') }}"><i class="bi bi-gem"></i> Planos</a>
                    </li>
                    @endif

                    @if($verCom)
                    <li class="{{ request()->routeIs('admin.subscricoes*') ? 'active' : '' }}">
                        <a href="{{ route('admin.subscricoes') }}"><i class="bi bi-arrow-repeat"></i> Subscrições
                            @if($subAtivas > 0)<span class="ul-painel-nav-badge ul-painel-nav-badge--info">{{ $subAtivas }}</span>@endif
                        </a>
                    </li>
                    @endif

                    @if($verCom)
                    <li class="{{ request()->routeIs('admin.pagamentos*') ? 'active' : '' }}">
                        <a href="{{ route('admin.pagamentos') }}"><i class="bi bi-credit-card"></i> Pagamentos
                            @if($pagPend > 0)<span class="ul-painel-nav-badge">{{ $pagPend }}</span>@endif
                        </a>
                    </li>
                    @endif

                    @if($verCom)
                    <li class="{{ request()->routeIs('admin.faturas*') ? 'active' : '' }}">
                        <a href="{{ route('admin.faturas') }}"><i class="bi bi-receipt"></i> Faturas                        </a>
                    </li>
                    @endif

                    @if($verMod)
                    <li class="ul-painel-nav-seccao">CONTEÚDO</li>

                    <li class="{{ request()->routeIs('admin.settings') || request()->routeIs('admin.depoimentos*') || request()->routeIs('admin.parceiros*') || request()->routeIs('admin.faq*') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings') }}"><i class="bi bi-collection"></i> Conteúdo</a>
                    </li>
                    @endif

                    @if($verCom)
                    <li class="ul-painel-nav-seccao">ANÁLISE</li>

                    <li class="{{ request()->routeIs('admin.relatorios*') ? 'active' : '' }}">
                        <a href="{{ route('admin.relatorios') }}"><i class="bi bi-bar-chart"></i> Relatórios</a>
                    </li>
                    @endif

                    @if($verCom)
                    <li class="{{ request()->routeIs('admin.exportar*') ? 'active' : '' }}">
                        <a href="{{ route('admin.exportar.imoveis') }}"><i class="bi bi-download"></i> Exportar</a>
                    </li>
                    @endif

                    @if($adminRole === 'super_admin')
                    <li class="ul-painel-nav-seccao">SISTEMA</li>

                    <li class="{{ request()->routeIs('admin.logs*') ? 'active' : '' }}">
                        <a href="{{ route('admin.logs') }}"><i class="bi bi-clock-history"></i> Logs</a>
                    </li>

                    <li class="{{ request()->routeIs('admin.compliance*') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i> Configurações</a>
                    </li>

                    <li class="{{ request()->routeIs('admin.empresa-config*') ? 'active' : '' }}">
                        <a href="{{ route('admin.empresa-config') }}"><i class="bi bi-building"></i> Empresa</a>
                    </li>

                    <li class="{{ request()->routeIs('admin.admins*') ? 'active' : '' }}">
                        <a href="{{ route('admin.admins') }}"><i class="bi bi-person-badge"></i> Administradores
                            @if($adminCount > 0)<span class="ul-painel-nav-badge ul-painel-nav-badge--info">{{ $adminCount }}</span>@endif
                        </a>
                    </li>

                    @endif

                    <li>
                        <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right"></i> Sair</a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">@csrf</form>
                    </li>
                </ul>
            </aside>

            <!-- CONTEÚDO -->
            <div class="ul-painel-conteudo">
                <div class="ul-painel-conteudo-inner">

                    @include('components.flash')

                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="ul-painel-rodape">
            <span>© 2026 QNB-Imobiliária — Painel Admin</span>
        </footer>
    </div>

    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/animate-wow/wow.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chart.umd.min.js') }}"></script>
    <script src="{{ asset('painel-assets/js/charts-helpers.js').'?v='.filemtime(public_path('painel-assets/js/charts-helpers.js')) }}"></script>
    <!-- Outros scripts da página entram pela stack 'scripts' -->
    <script src="{{ asset('painel-assets/js/painel-loader.js') }}"></script>
    <script src="{{ asset('painel-assets/js/painel.js').'?v='.filemtime(public_path('painel-assets/js/painel.js')) }}"></script>
    @include('components.modal-global')
    @stack('scripts')
</body>

</html>
