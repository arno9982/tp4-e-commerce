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

        // Produits fictifs avec les vraies catégories
        $products = [
            // ROBES & JUPES (cat 1)
            [
                "name" => "Robe élégante rouge",
                "price" => 15000,
                "category_id" => 1,
                "sizes" => ["S", "M", "L", "XL"],
            ],
            [
                "name" => "Robe fleurie été",
                "price" => 12500,
                "category_id" => 1,
                "sizes" => ["XS", "S", "M", "L"],
            ],
            [
                "name" => "Jupe noire élégante",
                "price" => 9800,
                "category_id" => 1,
                "sizes" => ["S", "M", "L", "XL", "XXL"],
            ],

            // T-SHIRTS & HAUTS (cat 2)
            [
                "name" => "T-shirt coton premium blanc",
                "price" => 8000,
                "category_id" => 2,
                "sizes" => ["XS", "S", "M", "L", "XL"],
            ],
            [
                "name" => "T-shirt graphique noir",
                "price" => 7500,
                "category_id" => 2,
                "sizes" => ["S", "M", "L", "XL", "XXL"],
            ],
            [
                "name" => "Chemise casual bleu",
                "price" => 13000,
                "category_id" => 2,
                "sizes" => ["M", "L", "XL"],
            ],

            // JEANS & PANTALONS (cat 3)
            [
                "name" => "Jeans slim fit bleu",
                "price" => 19000,
                "category_id" => 3,
                "sizes" => ["S", "M", "L", "XL"],
            ],
            [
                "name" => "Pantalon chino beige",
                "price" => 16500,
                "category_id" => 3,
                "sizes" => ["S", "M", "L", "XL", "XXL"],
            ],
            [
                "name" => "Jeans noir déchiré",
                "price" => 21000,
                "category_id" => 3,
                "sizes" => ["XS", "S", "M", "L"],
            ],

            // VESTES & MANTEAUX (cat 4)
            [
                "name" => "Veste blazer grise",
                "price" => 32000,
                "category_id" => 4,
                "sizes" => ["S", "M", "L", "XL"],
            ],
            [
                "name" => "Manteau d'hiver noir",
                "price" => 45000,
                "category_id" => 4,
                "sizes" => ["S", "M", "L", "XL", "XXL"],
            ],
            [
                "name" => "Veste en cuir marron",
                "price" => 38000,
                "category_id" => 4,
                "sizes" => ["M", "L", "XL"],
            ],

            // CHAUSSURES FEMME (cat 5)
            [
                "name" => "Talons hauts noirs",
                "price" => 22000,
                "category_id" => 5,
                "sizes" => ["35", "36", "37", "38", "39", "40"],
            ],
            [
                "name" => "Sneakers blanches",
                "price" => 18500,
                "category_id" => 5,
                "sizes" => ["36", "37", "38", "39", "40", "41"],
            ],
            [
                "name" => "Sandales d'été or",
                "price" => 12000,
                "category_id" => 5,
                "sizes" => ["35", "36", "37", "38", "39"],
            ],

            // CHAUSSURES HOMME (cat 6)
            [
                "name" => "Chaussures de ville marron",
                "price" => 28000,
                "category_id" => 6,
                "sizes" => ["39", "40", "41", "42", "43", "44"],
            ],
            [
                "name" => "Baskets sportives noires",
                "price" => 24500,
                "category_id" => 6,
                "sizes" => ["40", "41", "42", "43", "44", "45"],
            ],
            [
                "name" => "Sandales confort beige",
                "price" => 16000,
                "category_id" => 6,
                "sizes" => ["38", "39", "40", "41", "42", "43"],
            ],

            // ACCESSOIRES (cat 7) - pas de tailles
            [
                "name" => "Ceinture noire cuir",
                "price" => 8500,
                "category_id" => 7,
                "sizes" => [],
            ],
            [
                "name" => "Sac à main rouge",
                "price" => 19500,
                "category_id" => 7,
                "sizes" => [],
            ],
            [
                "name" => "Écharpe cachemire",
                "price" => 14000,
                "category_id" => 7,
                "sizes" => [],
            ],
            [
                "name" => "Lunettes de soleil UV",
                "price" => 12500,
                "category_id" => 7,
                "sizes" => [],
            ],
        ];

        foreach ($products as $index => $p) {
            Product::create([
                "name" => $p["name"],
                "slug" => Str::slug($p["name"]) . '-' . ($index + 1),
                "description" => "Un produit fictif généré automatiquement pour les tests. Découvrez nos produits de qualité premium.",
                "price" => $p["price"],
                "stock_quantity" => rand(5, 30),
                "image_url" => "products/fake_" . (($index % 4) + 1) . ".jpg",
                "category_id" => $p["category_id"],
                "rating" => round(rand(30, 50) / 10, 1),
                "sizes" => $p["sizes"],
            ]);
        }
    }

    /**
     * Génère 4 images 400x400 colorées dans storage/app/public/products
     */
    private function generateFakeImages()
    {
        $colors = [
            [220, 53, 69],      // Rouge
            [40, 167, 69],      // Vert
            [0, 123, 255],      // Bleu
            [255, 193, 7],      // Or/Jaune
        ];

        for ($i = 1; $i <= 4; $i++) {
            $image = imagecreatetruecolor(400, 400);
            $rgb = $colors[$i - 1];
            $color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
            imagefill($image, 0, 0, $color);

            // Ajouter du texte
            $white = imagecolorallocate($image, 255, 255, 255);
            imagestring($image, 5, 150, 190, "PRODUCT", $white);
            imagestring($image, 3, 170, 210, "Image " . $i, $white);

            $path = storage_path("app/public/products/fake_{$i}.jpg");
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            imagejpeg($image, $path, 90);
            imagedestroy($image);
        }
    }
}