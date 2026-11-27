<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.inscription');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $utilisateur = User::create($request->validated());
        Auth::login($utilisateur);

        return redirect()->route('accueil')
            ->with('succes', 'Bienvenue sur MediaHub.');
    }

    public function creerConnexion(): View
    {
        return view('auth.connexion');
    }

    public function connecter(Request $request): RedirectResponse
    {
        $identifiants = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($identifiants)) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended(route('accueil'));
    }

    public function deconnecter(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('accueil');
    }
}
