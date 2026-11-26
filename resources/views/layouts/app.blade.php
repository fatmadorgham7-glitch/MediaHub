<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titre', 'MediaHub')</title>@yield('meta')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('partials.navigation')

    <main class="conteneur">
            
        
        <!-- 1. Message de succès (bouton vert) -->
        @if (session('succes'))
            <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
                {{ session('succes') }}
            </div>
        @endif

        <!-- 2. Message d'erreur personnalisé (ex: suppression impossible) -->
        @if (session('erreur'))
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">
                {{ session('erreur') }}
            </div>
        @endif

        <!-- 3. Erreurs de validation du formulaire (champs vides, etc.) -->
        @if ($errors->any())
            <div style="background-color: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ffeeba;">
                <strong>Attention, veuillez corriger les erreurs suivantes :</strong>
                <ul style="margin-top: 10px; margin-bottom: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('contenu')
    </main>
  

    @include('partials.pied-de-page')
</body>
</html>