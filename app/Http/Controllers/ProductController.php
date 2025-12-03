<?php
// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
public function index()
{
    $products = Product::with('category') // charge la catégorie pour éviter N+1
                       ->latest()
                       ->paginate(15);

    return view('catalogue', compact('products'));
}

public function show($slug)
{
    $product = Product::where('slug', $slug)
                      ->with('category')
                      ->firstOrFail();

    return view('products.show', compact('product')); // tu créeras cette vue plus tard
}
}
