@extends('layouts.app')

@section('titre', $publication->titre)

@section('contenu')
    <article class="detail-panel">
        <header class="detail-heading">
            <p class="eyebrow">Publication</p>
            <h1>{{ $publication->titre }}</h1>
            <div class="detail-meta">
                <span>Par {{ $publication->auteur->name }}</span>
                <span>{{ $publication->categorie->nom }}</span>
            </div>
        </header>

        <div class="detail-content">
            {{ $publication->contenu }}
        </div>

        @if ($publication->image)
            <img class="detail-image" src="{{ Storage::url($publication->image) }}" alt="{{ $publication->titre }}">
        @endif

        @if ($publication->type_media === 'audio' && $publication->fichier_media)
            <audio controls class="audio-player" src="{{ Storage::url($publication->fichier_media) }}"></audio>
        @elseif ($publication->type_media === 'video' && $publication->fichier_media)
            <video class="video-player" controls src="{{ Storage::url($publication->fichier_media) }}"></video>
        @endif

        <div class="detail-actions">
            <a class="button button-secondary" href="{{ route('contenus.index') }}">Retour aux publications</a>
            @can('update', $publication)
                <a class="button button-primary" href="{{ route('contenus.edit', $publication) }}">Modifier cette publication</a>
            @endcan
        </div>

        @auth
            @can('viewDocument', $publication)
                @if ($publication->document)
                    <p><a class="text-link" href="{{ route('contenus.document', $publication) }}">Télécharger le document privé</a></p>
                @endif
            @endcan
        @endauth

        <section class="commentaires">
            <h2>Commentaires ({{ $publication->commentaires->count() }})</h2>

            @forelse ($publication->commentaires as $commentaire)
                <article class="commentaire">
                    <p><strong>{{ $commentaire->auteur->name }}</strong> a écrit :</p>
                    <p>{{ $commentaire->contenu }}</p>
                </article>
            @empty
                <p>Aucun commentaire pour le moment.</p>
            @endforelse
        </section>
    </article>
@endsection
