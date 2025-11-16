<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations (crée la table).
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // ID de l'article de commande (Clé Primaire)

            // Clé Étrangère vers la table 'orders'
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // Clé Étrangère vers la table 'products'
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            $table->integer('quantity'); // Quantité de ce produit dans la commande
            $table->decimal('price_at_purchase', 10, 2); // Le prix du produit au moment de l'achat (important pour l'historique)

            $table->timestamps(); // colonnes created_at et updated_at
        });
    }

    /**
     * Annule les migrations (supprime la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
