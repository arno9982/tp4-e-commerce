{{-- resources/views/catalogue.blade.php --}}
@extends('layouts.app')

@section('title', 'Nos produits')

@section('content')
    <aside class="sidebar">
        <h2>Catégories</h2>
        <nav>
            <ul class="category-list">
                <li><a href="#">Robes & Jupes</a></li>
                <li><a href="#">T-shirts & Hauts</a></li>
                <li><a href="#">Jeans & Pantalons</a></li>
                <li><a href="#">Vestes & Manteaux</a></li>
                <li><a href="#">Chaussures Femme</a></li>
                <li><a href="#">Chaussures Homme</a></li>
                <li><a href="#">Accessoires</a></li>
            </ul>
        </nav>
        <hr>
        <h2>Filtres</h2>
        <div class="filters">
            <div class="filter-group">
                <h3>Prix (FCFA)</h3>
                <input type="range" min="1000" max="100000" value="50000" class="price-range">
                <div style="display: flex; justify-content: space-between; font-size: 0.9em; margin-top: 5px;">
                    <span>1 000 FCFA</span>
                    <span>100 000 FCFA</span>
                </div>
            </div>
            <button class="apply-filters-btn">Appliquer les filtres</button>
        </div>
    </aside>

    <section class="product-list-container">

        <div class="product-grid">
            @forelse ($products as $product)
                <article class="product-card">
                    <a href="{{ route('products.show', $product->slug) }}" class="product-link">
                        <img src="{{ $product->image_url ? asset('storage/' . $product->image_url) : asset('images/default-product.jpg') }}"
                            alt="{{ $product->name }}">
                        <div class="card-details">
                            <p class="category-tag">{{ $product->category?->name ?? 'Non classé' }}</p>
                            <h3>{{ $product->name }}</h3>
                            @if ($product->sizes && count($product->sizes) > 0)
                                <div class="product-sizes">
                                    <span class="size-label">Tailles:</span>
                                    @foreach ($product->sizes as $size)
                                        <span class="size-badge">{{ $size }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="product-rating">
                                @php
                                    $rating = $product->rating ?? 0;
                                    $full = floor($rating);
                                    $hasHalf = $rating - $full >= 0.5;
                                @endphp
                                <span class="stars">
                                    {{ str_repeat('★★★★★', $full) }}
                                    @if ($hasHalf)
                                        ★
                                    @endif
                                    {{ str_repeat('☆', 5 - $full - ($hasHalf ? 1 : 0)) }}
                                </span>
                                <span class="review-count">({{ number_format($rating, 1) }})</span>
                            </div>
                            <p class="price"><strong>{{ number_format($product->price) }} FCFA</strong></p>
                        </div>
                    </a>
                </article>
            @empty
                <p>Aucun produit trouvé.</p>
            @endforelse
        </div> {{-- fin .product-grid --}}
    </section> {{-- fin .product-list-container --}}
@endsection
