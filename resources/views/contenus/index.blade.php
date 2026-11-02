@extends('layouts.app')

@section('titre', 'Contenus')

@section('contenu')
    <h1>Contenus multimédias</h1>
    <p>Tri : {{ $tri }}</p>

    @forelse ($contenus as $contenu)
    <x-carte :titre="$contenu->titre">
        <p>{{ $contenu->description }}</p>
    </x-carte>
@empty
    <p>Aucun contenu disponible pour le moment.</p>
@endforelse
@endsection