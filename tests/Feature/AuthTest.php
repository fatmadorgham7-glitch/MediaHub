<?php

namespace Tests\Feature;

use App\Models\User;
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
        $response->assertSessionHas('_old_input.email');
        $response->assertSessionMissing('_old_input.password');
        $this->assertGuest();
    }

    public function test_un_utilisateur_peut_se_connecter_puis_se_deconnecter(): void
    {
        $user = User::factory()->create([
            'password' => 'motdepasse',
        ]);

        $this->post('/connexion', [
            'email' => $user->email,
            'password' => 'motdepasse',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);

        $this->post('/deconnexion')->assertRedirect('/');

        $this->assertGuest();
    }
}
