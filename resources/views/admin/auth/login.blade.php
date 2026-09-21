<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin — Entrar | QNB-Imobiliária</title>

    <!-- libraries CSS -->
    <link rel="stylesheet" href="{{ asset('assets/icon/flaticon_real_estate.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/animate-wow/animate.min.css') }}">

    <!-- custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('painel-assets/css/painel.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/logo-c.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>

<body>
    <div class="preloader" id="preloader">
        <div class="loader"></div>
    </div>

    <div class="ul-painel">

        <!-- TOPBAR -->
        <div class="ul-painel-topbar">
            <div class="ul-painel-topbar-left">
                <a href="{{ route('home') }}"><img src="{{ asset('assets/img/logo.svg') }}" alt="QNB-Imobiliária" class="logo"></a>
            </div>
            <div class="ul-painel-topbar-right">
                <a href="{{ route('home') }}" class="ul-painel-ver-site">Ver site público</a>
            </div>
        </div>

        <!-- AUTH -->
        <div class="ul-auth">
            <div class="ul-auth-card">
                <div class="ul-auth-logo">
                    <img src="{{ asset('assets/img/logo-c.svg') }}" alt="QNB-Imobiliária">
                </div>
                <h2 class="ul-auth-titulo">Painel Admin</h2>
                <p class="ul-auth-subtitulo">Acesse o painel de administração.</p>

                @if(session('error'))
                <div class="ul-auth-note">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                <div class="ul-auth-erro" style="display:block;">
                    @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form class="ul-painel-form" method="POST" action="{{ route('admin.login.post') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email da conta" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Palavra-passe</label>
                        <input type="password" id="password" name="password" placeholder="A sua palavra-passe" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="ul-btn w-100"><i class="bi bi-person"></i> Entrar</button>
                    </div>
                </form>

                <p class="ul-auth-alternativa"><a href="{{ route('home') }}">Voltar ao site</a></p>
            </div>
        </div>

        <!-- RODAPÉ SIMPLIFICADO -->
        <footer class="ul-painel-rodape">
            <span>© 2026 QNB-Imobiliária. Todos os direitos reservados.</span>
            <span><a href="{{ route('home') }}">Voltar ao site</a></span>
        </footer>
    </div>

    <!-- scripts -->
    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('painel-assets/js/painel-loader.js') }}"></script>
</body>

</html>
