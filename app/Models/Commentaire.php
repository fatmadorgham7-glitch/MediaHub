<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
     use HasFactory;

    protected $fillable = [
        'titre',
        'contenu',
        'categorie_id',
        'user_id',
    ];
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
