@extends('layouts.app')
@section('titre', 'Modifier une catégorie')
@section('contenu')
    <section class="page-heading">
        <div>
            <p class="eyebrow">Administration</p>
            <h1>Modifier une catégorie</h1>
            <p class="page-intro">Mettez à jour le nom ou l'identifiant de cette catégorie.</p>
        </div>
    </section>
    <form class="form-panel category-form" method="POST" action="{{ route('categories.update', $categorie) }}">
        @csrf
        @method('PUT')
        <div class="form-field">
            <label for="nom">Nom</label>
            <input class="form-control" id="nom" name="nom" value="{{ old('nom', $categorie->nom) }}" minlength="3" maxlength="255" required>
            @error('nom') <span class="form-error" role="alert">{{ $message }}</span> @enderror
        </div>
        <div class="form-field">
            <label for="slug">Slug</label>
            <input class="form-control" id="slug" name="slug" value="{{ old('slug', $categorie->slug) }}" maxlength="255" required>
            <p class="form-help">Identifiant lisible utilisé dans les URL.</p>
            @error('slug') <span class="form-error" role="alert">{{ $message }}</span> @enderror
        </div>
        <div class="form-actions">
            <a class="button button-secondary" href="{{ route('categories.index') }}">Annuler</a>
            <button class="button button-primary" type="submit">Enregistrer les modifications</button>
        </div>
    </form>
@endsection
