<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PublicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'categorie' => $this->categorie?->nom,
            'auteur' => $this->auteur?->name,
            'image' => $this->image ? Storage::url($this->image) : null,
            'fichier_media' => $this->fichier_media ? Storage::url($this->fichier_media) : null,
            'type_media' => $this->type_media,
            'commentaires_count' => $this->commentaires_count,
            'publie_le' => $this->created_at?->toDateString(),
        ];
    }
}
