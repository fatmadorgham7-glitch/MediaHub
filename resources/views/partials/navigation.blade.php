<nav class="navigation">
    <a href="{{ route('accueil') }}">MediaHub</a>
    <a href="{{ route('contenus.index') }}">Contenus</a>
    <a href="{{ route('a-propos') }}">À propos</a>
    <a href="{{ route('categories.index') }}">Catégories</a>
    @guest
        <a href="{{ route('connexion') }}">Connexion</a>
        <a href="{{ route('inscription') }}">Inscription</a>
    @endguest
    @auth
        <span>{{ auth()->user()->name }}</span>
        <form action="{{ route('deconnexion') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit">Déconnexion</button>
        </form>
    @endauth
</nav>