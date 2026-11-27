@extends('layouts.app')

@section('titre', 'Administration')

@section('contenu')
    <h1>Tableau de bord administrateur</h1>

    <dl>
        <dt>Publications</dt>
        <dd>{{ $nombrePublications }}</dd>

        <dt>Catégories</dt>
        <dd>{{ $nombreCategories }}</dd>

        <dt>Utilisateurs</dt>
        <dd>{{ $nombreUtilisateurs }}</dd>
    </dl>
@endsection
