@extends('layouts.app')

@section('titre', 'Catégories')

@section('contenu')
    <h1>Catégories</h1>
    @can('gerer-categories')
        <p><a href="{{ route('categories.create') }}">+ Nouvelle catégorie</a></p>
    @endcan
    @forelse ($categories as $categorie)
        <div style="border:1px solid #ccc;padding:15px;margin-bottom:10px;">
            <strong>{{ $categorie->nom }}</strong>
            ({{ $categorie->publications_count }} publication(s))
            @can('gerer-categories')
                | <a href="{{ route('categories.edit', $categorie) }}">Modifier</a>
                <form action="{{ route('categories.destroy', $categorie) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</button>
                </form>
            @endcan
        </div>
    @empty
        <p>Aucune catégorie disponible.</p>
    @endforelse
@endsection
