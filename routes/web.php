<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController; // Assuré d'être importé
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. Routes Publiques (Catalogue, Accueil & Newsletter)
|--------------------------------------------------------------------------
*/
// CONTACT
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// ABOUT
Route::get('/about', function () {
    return view('about');
})->name('about');

// TERMS & CONDITIONS
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

// BLOG (liste des articles)
Route::get('/blog', function () {
    return view('blog.index');
})->name('blog.index');

// BLOG (article individuel si tu veux)
Route::get('/blog/{slug}', function ($slug) {
    return view('blog.show', compact('slug'));
})->name('blog.show');


// Page d'accueil
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Catalogue de produits et fiche détaillée

// ATTENTION: Le nom de la route 'products.index' doit pointer vers le catalogue public.
// J'ai utilisé 'index' dans le contrôleur pour le public (comme le standard Resource)
// Si vous gardez 'catalogueIndex', il faut ajuster le contrôleur ou renommer la route ici.

Route::get('/catalogue', [ProductController::class, 'index'])->name('products.index');
// Afficher un produit par slug (pas de route model binding automatique)
Route::get('/produit/{slug}', [ProductController::class, 'show'])->name('products.show'); 


// Inscription à la newsletter
Route::post('/newsletter', function () {
    request()->validate(['email' => 'required|email']);
    // Logique de sauvegarde ou d'envoi à un service externe
    return back()->with('success', 'Merci ! Vous êtes inscrit à la newsletter !');
})->name('newsletter.subscribe');


/*
|--------------------------------------------------------------------------
| 2. Routes Authentifiées (Dashboard & Profil Utilisateur)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Redirection vers le dashboard admin si admin, sinon vers l'accueil
    Route::get('/dashboard', function () {
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');

    // Gestion du profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Historique des commandes du client 
    // Route::get('/mes-commandes', [OrderController::class, 'userHistory'])->name('orders.user.history');

    // Route API pour récupérer les tailles par catégorie
    Route::get('/api/category-sizes/{categoryId}', [ProductController::class, 'getCategorySizes'])->name('api.category-sizes');
});


/*
|--------------------------------------------------------------------------
| 3. Routes Administration (CRUD Produits, Commandes, etc.)
|--------------------------------------------------------------------------
| Ce groupe de routes nécessite l'authentification et le rôle 'admin'.
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Tableau de bord administrateur (ADMIN SEULEMENT)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD Produits - Routes explicites
    Route::get('products', [ProductController::class, 'adminIndex'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
});


/*
|--------------------------------------------------------------------------
| 4. Routes d'Authentification (Générées par Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
