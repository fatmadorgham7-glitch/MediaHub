@extends('layouts.app')

@section('titre', 'Créer une publication')

@section('contenu')
    <h1>Créer une publication</h1>

    <form method="POST" action="{{ route('contenus.store') }}" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom: 10px;">
            <label>Titre :</label><br>
            <input type="text" name="titre" value="{{ old('titre') }}" style="width: 100%;">
            @error('titre') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label>Catégorie :</label><br>
            <select name="categorie_id" style="width: 100%;">
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->id }}" @selected(old('categorie_id') == $categorie->id)>
                        {{ $categorie->nom }}
                    </option>
                @endforeach
            </select>
            @error('categorie_id') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label>Contenu :</label><br>
            <textarea name="contenu" rows="5" style="width: 100%;">{{ old('contenu') }}</textarea>
            @error('contenu') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label>Image :</label><br>
            <input type="file" name="image" accept="image/*">
            @error('image') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 10px;">
            <label>Fichier audio ou vidéo :</label><br>
            <input type="file" name="fichier_media" accept="audio/*,video/*">
            @error('fichier_media') <span style="color:red;">{{ $message }}</span> @enderror
        </div>

        <button type="submit" style="padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 5px;">
            Enregistrer
        </button>
        <a href="{{ route('contenus.index') }}">Annuler</a>
    </form>
@endsection