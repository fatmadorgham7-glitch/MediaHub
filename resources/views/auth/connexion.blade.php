@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
    <h1>Connexion</h1>
    <form method="POST" action="{{ route('connexion') }}">
        @csrf
        <div style="margin-bottom:10px;">
            <label for="email">Email</label><br>
            <input id="email" type="email" name="email" value="{{ old('email') }}" style="width:100%;">
            @error('email') <span style="color:red;">{{ $message }}</span> @enderror
        </div>
        <div style="margin-bottom:10px;">
            <label for="password">Mot de passe</label><br>
            <input id="password" type="password" name="password" style="width:100%;">
            @error('password') <span style="color:red;">{{ $message }}</span> @enderror
        </div>
        <button type="submit">Se connecter</button>
    </form>
@endsection
