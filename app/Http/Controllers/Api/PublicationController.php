<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Http\Resources\PublicationResource;
use App\Models\Publication;
use App\Services\PublicationMediaStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $query = Publication::with(['categorie', 'auteur'])
            ->withCount('commentaires')
            ->latest();

        if (request()->filled('categorie_id')) {
            $query->where('categorie_id', request()->integer('categorie_id'));
        }

        return PublicationResource::collection($query->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StorePublicationRequest $request,
        PublicationMediaStorage $mediaStorage
    ): JsonResponse {
        $fichiers = $mediaStorage->storeFromRequest($request);
        $enregistre = false;

        try {
            $publication = Publication::create(array_merge(
                $request->validated(),
                $fichiers,
                ['user_id' => $request->user()->id]
            ));

            $enregistre = true;
        } finally {
            if (! $enregistre) {
                $mediaStorage->delete($mediaStorage->paths($fichiers));
            }
        }

        return (new PublicationResource($publication->load(['categorie', 'auteur'])->loadCount('commentaires')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Publication $publication): PublicationResource
    {
        return new PublicationResource(
            $publication->load(['categorie', 'auteur'])->loadCount('commentaires')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdatePublicationRequest $request,
        Publication $publication,
        PublicationMediaStorage $mediaStorage
    ): PublicationResource {
        $this->authorize('update', $publication);
        $donnees = $request->validated();
        $fichiers = $mediaStorage->storeFromRequest($request);
        $anciensFichiers = [];

        foreach (['image', 'fichier_media'] as $champ) {
            $remplaceOuSupprime = isset($fichiers[$champ])
                || (array_key_exists($champ, $donnees) && $donnees[$champ] === null);

            if ($remplaceOuSupprime && $publication->{$champ}) {
                $anciensFichiers[] = $publication->{$champ};
            }
        }

        if (array_key_exists('fichier_media', $donnees) && $donnees['fichier_media'] === null) {
            $donnees['type_media'] = null;
        }

        $enregistre = false;

        try {
            if (! $publication->update(array_merge($donnees, $fichiers))) {
                throw new \RuntimeException('Impossible d’enregistrer la publication.');
            }

            $enregistre = true;
        } finally {
            if (! $enregistre) {
                $mediaStorage->delete($mediaStorage->paths($fichiers));
            }
        }

        $mediaStorage->delete($anciensFichiers);

        return new PublicationResource(
            $publication->load(['categorie', 'auteur'])->loadCount('commentaires')
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publication $publication): JsonResponse
    {
        $this->authorize('delete', $publication);
        $publication->delete();

        return response()->json(null, 204);
    }
}
