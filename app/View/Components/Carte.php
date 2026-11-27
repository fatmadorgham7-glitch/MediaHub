<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Carte extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $titre,
        public ?string $image = null,
        public ?string $fichierMedia = null,
        public ?string $typeMedia = null,
        public bool $miseEnAvant = false
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.carte');
    }
}
