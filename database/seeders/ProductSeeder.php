<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Créer le dossier storage/products
        Storage::makeDirectory('public/products');

        // Générer des fausses images 400x400 en couleur uni
        $this->generateFakeImages();

        // Produits fictifs
        $products = [
            [
                "name" => "Robe élégante rouge",
                "price" => 15000,
                "category_id" => 1,
                "sizes" => ["S","M","L"],
            ],
            [
                "name" => "T-shirt coton premium",
                "price" => 8000,
                "category_id" => 2,
                "sizes" => ["M","L","XL"],
            ],
            [
                "name" => "Jeans slim fit",
                "price" => 19000,
                "category_id" => 3,
                "sizes" => ["32","34","36"],
            ],
            [
                "name" => "Veste pour homme",
                "price" => 32000,
                "category_id" => 4,
                "sizes" => ["L","XL"],
            ],
        ];

        foreach ($products as $index => $p) {
            Product::create([
                "name" => $p["name"],
                "slug" => Str::slug($p["name"]),
                "description" => "Un produit fictif généré automatiquement pour les tests.",
                "price" => $p["price"],
                "stock_quantity" => rand(5, 30),
                "image_url" => "products/fake_" . ($index + 1) . ".jpg",
                "category_id" => $p["category_id"],
                "rating" => rand(30, 50) / 10,     // ex: 4.3
                "sizes" => $p["sizes"],
            ]);
        }
    }

    /**
     * Génère 4 images 400x400 colorées dans storage/app/public/products
     */
    private function generateFakeImages()
    {
        for ($i = 1; $i <= 4; $i++) {
            $image = imagecreatetruecolor(400, 400);
            $color = imagecolorallocate($image, rand(0,255), rand(0,255), rand(0,255));
            imagefill($image, 0, 0, $color);

            $path = public_path("images/products/fake_{$i}.jpg");
            imagejpeg($image, $path, 90);
            imagedestroy($image);
        }
    }
}
