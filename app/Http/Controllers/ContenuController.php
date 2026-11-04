<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Categorie;

class ContenuController extends Controller
{
    /**
     * Afficher la liste des publications (INDEX)
     */
    public function index()
    {
        $contenus = Publication::with(['categorie', 'auteur'])
            ->latest()
            ->paginate(9);

        return view('contenus.index', [
            'contenus' => $contenus,
            'tri' => 'Du plus récent au plus ancien'
        ]);
    }

    /**
     * Afficher le formulaire de création (CREATE)
     */
    public function create()
    {
        $categories = Categorie::all();
        return view('contenus.create', compact('categories'));
    }

    /**
     * Enregistrer une nouvelle publication (STORE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|max:255|min:5',
            'contenu' => 'required|min:10',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        Publication::create([
            'titre' => $request->input('titre'),
            'contenu' => $request->input('contenu'),
            'categorie_id' => $request->input('categorie_id'),
            'user_id' => 1, // Provisoire en attendant la semaine 8
        ]);

        return redirect()->route('contenus.index')->with('succes', 'Publication créée avec succès.');
    }

    /**
     * Afficher une publication spécifique (SHOW)
     */
    public function show(Publication $publication)
    {
        $publication->load(['categorie', 'auteur', 'commentaires.auteur']);
        return view('contenus.show', compact('publication'));
    }

    /**
     * Afficher le formulaire de modification (EDIT)
     */
    public function edit(Publication $publication)
    {
        $categories = Categorie::all();
        return view('contenus.edit', compact('publication', 'categories'));
    }

    /**
     * Mettre à jour une publication (UPDATE)
     */
    public function update(Request $request, Publication $publication)
    {
        $request->validate([
            'titre' => 'required|max:255|min:5',
            'contenu' => 'required|min:10',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        $publication->update($request->only('titre', 'contenu', 'categorie_id'));

        return redirect()->route('contenus.show', $publication)->with('succes', 'Publication mise à jour avec succès.');
    }

    /**
     * Supprimer une publication (DESTROY)
     */
    public function destroy(Publication $publication)
    {
        $publication->delete(); // C'est un soft delete grâce au trait dans le modèle
        return redirect()->route('contenus.index')->with('succes', 'Publication supprimée avec succès.');
    }
}