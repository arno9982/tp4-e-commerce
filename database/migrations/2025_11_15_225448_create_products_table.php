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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // ID du produit (Clé Primaire)
            $table->string('name', 255); // Nom du produit (VARCHAR)
            $table->text('description')->nullable(); // Description détaillée
            $table->decimal('price', 10, 2); // Prix du produit (ex: 999.99)
            $table->integer('stock_quantity')->default(0); // Quantité en stock
            $table->string('image_url')->nullable(); // URL de l'image principale
            $table->timestamps(); // colonnes created_at et updated_at
        });
    }

    /**
     * Annule les migrations (supprime la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
