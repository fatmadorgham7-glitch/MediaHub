<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class Publication extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'titre',
        'contenu',
        'categorie_id',
        'user_id',
        'image',
        'fichier_media',
        'type_media',
        'document',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('categories.liste');
        });

        static::deleted(function () {
            Cache::forget('categories.liste');
        });

        static::restored(function () {
            Cache::forget('categories.liste');
        });

        static::deleting(function (Publication $publication) {
            if ($publication->isForceDeleting()) {
                return;
            }

            $publication->deleteStoredMedia();
            $publication->forceFill([
                'image' => null,
                'fichier_media' => null,
                'type_media' => null,
                'document' => null,
            ])->saveQuietly();
        });

        static::forceDeleted(function (Publication $publication) {
            $publication->deleteStoredMedia();
        });
    }

    private function deleteStoredMedia(): void
    {
        foreach ([
            'image' => 'public',
            'fichier_media' => 'public',
            'document' => 'local',
        ] as $champ => $disque) {
            $chemin = $this->{$champ};

            if ($chemin && ! Storage::disk($disque)->delete($chemin)) {
                throw new RuntimeException("Impossible de supprimer le fichier média « {$chemin} ».");
            }
        }
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }
}
