<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrderItem
 * Représente une ligne spécifique dans une commande (la table pivot de la relation N:N).
 * Correspond à la table 'order_items'.
 */
class OrderItem extends Model
{
    use HasFactory;

    /**
     * Les attributs de la base de données qui sont "mass assignable".
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',          // Clé étrangère vers la commande
        'product_id',        // Clé étrangère vers le produit
        'quantity',          // Quantité de ce produit dans la ligne
        'price_at_purchase', // Prix enregistré (fixe le prix pour l'historique)
    ];

    /**
     * RELATION : Plusieurs-à-Un (BelongsTo).
     *
     * Cet article de commande appartient à UNE SEULE Commande.
     * La clé étrangère utilisée est 'order_id'.
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        // Permet de charger la commande parente : $item->order
        return $this->belongsTo(Order::class);
    }

    /**
     * RELATION : Plusieurs-à-Un (BelongsTo).
     *
     * Cet article de commande fait référence à UN SEUL Produit.
     * La clé étrangère utilisée est 'product_id'.
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        // Permet de charger le produit associé : $item->product
        return $this->belongsTo(Product::class);
    }
}
