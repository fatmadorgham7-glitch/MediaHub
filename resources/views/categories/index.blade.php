@extends('layouts.app')

@section('titre', 'Catégories')

@section('contenu')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Explorer</p>
            <h1>Les <span>catégories</span></h1>
            <p class="page-intro">Des univers pour retrouver les publications qui vous intéressent.</p>
        </div>
        @can('gerer-categories')
            <a class="button button-primary" href="{{ route('categories.create') }}">+ Nouvelle catégorie</a>
        @endcan
    </section>

    <div class="category-list">
        @forelse ($categories as $categorie)
            <article class="category-row">
                <div class="category-info">
                    <span class="category-symbol" aria-hidden="true">{{ mb_strtoupper(mb_substr($categorie->nom, 0, 1)) }}</span>
                    <div>
                        <strong>{{ $categorie->nom }}</strong>
                        <small>{{ $categorie->publications_count }} publication(s)</small>
                    </div>
                </div>
                @can('gerer-categories')
                    <div class="category-actions">
                        <a class="button button-secondary button-small" href="{{ route('categories.edit', $categorie) }}">Modifier</a>
                        <form action="{{ route('categories.destroy', $categorie) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="button button-danger button-small" type="submit" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</button>
                        </form>
                    </div>
                @endcan
            </article>
        @empty
            <div class="empty-state">
                <h2>Aucune catégorie pour le moment.</h2>
                @can('gerer-categories')
                    <a class="button button-primary" href="{{ route('categories.create') }}">Créer une catégorie</a>
                @endcan
            </div>
        @endforelse
    </div>
    <div class="pagination-shell">{{ $categories->links() }}</div>
@endsection
