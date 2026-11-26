<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\Categorie;
use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;

class ContenuController extends Controller
{
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

    public function create()
    {
        $categories = Categorie::all();
        return view('contenus.create', compact('categories'));
    }

    public function store(StorePublicationRequest $request)
    {
        // La validation est gérée par StorePublicationRequest
        Publication::create(array_merge($request->validated(), [
            'user_id' => 1 // Provisoire en attendant la semaine 8
        ]));

        return redirect()->route('contenus.index')->with('succes', 'Publication créée avec succès.');
    }

    public function show(Publication $publication)
    {
        $publication->load(['categorie', 'auteur', 'commentaires.auteur']);
        return view('contenus.show', compact('publication'));
    }

    public function edit(Publication $publication)
    {
        $categories = Categorie::all();
        return view('contenus.edit', compact('publication', 'categories'));
    }

    public function update(UpdatePublicationRequest $request, Publication $publication)
    {
        // La validation est gérée par UpdatePublicationRequest
        $publication->update($request->validated());

        return redirect()->route('contenus.show', $publication)->with('succes', 'Publication mise à jour avec succès.');
    }

    public function destroy(Publication $publication)
    {
        $publication->delete(); 
        return redirect()->route('contenus.index')->with('succes', 'Publication supprimée avec succès.');
    }
}