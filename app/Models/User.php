<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// NOUVEAU : On importe la classe HasMany pour définir la relation One-to-Many
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Contracts\Auth\MustVerifyEmail; // Gardé commenté si non utilisé

/**
 * Class User
 * Le modèle utilisateur par défaut de Laravel, avec la relation vers les commandes.
 * Correspond à la table 'users'.
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * (Méthode moderne pour les casts)
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * RELATION : Un-à-Plusieurs (One-to-Many).
     *
     * Un Utilisateur peut passer PLUSIEURS Commandes (Orders).
     * Cette fonction permet de récupérer toutes les commandes d'un utilisateur : $user->orders.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        // On indique à Eloquent que ce modèle a plusieurs modèles Order
        return $this->hasMany(Order::class);
    }
}
