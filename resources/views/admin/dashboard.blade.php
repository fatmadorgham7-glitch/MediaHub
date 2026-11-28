@extends('layouts.app')

@section('titre', 'Administration')

@section('contenu')
    <section class="page-heading">
        <div>
            <p class="eyebrow">MediaHub · Administration</p>
            <h1>Tableau de bord administrateur</h1>
            <p class="page-intro">Les chiffres clés de votre plateforme.</p>
        </div>
    </section>

    <section class="stats-grid" aria-label="Statistiques de la plateforme">
        <article class="stat-card">
            <p class="stat-label">Publications</p>
            <p class="stat-number">{{ $nombrePublications }}</p>
            <a class="text-link" href="{{ route('contenus.index') }}">Parcourir les contenus</a>
        </article>
        <article class="stat-card">
            <p class="stat-label">Catégories</p>
            <p class="stat-number">{{ $nombreCategories }}</p>
            <a class="text-link" href="{{ route('categories.index') }}">Gérer les catégories</a>
        </article>
        <article class="stat-card">
            <p class="stat-label">Utilisateurs</p>
            <p class="stat-number">{{ $nombreUtilisateurs }}</p>
            <span class="stat-note">Comptes inscrits sur MediaHub</span>
        </article>
    </section>
@endsection
