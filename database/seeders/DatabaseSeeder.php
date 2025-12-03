<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur test
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Créer les catégories
        Category::create(['name' => '']);
        Category::create(['name' => 'Homme']);
        Category::create(['name' => 'Jeans']);
        Category::create(['name' => 'Vêtements']);

        // Appeler le seeder des produits
        $this->call(ProductSeeder::class);
    }
}
