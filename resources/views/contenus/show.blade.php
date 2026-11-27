@extends('layouts.app')

@section('titre', $publication->titre)

@section('contenu')
    <article>
        <h1>{{ $publication->titre }}</h1>
        <p>
            Publié par <strong>{{ $publication->auteur->name }}</strong> 
            dans la catégorie <strong>{{ $publication->categorie->nom }}</strong>
        </p>
        
        <div class="contenu-texte">
            {{ $publication->contenu }}
        </div>

        @if ($publication->image)
            <img src="{{ Storage::url($publication->image) }}" alt="{{ $publication->titre }}" style="max-width:100%;">
        @endif

        @if ($publication->type_media === 'audio')
            <audio controls src="{{ Storage::url($publication->fichier_media) }}"></audio>
        @elseif ($publication->type_media === 'video')
            <video controls src="{{ Storage::url($publication->fichier_media) }}" style="max-width:100%;"></video>
        @endif
    </article>

    @can('update', $publication)
        <p><a href="{{ route('contenus.edit', $publication) }}">Modifier cette publication</a></p>
    @endcan

    <hr>

    <section class="commentaires">
        <h2>Commentaires ({{ $publication->commentaires->count() }})</h2>

        @forelse ($publication->commentaires as $commentaire)
            <div class="commentaire" style="margin-bottom: 15px;">
                <p><strong>{{ $commentaire->auteur->name }}</strong> a écrit :</p>
                <p>{{ $commentaire->contenu }}</p>
            </div>
        @empty
            <p>Aucun commentaire pour le moment.</p>
        @endforelse
    </section>
@endsection
