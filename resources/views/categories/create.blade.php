@extends('layouts.app')
@section('titre', 'Créer une catégorie')
@section('contenu')
    <h1>Créer une catégorie</h1>
    <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        <div><label>Nom</label><br><input name="nom" value="{{ old('nom') }}" style="width:100%;">
            @error('nom') <span style="color:red;">{{ $message }}</span> @enderror</div>
        <div style="margin-top:10px;"><label>Slug</label><br><input name="slug" value="{{ old('slug') }}" style="width:100%;">
            @error('slug') <span style="color:red;">{{ $message }}</span> @enderror</div>
        <button type="submit" style="margin-top:10px;">Créer</button>
    </form>
@endsection
