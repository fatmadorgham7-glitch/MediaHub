<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use App\Models\Categorie;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class CategorieController extends Controller
{
    public function index()
    {
        // On récupère les catégories avec le nombre de publications
        $liste = Cache::remember('categories.liste', 3600, function () {
            return Categorie::withCount('publications')->get();
        });
        $page = request()->integer('page', 1);
        $categories = new LengthAwarePaginator(
            $liste->forPage($page, 5)->values(),
            $liste->count(),
            5,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

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
        Cache::forget('categories.liste');

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
        Cache::forget('categories.liste');

        return redirect()->route('categories.index')->with('succes', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Categorie $categorie)
    {
        Gate::authorize('gerer-categories');
        // On garde la sécurité du Mini-Défi de la semaine 7
        if ($categorie->publications()->withTrashed()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('erreur', 'Impossible de supprimer cette catégorie car elle contient des publications.');
        }

        $categorie->delete();
        Cache::forget('categories.liste');

        return redirect()->route('categories.index')->with('succes', 'Catégorie supprimée avec succès.');
    }
}
