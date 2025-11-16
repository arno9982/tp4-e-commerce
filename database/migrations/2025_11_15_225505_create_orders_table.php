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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // ID de la commande (Clé Primaire)

            // Clé Étrangère vers la table 'users' : Qui a passé la commande ?
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->decimal('total_amount', 10, 2); // Montant total de la commande
            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending'); // Statut de la commande

            $table->timestamps(); // colonnes created_at et updated_at
        });
    }

    /**
     * Annule les migrations (supprime la table).
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
