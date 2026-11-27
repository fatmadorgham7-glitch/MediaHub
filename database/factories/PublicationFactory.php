<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(6),
            'contenu' => fake()->paragraphs(3, true),
            // On prend une catégorie au hasard parmi celles créées par le seeder
            'categorie_id' => Categorie::factory(),
            // On crée un nouvel utilisateur automatiquement pour chaque publication
            'user_id' => User::factory(),
        ];
    }
}
