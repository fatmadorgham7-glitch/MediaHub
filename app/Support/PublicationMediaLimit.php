<?php

namespace App\Support;

use App\Models\Publication;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class PublicationMediaLimit
{
    public static function validate(
        Validator $validator,
        Request $request,
        ?Publication $publication = null
    ): void {
        $nombre = 0;
        $champEnErreur = 'fichier_media';

        foreach (['image', 'fichier_media'] as $champ) {
            if ($request->hasFile($champ)) {
                $nombre++;
                $champEnErreur = $champ;

                continue;
            }

            $suppressionDemandee = $request->boolean('retirer_'.$champ)
                || ($request->isJson()
                    && $request->exists($champ)
                    && $request->input($champ) === null);

            if ($suppressionDemandee) {
                continue;
            }

            if ($publication?->{$champ}) {
                $nombre++;
            }
        }

        if ($nombre > 1) {
            $validator->errors()->add(
                $champEnErreur,
                'Une publication ne peut avoir qu’un seul média : une image ou un fichier audio/vidéo.'
            );
        }
    }
}
