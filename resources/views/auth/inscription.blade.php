@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
    <h1>Inscription</h1>
    <form method="POST" action="{{ route('inscription') }}">
        @csrf
        <div style="margin-bottom:10px;">
            <label for="name">Nom</label><br>
            <input id="name" type="text" name="name" value="{{ old('name') }}" style="width:100%;">
            @error('name') <span style="color:red;">{{ $message }}</span> @enderror
        </div>
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
        <div style="margin-bottom:10px;">
            <label for="password_confirmation">Confirmation</label><br>
            <input id="password_confirmation" type="password" name="password_confirmation" style="width:100%;">
        </div>
        <button type="submit">Créer mon compte</button>
    </form>
@endsection
