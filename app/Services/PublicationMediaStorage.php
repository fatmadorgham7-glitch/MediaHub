<?php

namespace App\Services;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PublicationMediaStorage
{
    private const UPLOADS = [
        'image' => ['folder' => 'publications', 'disk' => 'public'],
        'fichier_media' => ['folder' => 'medias', 'disk' => 'public'],
        'document' => ['folder' => 'documents', 'disk' => 'local'],
    ];

    public function storeFromRequest(Request $request): array
    {
        $fichiers = [];
        $enregistre = false;

        try {
            foreach (self::UPLOADS as $champ => $destination) {
                if (! $request->hasFile($champ)) {
                    continue;
                }

                $fichier = $request->file($champ);
                $chemin = $fichier->store($destination['folder'], $destination['disk']);

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
                $this->delete($fichiers);
            }
        }
    }

    /**
     * @return array{attributes: array<string, mixed>, old_files: array<string, string>}
     */
    public function prepareUpdate(
        Request $request,
        Publication $publication,
        array $donnees,
        array $nouveauxFichiers
    ): array {
        $anciensFichiers = [];

        foreach (self::UPLOADS as $champ => $destination) {
            $suppressionDemandee = $request->boolean('retirer_'.$champ)
                || ($request->isJson()
                    && array_key_exists($champ, $donnees)
                    && $donnees[$champ] === null);

            if ((isset($nouveauxFichiers[$champ]) || $suppressionDemandee) && $publication->{$champ}) {
                $anciensFichiers[$champ] = $publication->{$champ};
            }

            if ($suppressionDemandee) {
                $donnees[$champ] = null;
            }
        }

        unset($donnees['retirer_image'], $donnees['retirer_fichier_media'], $donnees['retirer_document']);

        if ($request->boolean('retirer_fichier_media')
            || ($request->isJson()
                && array_key_exists('fichier_media', $donnees)
                && $donnees['fichier_media'] === null)) {
            $donnees['type_media'] = null;
        }

        return [
            'attributes' => array_merge($donnees, $nouveauxFichiers),
            'old_files' => $anciensFichiers,
        ];
    }

    public function delete(array $fichiers): void
    {
        foreach (self::UPLOADS as $champ => $destination) {
            $chemin = $fichiers[$champ] ?? null;

            if ($chemin && ! Storage::disk($destination['disk'])->delete($chemin)) {
                throw new RuntimeException("Impossible de supprimer le fichier « {$chemin} ».");
            }
        }
    }
}
