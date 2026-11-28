<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0e0c">
    <title>@yield('titre', 'MediaHub') — MediaHub</title>
    @yield('meta')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <div class="app-shell">
        @include('partials.navigation')

        <div class="app-workspace">
            <header class="workspace-topbar">
                <div class="breadcrumbs">
                    <span class="breadcrumb-dot" aria-hidden="true"></span>
                    <span>MediaHub</span>
                    <span aria-hidden="true">/</span>
                    <strong>@yield('titre', 'Accueil')</strong>
                </div>
                <a class="topbar-link" href="{{ route('contenus.index') }}">
                    Explorer les contenus
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
                </a>
            </header>

            <main class="main-content">
                <div class="conteneur">
                    @if (session('succes'))
                        <div class="alert alert-success" role="status">{{ session('succes') }}</div>
                    @endif

                    @if (session('erreur'))
                        <div class="alert alert-danger" role="alert">{{ session('erreur') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-validation" role="alert">
                            <strong>Quelques points sont à corriger :</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('contenu')
                </div>
            </main>

            @include('partials.pied-de-page')
        </div>
    </div>
</body>
</html>