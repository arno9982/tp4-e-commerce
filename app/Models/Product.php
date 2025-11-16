<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Product
 * Représente un produit vendu dans la boutique.
 * Correspond à la table 'products' dans la base de données.
 */
class Product extends Model
{
    use HasFactory;

    /**
     * Les attributs de la base de données qui sont "mass assignable" (peuvent être remplis via un tableau).
     * Ceci est crucial pour la sécurité, car cela empêche l'assignation de colonnes sensibles.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_quantity',
        'image_url',
    ];

    /**
     * RELATION : Un-à-Plusieurs (One-to-Many).
     *
     * Un Produit peut être présent dans PLUSIEURS lignes d'articles de commande (OrderItems).
     * C'est le côté "Un" de la relation pivot OrderItem.
     *
     * @return HasMany
     */
    public function orderItems(): HasMany
    {
        // On indique à Eloquent que ce modèle a plusieurs OrderItems
        return $this->hasMany(OrderItem::class);
    }
}
