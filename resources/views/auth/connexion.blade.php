@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
    <section class="auth-layout">
        <div class="auth-aside">
            <div>
                <p class="eyebrow">Ravi de vous retrouver</p>
                <h2>Reprenez le fil de <span>vos découvertes.</span></h2>
                <p>Vos publications et vos médias vous attendent.</p>
            </div>
            <span class="auth-aside-note">MediaHub · votre espace multimédia</span>
        </div>
        <div class="auth-card">
            <p class="eyebrow">Connexion</p>
            <h1>Bon retour.</h1>
            <p>Connectez-vous pour continuer.</p>
            <form method="POST" action="{{ route('connexion') }}">
                @csrf
                <div class="form-field">
                    <label for="email">Adresse e-mail</label>
                    <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="vous@exemple.fr" required>
                    @error('email') <span class="form-error" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="form-field">
                    <label for="password">Mot de passe</label>
                    <input class="form-control" id="password" type="password" name="password" autocomplete="current-password" placeholder="Votre mot de passe" required>
                    @error('password') <span class="form-error" role="alert">{{ $message }}</span> @enderror
                </div>
                <button class="button button-primary button-block" type="submit">Se connecter</button>
            </form>
            <p class="auth-switch">Nouveau sur MediaHub ? <a href="{{ route('inscription') }}">Créer un compte</a></p>
        </div>
    </section>
@endsection
