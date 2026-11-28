@extends('layouts.app')

@section('titre', 'À propos')
@section('meta')
    <meta name="description" content="Découvrez MediaHub, un espace pour publier et parcourir des contenus multimédias.">
@endsection
@section('contenu')
    <section class="about-panel">
        <p class="eyebrow">À propos</p>
        <h1>Un espace commun pour les idées qui prennent plusieurs formes.</h1>
        <p>MediaHub réunit publications, images, sons et vidéos dans une expérience simple à parcourir. Les créateurs gardent la main sur leurs contenus, tandis que les catégories aident chacun à trouver ce qui l'intéresse.</p>
        <div class="about-values">
            <div class="about-value">
                <strong>Découvrir</strong>
                <span>Un catalogue clair, organisé par catégories.</span>
            </div>
            <div class="about-value">
                <strong>Créer</strong>
                <span>Publiez du texte accompagné de vos médias.</span>
            </div>
            <div class="about-value">
                <strong>Partager avec soin</strong>
                <span>Les documents privés restent protégés.</span>
            </div>
        </div>
    </section>
@endsection