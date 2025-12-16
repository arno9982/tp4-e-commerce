{{-- resources/views/products/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier le produit : ' . $product->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 flex items-center gap-3">
                Modifier le produit
            </h1>
            <p class="mt-2 text-lg text-gray-600">Produit : <span class="font-semibold text-indigo-600">{{ $product->name }}</span></p>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('admin.products.update', $product) }}"
              method="POST"
              enctype="multipart/form-data"
              class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">

            @csrf
            @method('PUT')

            <div class="p-8 lg:p-10 space-y-8">

                <!-- 1. Nom & Catégorie -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nom du produit <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name', $product->name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id" name="category_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow @error('category_id') border-red-500 @enderror">
                            <option value="">Choisir une catégorie</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 2. Prix & Stock -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                            Prix (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" id="price" required step="0.01" min="0"
                               value="{{ old('price', $product->price) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                            Quantité en stock <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock_quantity" id="stock_quantity" required min="0"
                               value="{{ old('stock_quantity', $product->stock_quantity) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('stock_quantity') border-red-500 @enderror">
                        @error('stock_quantity')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 3. Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="5"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 3.5 Tailles (dynamiques) -->
                <div id="sizes-container" class="hidden p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                    <label class="block text-lg font-semibold text-gray-800 mb-4">Tailles disponibles</label>
                    <div id="sizes-list" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                        <!-- Rempli par JS -->
                    </div>
                    @error('sizes')
                        <p class="mt-4 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 4. Image du produit -->
                <div class="bg-gray-50 rounded-xl p-6 border-2 border-dashed border-gray-300">
                    <label class="block text-lg font-semibold text-gray-800 mb-4">Image du produit</label>

                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <!-- Prévisualisation actuelle -->
                        <div class="flex-shrink-0">
                            <img id="image-preview"
                                 src="{{ $product->image_url ? asset('storage/' . $product->image_url) : asset('images/placeholder.png') }}"
                                 alt="Prévisualisation"
                                 class="h-32 w-32 object-cover rounded-xl shadow-lg border-4 border-white {{ $product->image_url ? '' : 'hidden' }}">
                        </div>

                        <div class="flex-1 text-center sm:text-left">
                            <input type="file" name="image" id="image" accept="image/*" class="hidden">
                            <label for="image"
                                   class="cursor-pointer inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-md transition transform hover:scale-105">
                                Changer l’image
                            </label>
                            <p class="mt-3 text-sm text-gray-600">
                                @if($product->image_url) Image actuelle présente @else Aucune image @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-2">
                                JPG, PNG, WebP, GIF • Max 5 Mo • Min 400×400px recommandé
                            </p>
                        </div>
                    </div>

                    <div id="image-info" class="mt-4 p-4 bg-white rounded-lg border hidden">
                        <p class="text-sm text-gray-700">
                            <strong>Dimensions :</strong> <span id="image-dimensions"></span><br>
                            <strong>Taille :</strong> <span id="image-size"></span><br>
                            <strong>Format :</strong> <span id="image-format"></span>
                        </p>
                    </div>

                    @error('image')
                        <p class="mt-4 text-sm text-red-600 font-medium">Erreur : {{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.products.index') }}"
                       class="px-8 py-3 border border-gray-300 rounded-xl font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-10 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition duration-200">
                        Sauvegarder les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// URL de l'API pour obtenir les tailles selon la catégorie
const CATEGORY_SIZES_URL = @json(url('/api/category-sizes'));

// Tailles actuelles du produit (peuvent venir de la BDD ou de old())
const currentSizes = @json($product->sizes ?? []);
const oldSizes = @json(old('sizes', []));

// Éléments DOM
const categorySelect = document.getElementById('category_id');
const sizesContainer = document.getElementById('sizes-container');
const sizesList = document.getElementById('sizes-list');

async function updateSizes() {
    if (!categorySelect || !sizesContainer || !sizesList) return;

    const categoryId = categorySelect.value;

    // Cacher et vider par défaut
    sizesContainer.classList.add('hidden');
    sizesList.innerHTML = '';

    if (!categoryId) return;

    try {
        const response = await fetch(`${CATEGORY_SIZES_URL}/${categoryId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('HTTP ' + response.status);

        const { sizes } = await response.json();

        if (!sizes || sizes.length === 0) return;

        // Afficher le container
        sizesContainer.classList.remove('hidden');

        // Générer les checkboxes
        sizesList.innerHTML = sizes.map(size => {
            // Cocher si la taille est dans currentSizes OU dans old('sizes') après erreur de validation
            const isChecked = [...currentSizes, ...oldSizes].includes(size);
            return `
                <label class="flex items-center p-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 transition">
                    <input type="checkbox" name="sizes[]" value="${size}" class="rounded text-indigo-600" ${isChecked ? 'checked' : ''}>
                    <span class="ml-2 text-sm font-medium">${size}</span>
                </label>
            `;
        }).join('');

    } catch (err) {
        console.error('Erreur chargement tailles:', err);
    }
}

// Prévisualisation d'image (améliorée et simplifiée)
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('image-preview');
    const fileNameSpan = document.getElementById('file-name');
    const imageInfo = document.getElementById('image-info');
    const dimensionsSpan = document.getElementById('image-dimensions');
    const sizeSpan = document.getElementById('image-size');
    const formatSpan = document.getElementById('image-format');

    // Si on annule la sélection → revenir à l'image existante
    if (!input.files || !input.files[0]) {
        preview.src = "{{ $product->image_url ? asset('storage/' . $product->image_url) : asset('images/placeholder.png') }}";
        preview.classList.toggle('hidden', !{{ $product->image_url ? 'true' : 'false' }});
        fileNameSpan.textContent = "{{ $product->image_url ? 'Image actuelle' : 'Aucune image' }}";
        imageInfo.classList.add('hidden');
        return;
    }

    const file = input.files[0];
    if (file.size > 5 * 1024 * 1024) {
        alert('Image trop lourde (max 5 Mo)');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        const img = new Image();
        img.onload = () => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            fileNameSpan.textContent = file.name;
            dimensionsSpan.textContent = `${img.width} × ${img.height} px`;
            sizeSpan.textContent = (file.size / 1024).toFixed(1) + ' KB';
            formatSpan.textContent = file.type.split('/')[1].toUpperCase();
            imageInfo.classList.remove('hidden');
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// AU CHARGEMENT DE LA PAGE → ON LANCE LA MAGIE
document.addEventListener('DOMContentLoaded', () => {
    const imageInput = document.getElementById('image');

    // Très important : charger les tailles dès le départ
    updateSizes();

    // Et aussi à chaque changement de catégorie
    categorySelect?.addEventListener('change', updateSizes);

    // Prévisualisation image
    image?.addEventListener('change', previewImage);
});
</script>
@endpush
@endsection