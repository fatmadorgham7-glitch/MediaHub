<?php

namespace Tests\Feature;

use Tests\TestCase;

class CarteComponentTest extends TestCase
{
    public function test_la_carte_peut_etre_mise_en_avant(): void
    {
        $this->blade('<x-carte titre="Une carte" mise-en-avant />')
            ->assertSee('class="carte carte-vedette"', false)
            ->assertSee('Une carte');
    }

    public function test_la_carte_affiche_les_medias_publics(): void
    {
        $this->blade(
            '<x-carte titre="Une publication" image="publications/image.jpg" fichier-media="medias/audio.mp3" type-media="audio" />'
        )
            ->assertSee('<img', false)
            ->assertSee('<audio controls', false);
    }
}
