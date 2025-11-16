<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Order
 * Représente une commande passée par un utilisateur.
 * Correspond à la table 'orders'.
 */
class Order extends Model
{
    use HasFactory;

    /**
     * Les attributs de la base de données qui sont "mass assignable".
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',       // Clé étrangère vers l'utilisateur
        'total_amount',  // Montant total de la commande
        'status',        // Statut (pending, shipped, etc.)
    ];

    /**
     * RELATION : Plusieurs-à-Un (BelongsTo).
     *
     * Une Commande appartient à UN SEUL Utilisateur (le client qui a commandé).
     * La clé étrangère utilisée est 'user_id'.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // On indique à Eloquent que ce modèle appartient à un modèle User
        return $this->belongsTo(User::class);
    }

    /**
     * RELATION : Un-à-Plusieurs (One-to-Many).
     *
     * Une Commande contient PLUSIEURS articles de commande (OrderItems).
     *
     * @return HasMany
     */
    public function orderItems(): HasMany
    {
        // On indique à Eloquent que ce modèle a plusieurs OrderItems
        return $this->hasMany(OrderItem::class);
    }
}
