<?php

namespace Database\Factories;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categorie>
 */
class CategorieFactory extends Factory
{
    public function definition(): array
    {
        $nom = fake()->unique()->word();

        return [
            'nom' => ucfirst($nom),
            'slug' => $nom,
        ];
    }
}
