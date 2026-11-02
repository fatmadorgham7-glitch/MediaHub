<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function accueil(){
        return 'Accueil ';
    }
    public function aPropos(){
        return view('Apropos de  MediaHub');
    }

}
