@extends('layouts.app')

@section('titre', 'Contenus')

@section('contenu')
    <h1>Contenus multimédias</h1>

    <!-- Le fameux bouton de création -->
    <p>
        <a href="{{ route('contenus.create') }}" style="display: inline-block; padding: 10px; background-color: #3490dc; color: white; text-decoration: none; border-radius: 5px;">
            + Nouvelle publication
        </a>
    </p>

    <div class="grille">
        @forelse ($contenus as $contenu)
            <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                <h2>{{ $contenu->titre }}</h2>
                <p>Catégorie : {{ $contenu->categorie->nom }}</p>
                <p>Auteur : {{ $contenu->auteur->name }}</p>
                
                <!-- Les boutons d'actions -->
                <div style="margin-top: 10px;">
                    <a href="{{ route('contenus.show', $contenu) }}">Voir</a> | 
                    <a href="{{ route('contenus.edit', $contenu) }}">Modifier</a> | 
                    
                    <form action="{{ route('contenus.destroy', $contenu) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')" style="background: none; border: none; color: red; cursor: pointer; text-decoration: underline;">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p>Aucun contenu disponible pour le moment.</p>
        @endforelse
    </div>

    <!-- La pagination -->
    <div style="margin-top: 20px;">
        {{ $contenus->links() }}
    </div>
@endsection