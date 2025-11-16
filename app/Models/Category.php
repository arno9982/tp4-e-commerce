<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Category
 * Représente une catégorie de produits.
 */
class Category extends Model
{
    use HasFactory;

    /**
     * Les attributs pouvant être assignés massivement.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * RELATION : Un-à-Plusieurs (One-to-Many).
     *
     * Une Catégorie a plusieurs Produits.
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        // On indique à Eloquent que cette catégorie possède plusieurs produits
        return $this->hasMany(Product::class);
    }
}
