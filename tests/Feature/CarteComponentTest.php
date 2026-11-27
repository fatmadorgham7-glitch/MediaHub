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
}
