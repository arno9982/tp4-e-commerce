<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. Routes Publiques (Catalogue & Accueil - T4)
|--------------------------------------------------------------------------
*/

// Page d'accueil (Route: /)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Catalogue de produits (Route: /catalogue)
Route::get('/catalogue', [ProductController::class, 'index'])->name('products.index');

// Fiche produit détaillée (Route: /produit/{product})
Route::get('/produit/{product}', [ProductController::class, 'show'])->name('products.show');


/*
|--------------------------------------------------------------------------
| 2. Routes Authentifiées (Client & Admin - T5 + T6/T7/T8)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Routes par défaut de Breeze (Dashboard, Profil)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Produits (T6, T7, T8) - Zone Admin
    // Accessible à tout utilisateur connecté ('auth') pour le moment.
    Route::prefix('admin')->group(function () {
        // La route resource crée 6 routes : index, create, store, edit, update, destroy
        Route::resource('products', ProductController::class)->except(['show']);

        // Routes Panier/Commandes (pour T10, T11, T12 - À décommenter plus tard)
        // Route::get('orders', [OrderController::class, 'adminIndex'])->name('orders.admin.index');
    });

    // Historique des commandes du client (T12)
    // Route::get('/mes-commandes', [OrderController::class, 'userHistory'])->name('orders.user.history');
});

/*
|--------------------------------------------------------------------------
| 3. Routes d'Authentification (Générées par Breeze - T5)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
