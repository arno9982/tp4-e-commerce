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
        // Créer un utilisateur ADMIN
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        // Créer un utilisateur TEST normal (non-admin)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'is_admin' => false,
        ]);

        // Créer les catégories
        $categories = [
            'ROBES & JUPES',
            'T-SHIRTS & HAUTS',
            'JEANS & PANTALONS',
            'VESTES & MANTEAUX',
            'CHAUSSURES FEMME',
            'CHAUSSURES HOMME',
            'ACCESSOIRES',
        ];

        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }

        // Appeler le seeder des produits
        $this->call(ProductSeeder::class);
    }
}