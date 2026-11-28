<aside class="sidebar" aria-label="Navigation principale">
    <a class="brand" href="{{ route('accueil') }}" aria-label="MediaHub, accueil">
        <span class="brand-mark" aria-hidden="true">m</span>
        <span class="brand-word">media<span>hub</span></span>
    </a>

    <p class="sidebar-label">Votre espace</p>
    <nav class="sidebar-nav">
        <a href="{{ route('accueil') }}" @class(['nav-link', 'is-active' => request()->routeIs('accueil')]) @if (request()->routeIs('accueil')) aria-current="page" @endif>
            <span class="nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg>
            </span>
            Accueil
        </a>
        <a href="{{ route('contenus.index') }}" @class(['nav-link', 'is-active' => request()->routeIs('contenus.*')]) @if (request()->routeIs('contenus.*')) aria-current="page" @endif>
            <span class="nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="m10 9 5 3-5 3z"/></svg>
            </span>
            Publications
        </a>
        <a href="{{ route('categories.index') }}" @class(['nav-link', 'is-active' => request()->routeIs('categories.*')]) @if (request()->routeIs('categories.*')) aria-current="page" @endif>
            <span class="nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="5" rx="2"/><rect x="13" y="10" width="8" height="11" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/></svg>
            </span>
            Catégories
        </a>
        <a href="{{ route('a-propos') }}" @class(['nav-link', 'is-active' => request()->routeIs('a-propos')]) @if (request()->routeIs('a-propos')) aria-current="page" @endif>
            <span class="nav-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg>
            </span>
            À propos
        </a>
        @auth
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" @class(['nav-link', 'is-active' => request()->routeIs('admin.*')]) @if (request()->routeIs('admin.*')) aria-current="page" @endif>
                    <span class="nav-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M4 19V9m8 10V5m8 14v-7"/><path d="M2 19h20"/></svg>
                    </span>
                    Administration
                </a>
            @endif
        @else
            <a class="nav-link mobile-auth-link" href="{{ route('connexion') }}">Se connecter</a>
            <a class="nav-link mobile-auth-link" href="{{ route('inscription') }}">Créer un compte</a>
        @endauth
    </nav>

    <div class="sidebar-spacer"></div>
    <div class="sidebar-divider"></div>

    @auth
        <div class="sidebar-profile">
            <span class="profile-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <div class="profile-copy">
                <span class="profile-name">{{ auth()->user()->name }}</span>
                <span class="profile-role">{{ auth()->user()->role === 'admin' ? 'Administrateur' : 'Créateur' }}</span>
            </div>
            <form action="{{ route('deconnexion') }}" method="POST">
                @csrf
                <button class="logout-button" type="submit" aria-label="Se déconnecter">Sortir</button>
            </form>
        </div>
    @else
        <div class="guest-card">
            <strong>Un espace à vous</strong>
            <p>Connectez-vous pour publier et partager vos contenus.</p>
            <a class="button button-primary" href="{{ route('connexion') }}">Se connecter</a>
        </div>
    @endauth
</aside>