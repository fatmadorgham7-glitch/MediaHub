<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_est_redirige_vers_la_connexion(): void
    {
        $this->get('/admin/tableau-de-bord')
            ->assertRedirect('/connexion');
    }

    public function test_un_auteur_ne_peut_pas_acceder_au_tableau_de_bord(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/tableau-de-bord')
            ->assertForbidden();
    }

    public function test_un_administrateur_peut_acceder_au_tableau_de_bord(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/tableau-de-bord')
            ->assertOk()
            ->assertSee('Tableau de bord administrateur')
            ->assertSee('Publications')
            ->assertSee('Catégories')
            ->assertSee('Utilisateurs');
    }
}
