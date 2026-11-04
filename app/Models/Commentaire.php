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
}
