<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_sinscrire(): void
    {
        $response = $this->post('/inscription', [
            'name' => 'Amel',
            'email' => 'amel@example.com',
            'password' => 'motdepasse',
            'password_confirmation' => 'motdepasse',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'amel@example.com']);
    }

    public function test_connexion_avec_identifiants_incorrects(): void
    {
        $response = $this->post('/connexion', [
            'email' => 'inconnu@example.com',
            'password' => 'faux',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
