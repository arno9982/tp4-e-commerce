<?php

namespace Database\Seeders;

use App\Models\Product; // Importez le modèle Product
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Class ProductSeeder
 * Permet de peupler la table 'products' avec des données de test.
 */
class ProductSeeder extends Seeder
{
    /**
     * Exécute le seeder de la base de données.
     */
    public function run(): void
    {
        // Suppression de toutes les données existantes dans la table 'products'
        // pour garantir un état propre à chaque exécution du seeder.
        Product::truncate();

        // Création de 50 faux produits en utilisant la ProductFactory.
        // C'est la méthode recommandée par Laravel pour générer des données de test.
        Product::factory()->count(50)->create();

        $this->command->info('50 produits créés avec succès !');
    }
}
