{{-- resources/views/admin/products/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Catalogue Produits - Administration')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <!-- En-tête avec boutons -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Catalogue Produits</h1>
                <p class="mt-2 text-lg text-gray-600">Gérez votre inventaire en un clin d'œil</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-11 0h-1" />
                    </svg>
                    Retour au Dashboard
                </a>
                <a href="{{ route('admin.products.create') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-indigo-700 hover:to-blue-700 transform hover:scale-105 transition duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter un Produit
                </a>
            </div>
        </div>

        <!-- Messages de session -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 rounded-xl mb-8 shadow-md" role="alert">
                <p class="font-bold text-lg">Succès</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Tableau des produits -->
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Produit</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-wider">Prix</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($products as $product)
                            <tr class="hover:bg-indigo-50 transition duration-200">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->id }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-indigo-600">
                                    <button onclick="openModal({{ $product->id }})" class="hover:underline focus:outline-none">
                                        {{ $product->name }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <span class="px-3 py-1 rounded-full font-bold text-xs
                                        {{ $product->stock_quantity < 5 ? 'bg-red-100 text-red-800' :
                                           ($product->stock_quantity < 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium space-x-4">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 transition">Modifier</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Supprimer {{ $product->name }} ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-lg">
                                    Aucun produit trouvé. Commencez par en ajouter un !
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="p-6 border-t border-gray-200 bg-gray-50">
                {{ $products->links('pagination::tailwind') }}
            </div>
        </div>

    </div>
</div>

<!-- Modales pour aperçu produit (une par produit, cachée) -->
@foreach ($products as $product)
    <div id="modal-{{ $product->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 p-8 relative transform scale-95 transition-transform duration-300">
            <button onclick="closeModal({{ $product->id }})" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Image -->
                <div class="flex-shrink-0">
                    <img src="{{ $product->image_url ? asset('storage/' . $product->image_url) : asset('images/placeholder.png') }}"
                         alt="{{ $product->name }}" class="h-48 w-48 object-cover rounded-xl shadow-lg border-2 border-gray-200">
                </div>
                <!-- Infos -->
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h2>
                    <p class="text-gray-600 mb-4">{{ $product->description ?? 'Aucune description disponible.' }}</p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-semibold text-gray-700">Catégorie :</span> {{ $product->category->name ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Prix :</span> {{ number_format($product->price, 0, ',', ' ') }} FCFA
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Stock :</span> {{ $product->stock_quantity }}
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Tailles :</span> {{ $product->sizes ? implode(', ', $product->sizes) : 'N/A' }}
                        </div>
                        <div class="col-span-2">
                            <span class="font-semibold text-gray-700">Slug :</span> {{ $product->slug }}
                        </div>
                        <div class="col-span-2">
                            <span class="font-semibold text-gray-700">Date de création :</span> {{ $product->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(`modal-${id}`);
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('div').classList.remove('scale-95');
            modal.classList.add('opacity-100');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = document.getElementById(`modal-${id}`);
        modal.querySelector('div').classList.add('scale-95');
        modal.classList.remove('opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Fermer avec Échap
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal-"]').forEach(modal => {
                if (!modal.classList.contains('hidden')) closeModal(modal.id.split('-')[1]);
            });
        }
    });
</script>
@endpush
@endsection