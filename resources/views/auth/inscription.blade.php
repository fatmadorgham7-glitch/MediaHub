@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
    <section class="auth-layout">
        <div class="auth-aside">
            <div>
                <p class="eyebrow">Bienvenue dans le hub</p>
                <h2>Faites une place à <span>vos idées.</span></h2>
                <p>Créez un compte pour publier, organiser et partager vos contenus.</p>
            </div>
            <span class="auth-aside-note">Images · sons · vidéos</span>
        </div>
        <div class="auth-card">
            <p class="eyebrow">Inscription</p>
            <h1>Créer un compte</h1>
            <p>Quelques informations et vous êtes prêt.</p>
            <form method="POST" action="{{ route('inscription') }}">
                @csrf
                <div class="form-field">
                    <label for="name">Nom</label>
                    <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" placeholder="Votre nom" required>
                    @error('name') <span class="form-error" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="form-field">
                    <label for="email">Adresse e-mail</label>
                    <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="vous@exemple.fr" required>
                    @error('email') <span class="form-error" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="form-field">
                    <label for="password">Mot de passe</label>
                    <input class="form-control" id="password" type="password" name="password" autocomplete="new-password" placeholder="Choisissez un mot de passe" required>
                    @error('password') <span class="form-error" role="alert">{{ $message }}</span> @enderror
                </div>
                <div class="form-field">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Saisissez-le à nouveau" required>
                </div>
                <button class="button button-primary button-block" type="submit">Créer mon compte</button>
            </form>
            <p class="auth-switch">Déjà membre ? <a href="{{ route('connexion') }}">Se connecter</a></p>
        </div>
    </section>
@endsection
