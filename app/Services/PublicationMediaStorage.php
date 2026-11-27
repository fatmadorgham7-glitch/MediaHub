<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PublicationMediaStorage
{
    public function storeFromRequest(Request $request): array
    {
        $fichiers = [];
        $enregistre = false;

        try {
            foreach (['image' => 'publications', 'fichier_media' => 'medias'] as $champ => $dossier) {
                if (! $request->hasFile($champ)) {
                    continue;
                }

                $fichier = $request->file($champ);
                $chemin = $fichier->store($dossier, 'public');

                if ($chemin === false) {
                    throw new RuntimeException("Impossible de stocker le fichier « {$champ} ».");
                }

                $fichiers[$champ] = $chemin;

                if ($champ === 'fichier_media') {
                    $fichiers['type_media'] = str_starts_with($fichier->getMimeType() ?? '', 'audio')
                        ? 'audio'
                        : 'video';
                }
            }

            $enregistre = true;

            return $fichiers;
        } finally {
            if (! $enregistre) {
                $this->delete($this->paths($fichiers));
            }
        }
    }

    public function paths(array $fichiers): array
    {
        return array_values(array_filter([
            $fichiers['image'] ?? null,
            $fichiers['fichier_media'] ?? null,
        ]));
    }

    public function delete(array $chemins): void
    {
        foreach ($chemins as $chemin) {
            if ($chemin && ! Storage::disk('public')->delete($chemin)) {
                throw new RuntimeException("Impossible de supprimer le fichier « {$chemin} ».");
            }
        }
    }
}
