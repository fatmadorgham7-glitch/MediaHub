@extends('layouts.app')

@section('titre', 'Contenus')

@section('contenu')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Le catalogue</p>
            <h1>Contenus <span>multimédias</span></h1>
            <p class="page-intro">Des publications à parcourir, à écouter et à regarder.</p>
        </div>
        @auth
            <a class="button button-primary" href="{{ route('contenus.create') }}">+ Nouvelle publication</a>
        @else
            <a class="button button-secondary" href="{{ route('connexion') }}">Se connecter pour publier</a>
        @endauth
    </section>

    <div class="grille">
        @forelse ($contenus as $contenu)
            <x-carte
                :titre="$contenu->titre"
                :image="$contenu->image"
                :fichier-media="$contenu->fichier_media"
                :type-media="$contenu->type_media"
            >
                <div class="card-meta">
                    <span>{{ $contenu->categorie->nom }}</span>
                    <span>{{ $contenu->auteur->name }}</span>
                </div>
                <div class="card-actions">
                    <a class="button button-secondary button-small" href="{{ route('contenus.show', $contenu) }}">Voir</a>
                    @can('update', $contenu)
                        <a class="button button-secondary button-small" href="{{ route('contenus.edit', $contenu) }}">Modifier</a>
                    @endcan
                    @can('delete', $contenu)
                        <form action="{{ route('contenus.destroy', $contenu) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="button button-danger button-small" type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')">
                                Supprimer
                            </button>
                        </form>
                    @endcan
                </div>
            </x-carte>
        @empty
            <div class="empty-state">
                <h2>Le catalogue attend son premier contenu.</h2>
                <p>Aucun contenu disponible pour le moment.</p>
                @auth
                    <a class="button button-primary" href="{{ route('contenus.create') }}">Créer une publication</a>
                @endauth
            </div>
        @endforelse
    </div>

    <div class="pagination-shell">
        {{ $contenus->links() }}
    </div>
@endsection