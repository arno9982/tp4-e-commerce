<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',              // AJOUTÉ
        'description',
        'price',
        'stock_quantity',
        'image_url',
        'category_id',       // AJOUTÉ (si tu as un modèle Category)
        'rating',            // AJOUTÉ (float, ex: 4.5)
        'sizes',             // AJOUTÉ (json ou string)
    ];

    // Casts pour que Laravel gère correctement les types
    protected $casts = [
        'sizes' => 'array',     // ou 'json'
        'rating' => 'float',
        'price' => 'integer',
    ];

    // Relation avec Category (si tu as un modèle Category)
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relation OrderItems (tu l’avais déjà)
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
