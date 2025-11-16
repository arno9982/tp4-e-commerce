<?php

namespace Database\Factories;

use App\Models\Product; // Importez le modèle Product
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    // Indique que cette factory est liée au modèle Product
    protected $model = Product::class;

    /**
     * Définir l'état par défaut du modèle.
     * Les valeurs sont générées aléatoirement en utilisant l'objet Faker.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Utilisation de l'objet Faker pour générer des données réalistes
        return [
            // Nom: Une phrase aléatoire de 3 à 5 mots pour simuler un titre de produit.
            'name' => $this->faker->words($this->faker->numberBetween(3, 5), true),

            // Description: Un long texte pour une description détaillée.
            'description' => $this->faker->paragraph(3), // 3 phrases de paragraphe

            // Prix: Un nombre décimal entre 1000.00 et 100000.00 FCFA.
            'price' => $this->faker->randomFloat(2, 1000, 100000),

            // Stock: Une quantité aléatoire entre 0 (épuisé) et 100.
            'stock_quantity' => $this->faker->numberBetween(0, 100),

            // Image URL: Une URL de placeholder (simulée).
            // Dans un vrai projet, on utiliserait une librairie comme LoremFlickr.
            'image_url' => 'https://placehold.co/600x600/8f7a9d/ffffff?text=' . urlencode($this->faker->word()),
        ];
    }
}
