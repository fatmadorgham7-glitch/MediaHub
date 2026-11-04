<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        return 'Liste des catégories';
    }
        public function store(Request $request)
    {
        // 1. On valide les données
        $request->validate([
            'nom' => 'required|max:255|min:3',
            'slug' => 'required|max:255|unique:categories,slug', // Le slug doit être unique
        ]);

        Categorie::create($request->only('nom', 'slug'));
        
        return redirect()->route('categories.index')->with('succes', 'Catégorie créée avec succès.');
    }

    public function update(Request $request, Categorie $categorie)
    {
        // 1. On valide les données (on ignore le slug actuel pour la règle unique)
        $request->validate([
            'nom' => 'required|max:255|min:3',
            'slug' => 'required|max:255|unique:categories,slug,' . $categorie->id,
        ]);

        $categorie->update($request->only('nom', 'slug'));
        
        return redirect()->route('categories.index')->with('succes', 'Catégorie mise à jour avec succès.');
    }
}
