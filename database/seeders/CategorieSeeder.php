<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            [
                'nom' => 'Vidéo',
                'slug' => 'video',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Audio',
                'slug' => 'audio',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Image',
                'slug' => 'image',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ] as $categorie) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $categorie['slug']],
                $categorie
            );
        }
    }
}
