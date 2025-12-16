<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ImageService;
use App\Traits\ManageSizes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Exception;

class ProductController extends Controller
{
    use ManageSizes;

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    // ----------------------------------------------------------------------
    // C.R.U.D. - Administration (Products Management)
    // ----------------------------------------------------------------------

    /**
     * Affiche la liste des produits pour le panneau d'administration.
     */
    public function adminIndex() // Renommée pour éviter le conflit avec l'index public
    {
        $products = Product::with('category')
                           ->latest()
                           ->paginate(15);

        // Vous devrez créer la vue 'products.index' pour afficher cette liste
        return view('admin.products.index', compact('products'));
    }

    /**
     * Afficher le formulaire d'ajout (C)
     */
    public function create() 
    {
        // Récupérer les catégories pour le menu déroulant du formulaire
        $categories = Category::all(); 
        return view('admin.products.create', compact('categories')); 
    }

    /**
     * Sauvegarder un nouveau produit (C)
     */
    public function store(Request $request) 
    {
        // 1. Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,webp,gif|max:5120', // max 5 MB
            'sizes' => 'nullable|array',
            'sizes.*' => 'string|max:10',
        ]);
        
        // 2. Générer le slug unique
        $baseSlug = Str::slug($validatedData['name']);
        $uniqueSlug = $this->generateUniqueSlug($baseSlug);

        try {
            // 3. Traiter l'image si présente
            if ($request->hasFile('image')) {
                $imageData = $this->imageService->processAndStore($request->file('image'));
                $validatedData['image_url'] = $imageData['path'];
            }

            // 4. Créer le produit
            $product = new Product($validatedData);
            $product->slug = $uniqueSlug;
            $product->sizes = $validatedData['sizes'] ?? [];
            $product->save();

            return redirect()->route('admin.products.index')->with('success', 'Produit ajouté avec succès.');
        } catch (Exception $e) {
            \Log::error('Erreur lors de la création du produit: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de la création du produit: ' . $e->getMessage());
        }
    }
    private function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $count = 1;

        // Tant que le slug existe déjà dans la base de données
        while (Product::where('slug', $slug)->exists()) {
            // Ajouter un suffixe incrémenté : base-slug-1, base-slug-2, etc.
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
    
    /**
     * Afficher le formulaire d'édition (U)
     */
    public function edit($id) 
    {
        $product = Product::findOrFail($id);
        $categories = Category::all(); 
        return view('admin.products.edit', compact('product', 'categories'));
    }
    
    /**
     * Mettre à jour le produit (U)
     */
    public function update(Request $request, $id) 
    {
        $product = Product::findOrFail($id);
        
        // 1. Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,webp,gif|max:5120', // max 5 MB
            'sizes' => 'nullable|array',
        ]);

        try {
            // 2. Traiter la nouvelle image si présente
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                $this->imageService->deleteImage($product->image_url);
                
                // Traiter la nouvelle image
                $imageData = $this->imageService->processAndStore($request->file('image'));
                $validatedData['image_url'] = $imageData['path'];
            }
            // Si pas de nouvelle image, on conserve l'ancienne

            // 3. Mise à jour dans la base de données
            $product->update($validatedData);

            return redirect()->route('admin.products.index')->with('success', 'Produit mis à jour avec succès.');
        } catch (Exception $e) {
            \Log::error('Erreur lors de la mise à jour du produit: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de la mise à jour du produit: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer le produit (D)
     */
    public function destroy($id) 
    {
        try {
            $product = Product::findOrFail($id);
            
            // Supprimer l'image associée
            $this->imageService->deleteImage($product->image_url);
            
            // Supprimer le produit
            $product->delete();
            
            return redirect()->route('admin.products.index')->with('success', 'Produit supprimé avec succès.');
        } catch (Exception $e) {
            \Log::error('Erreur lors de la suppression du produit: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression du produit.');
        }
    }
    
    // ----------------------------------------------------------------------
    // C.R.U.D. - Public (Catalogue)
    // ----------------------------------------------------------------------

    /**
     * Affiche la liste des produits pour le catalogue public.
     */
    public function index() // GARDER pour la route '/products' ou '/catalogue'
    {
        $products = Product::with('category')
                           ->latest()
                           ->paginate(15); // Utilisation de la pagination pour le public

        return view('catalogue', compact('products'));
    }

    /**
     * Affiche les détails d'un produit (R)
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
                          ->with('category')
                          ->firstOrFail();

        return view('products.show', compact('product'));
    }

    /**
     * Récupère les tailles disponibles pour une catégorie (API endpoint)
     */
    public function getCategorySizes($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $sizes = $this->getSizesByCategory($category->name);

        // Log pour debug : vérifier que l'API est appelée et quelles tailles sont retournées
        try {
            \Log::info('API getCategorySizes', ['category_id' => $categoryId, 'category_name' => $category->name, 'sizes' => $sizes]);
        } catch (\Exception $e) {
            // Ne pas bloquer la réponse si la journalisation échoue
        }

        return response()->json(['sizes' => $sizes]);
    }
}
