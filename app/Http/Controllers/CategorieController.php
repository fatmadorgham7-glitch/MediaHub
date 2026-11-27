<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;

class CategorieController extends Controller
{
    public function index()
    {
        // On récupère les catégories avec le nombre de publications
        $categories = Categorie::withCount('publications')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        Gate::authorize('gerer-categories');
        return view('categories.create');
    }

    public function store(StoreCategorieRequest $request)
    {
        Gate::authorize('gerer-categories');
        // Plus besoin de valider manuellement, la FormRequest le fait automatiquement !
        Categorie::create($request->validated());
        
        return redirect()->route('categories.index')->with('succes', 'Catégorie créée avec succès.');
    }

    public function edit(Categorie $categorie)
    {
        Gate::authorize('gerer-categories');
        return view('categories.edit', compact('categorie'));
    }

    public function update(UpdateCategorieRequest $request, Categorie $categorie)
    {
        Gate::authorize('gerer-categories');
        // La FormRequest UpdateCategorieRequest gère la validation
        $categorie->update($request->validated());
        
        return redirect()->route('categories.index')->with('succes', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Categorie $categorie)
    {
        Gate::authorize('gerer-categories');
        // On garde la sécurité du Mini-Défi de la semaine 7
        if ($categorie->publications()->count() > 0) {
            return redirect()
                ->route('categories.index')
                ->with('erreur', 'Impossible de supprimer cette catégorie car elle contient des publications.');
        }

        $categorie->delete();
        
        return redirect()->route('categories.index')->with('succes', 'Catégorie supprimée avec succès.');
    }
}
