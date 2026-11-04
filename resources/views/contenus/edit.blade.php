@extends('layouts.app')

@section('titre', 'Modifier une publication')

@section('contenu')
    <h1>Modifier la publication</h1>

    <form method="POST" action="{{ route('contenus.update', $publication) }}">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 10px;">
            <label>Titre :</label><br>
            <input type="text" name="titre" value="{{ old('titre', $publication->titre) }}" style="width: 100%;">
            @error('titre') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label>Catégorie :</label><br>
            <select name="categorie_id" style="width: 100%;">
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->id }}" {{ $publication->categorie_id == $categorie->id ? 'selected' : '' }}>
                        {{ $categorie->nom }}
                    </option>
                @endforeach
            </select>
            @error('categorie_id') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label>Contenu :</label><br>
            <textarea name="contenu" rows="5" style="width: 100%;">{{ old('contenu', $publication->contenu) }}</textarea>
            @error('contenu') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <button type="submit" style="padding: 10px; background-color: #3490dc; color: white; border: none; border-radius: 5px;">
            Mettre à jour
        </button>
        <a href="{{ route('contenus.show', $publication) }}">Annuler</a>
    </form>
@endsection
