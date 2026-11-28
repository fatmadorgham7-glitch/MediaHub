@extends('layouts.app')

@section('titre', 'Accueil')

@section('contenu')
    <section class="home-hero">
        <div class="hero-copy">
            <p class="eyebrow">Le média, sous toutes ses formes</p>
            <h1>Vos contenus.<br><span>Votre espace.</span></h1>
            <p>Images, sons, vidéos et idées à partager : retrouvez vos publications dans un seul endroit, pensé pour les créateurs.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="{{ route('contenus.index') }}">
                    Explorer les contenus
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
                @auth
                    <a class="button button-secondary" href="{{ route('contenus.create') }}">Créer une publication</a>
                @else
                    <a class="button button-secondary" href="{{ route('inscription') }}">Nous rejoindre</a>
                @endauth
            </div>
            <div class="hero-note"><span aria-hidden="true"></span>Un lieu simple pour découvrir et publier.</div>
        </div>

        <div class="hero-mosaic" aria-hidden="true">
            <div class="mosaic-card mosaic-card-large">
                <span class="mosaic-label">IMAGE / COLLECTION</span>
                <span class="mosaic-index">01</span>
            </div>
            <div class="mosaic-card mosaic-card-small-top">
                <span class="mosaic-label">SON / VOIX</span>
                <span class="mosaic-index">02</span>
            </div>
            <div class="mosaic-card mosaic-card-small-bottom">
                <span class="mosaic-label">VIDÉO / MOMENT</span>
                <span class="mosaic-index">03</span>
            </div>
        </div>
    </section>

    <section class="feature-strip" aria-label="Formats pris en charge">
        <article class="feature-card">
            <span class="feature-index">01 — DÉCOUVRIR</span>
            <h2>Un fil de contenus vivant</h2>
            <p>Parcourez les publications et explorez par catégorie.</p>
        </article>
        <article class="feature-card">
            <span class="feature-index">02 — PUBLIER</span>
            <h2>Vos médias, vos mots</h2>
            <p>Ajoutez une image ou un média audio/vidéo à vos publications.</p>
        </article>
        <article class="feature-card">
            <span class="feature-index">03 — GARDER</span>
            <h2>Documents privés</h2>
            <p>Les documents associés restent réservés à leur auteur et aux administrateurs.</p>
        </article>
    </section>
@endsection