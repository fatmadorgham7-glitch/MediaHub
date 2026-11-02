<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function accueil(){
        return 'Accueil de MediaHub';
    }
    public function aPropos(){
        return 'Apropos de  MediaHub';
    }

}
