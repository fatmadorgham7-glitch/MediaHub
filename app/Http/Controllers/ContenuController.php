<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Models\Categorie;
use App\Models\Publication;
use App\Services\PublicationMediaStorage;

class ContenuController extends Controller
{
    public function index()
    {
        $contenus = Publication::with(['categorie', 'auteur'])
            ->withCount('commentaires')
            ->latest()
            ->paginate(9);

        return view('contenus.index', [
            'contenus' => $contenus,
            'tri' => 'Du plus récent au plus ancien',
        ]);
    }

    public function create()
    {
        $categories = Categorie::all();

        return view('contenus.create', compact('categories'));
    }

    public function store(StorePublicationRequest $request, PublicationMediaStorage $mediaStorage)
    {
        $donnees = $request->validated();
        $fichiers = $mediaStorage->storeFromRequest($request);
        $donnees['user_id'] = auth()->id();
        $enregistre = false;

        try {
            Publication::create(array_merge($donnees, $fichiers));
            $enregistre = true;
        } finally {
            if (! $enregistre) {
                $mediaStorage->delete($fichiers);
            }
        }

        return redirect()->route('contenus.index')->with('succes', 'Publication créée avec succès.');
    }

    public function show(Publication $publication)
    {
        $publication->load(['categorie', 'auteur', 'commentaires.auteur']);

        return view('contenus.show', compact('publication'));
    }

    public function edit(Publication $publication)
    {
        $this->authorize('update', $publication);
        $categories = Categorie::all();

        return view('contenus.edit', compact('publication', 'categories'));
    }

    public function update(
        UpdatePublicationRequest $request,
        Publication $publication,
        PublicationMediaStorage $mediaStorage
    ) {
        $this->authorize('update', $publication);
        $donnees = $request->validated();
        $fichiers = $mediaStorage->storeFromRequest($request);
        $miseAJour = $mediaStorage->prepareUpdate($request, $publication, $donnees, $fichiers);
        $enregistre = false;

        try {
            if (! $publication->update($miseAJour['attributes'])) {
                throw new \RuntimeException('Impossible d’enregistrer la publication.');
            }

            $enregistre = true;
        } finally {
            if (! $enregistre) {
                $mediaStorage->delete($fichiers);
            }
        }

        $mediaStorage->delete($miseAJour['old_files']);

        return redirect()->route('contenus.show', $publication)->with('succes', 'Publication mise à jour avec succès.');
    }

    public function destroy(Publication $publication)
    {
        $this->authorize('delete', $publication);
        $publication->delete();

        return redirect()->route('contenus.index')->with('succes', 'Publication supprimée avec succès.');
    }
}
