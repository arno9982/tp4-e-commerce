{{-- resources/views/products/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajouter un nouveau produit')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Ajouter un nouveau produit</h1>

        {{-- Formulaire d'ajout --}}
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 space-y-6">
            @csrf

            {{-- 1. Informations de base --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Nom du produit --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du produit <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catégorie --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 @error('category_id') border-red-500 @enderror">
                        <option value="">Sélectionnez une catégorie</option>
                        {{-- La variable $categories est passée depuis ProductController::create() --}}
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 2. Prix et Stock --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Prix --}}
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Prix (FCFA) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" required step="0.01" min="0" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stock --}}
                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantité en stock <span class="text-red-500">*</span></label>
                    <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity') ?? 0 }}" required min="0" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 @error('stock_quantity') border-red-500 @enderror">
                    @error('stock_quantity')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 3. Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="5" 
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- 3.5 Tailles (selon la catégorie) --}}
            <div id="sizes-container" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tailles disponibles</label>
                <div id="sizes-list" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
                    <!-- Les tailles seront générées dynamiquement par JavaScript -->
                </div>
                @error('sizes')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- 4. Image --}}
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Image du produit</label>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <input type="file" name="image" id="image" 
                               class="hidden" accept="image/jpeg,image/png,image/webp,image/gif">
                        
                        <label for="image" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                             Choisir une image
                        </label>

                        {{-- Zone de prévisualisation --}}
                        <img id="image-preview" class="h-20 w-20 object-cover rounded-md border-2 border-gray-300 hidden" alt="Prévisualisation de l'image">
                        
                        <span id="file-name" class="text-sm text-gray-500">Aucun fichier sélectionné</span>
                    </div>
                    
                    {{-- Informations sur l'image --}}
                    <div id="image-info" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-md text-sm">
                        <p class="text-gray-600">
                            <strong>Dimensions:</strong> <span id="image-dimensions">—</span><br>
                            <strong>Taille:</strong> <span id="image-size">—</span><br>
                            <strong>Format:</strong> <span id="image-format">—</span>
                        </p>
                    </div>

                    {{-- Recommandations --}}
                    <p class="text-xs text-gray-500">
                        ✓ Formats autorisés: JPEG, PNG, WebP, GIF<br>
                        ✓ Taille maximale: 5 MB<br>
                        ✓ Dimensions minimales: 100×100 px<br>
                        ✓ Dimensions recommandées: 400×400 px ou plus
                    </p>
                </div>
                @error('image')
                    <p class="mt-2 text-sm text-red-500 font-medium">❌ {{ $message }}</p>
                @enderror
            </div>

            {{-- Boutons d'action --}}
            <div class="pt-4 flex justify-end space-x-3">
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Créer le produit
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Toutes les fonctions sont déplacées hors de DOMContentLoaded pour garantir leur accessibilité
    
    // 1. Fonction pour gérer la logique des tailles
    async function updateSizes() {
        // ... (le contenu de updateSizes() reste le même)
        const categorySelect = document.getElementById('category_id');
        const sizesContainer = document.getElementById('sizes-container');
        const sizesList = document.getElementById('sizes-list');
        
        if (!categorySelect || !sizesContainer || !sizesList) {
            console.error('Un ou plusieurs éléments manquent');
            return;
        }

        const selectedCategoryId = categorySelect.value;

        if (!selectedCategoryId) {
            sizesContainer.classList.add('hidden');
            sizesList.innerHTML = '';
            return;
        }

        try {
            // NOTE: Assurez-vous que cette route existe et retourne JSON {"sizes": ["S", "M", "L"]}
            const response = await fetch(`{{ url('/api/category-sizes') }}/${selectedCategoryId}`);
            const data = await response.json();
            const sizes = data.sizes || [];
            
            if (sizes.length === 0) {
                sizesContainer.classList.add('hidden');
                sizesList.innerHTML = '';
                return;
            }

            sizesContainer.classList.remove('hidden');
            
            sizesList.innerHTML = sizes.map(size => `
                <label class="flex items-center p-2 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50 transition">
                    <input type="checkbox" name="sizes[]" value="${size}" class="rounded">
                    <span class="ml-2 text-sm font-medium">${size}</span>
                </label>
            `).join('');
        } catch (error) {
            console.error('Erreur lors de la récupération des tailles:', error);
        }
    }

    // 2. Fonction pour gérer la prévisualisation d'image
    function previewImage() {
        const input = document.getElementById('image');
        const preview = document.getElementById('image-preview');
        const fileNameSpan = document.getElementById('file-name');
        const imageInfo = document.getElementById('image-info');
        const dimensionsSpan = document.getElementById('image-dimensions');
        const sizeSpan = document.getElementById('image-size');
        const formatSpan = document.getElementById('image-format');

        if (!input.files || !input.files[0]) {
            preview.classList.add('hidden');
            fileNameSpan.textContent = 'Aucun fichier sélectionné';
            imageInfo.classList.add('hidden');
            return;
        }

        const file = input.files[0];
        const maxSize = 5 * 1024 * 1024; // 5 MB
        const allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        // Validation de Taille
        if (file.size > maxSize) {
            fileNameSpan.textContent = '❌ Fichier trop volumineux (max 5 MB)';
            input.value = '';
            alert('Le fichier dépasse 5 MB. Veuillez compresser votre image.');
            return;
        }

        // Validation de Type MIME
        if (!allowedMimes.includes(file.type)) {
            fileNameSpan.textContent = '❌ Format non autorisé';
            input.value = '';
            alert('Format d\'image non autorisé. Utilisez JPEG, PNG, WebP ou GIF.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                // Afficher la prévisualisation
                preview.src = e.target.result;
                preview.classList.remove('hidden');

                // Afficher les informations
                dimensionsSpan.textContent = `${img.width} × ${img.height} px`;
                sizeSpan.textContent = (file.size / 1024).toFixed(2) + ' KB';
                formatSpan.textContent = file.type.split('/')[1].toUpperCase();
                fileNameSpan.textContent = file.name;
                imageInfo.classList.remove('hidden');

                // Avertissement de dimension
                if (img.width < 100 || img.height < 100) {
                    alert('⚠️ Attention: L\'image doit faire au minimum 100×100 pixels. Votre image fait ' + img.width + '×' + img.height + ' pixels.');
                }
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
    
    // 3. Gestion des événements au chargement du DOM
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_id');
        const imageInput = document.getElementById('image'); // NOUVEAU

        if (categorySelect) {
            // Écouteur pour la catégorie
            categorySelect.addEventListener('change', updateSizes);
            // Appel initial pour gérer old()
            updateSizes();
        }
        
        if (imageInput) {
            // Écouteur pour l'image (Remplacement du onchange inline)
            imageInput.addEventListener('change', previewImage);
        }
    });
</script>
@endpush
@endsection