@extends('layouts.app')
@section('titre', 'Modifier une catégorie')
@section('contenu')
    <h1>Modifier une catégorie</h1>
    <form method="POST" action="{{ route('categories.update', $categorie) }}">
        @csrf
        @method('PUT')
        <div><label>Nom</label><br><input name="nom" value="{{ old('nom', $categorie->nom) }}" style="width:100%;">
            @error('nom') <span style="color:red;">{{ $message }}</span> @enderror</div>
        <div style="margin-top:10px;"><label>Slug</label><br><input name="slug" value="{{ old('slug', $categorie->slug) }}" style="width:100%;">
            @error('slug') <span style="color:red;">{{ $message }}</span> @enderror</div>
        <button type="submit" style="margin-top:10px;">Mettre à jour</button>
    </form>
@endsection
