
@extends('layouts.app')

@section('title', 'Ajouter un Produit - Admin E-commerce')
<link rel="stylesheet" href="{{asset('css/produits/create.css')}}">

@section('content')
<div class="min-h-screen">
    <div class="max-w-4xl">
        <!-- Header -->
        <div class="header-flex">
            <div>
                <h1 class="header-title">Ajouter un Produit</h1>
                <p class="header-desc">Créez un nouveau produit pour votre catalogue</p>
            </div>
            <a href="{{ route('admin.produits') }}" class="btn-back">
                <svg style="width:20px;height:20px;margin-right:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>

        <!-- Formulaire -->
        <div class="form-container">
            <form method="POST" action="{{ route('admin.produits.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Nom du produit -->
                <div>
                    <label for="nom" class="form-label">
                        Nom du produit <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                           class="form-input @error('nom') border-red-500 @enderror"
                           placeholder="Ex: Baguette traditionnelle">
                    @error('nom')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="form-label">
                        Description <span style="color:#dc2626;">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required
                              class="form-textarea @error('description') border-red-500 @enderror"
                              placeholder="Décrivez votre produit...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prix et Stock -->
                <div class="grid-2">
                    <div>
                        <label for="prix" class="form-label">
                            Prix (FCFA) <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="number" id="prix" name="prix" value="{{ old('prix') }}" 
                               step="0.01" min="0" required
                               class="form-input @error('prix') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('prix')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="stock" class="form-label">
                            Stock <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock') }}" 
                               min="0" required
                               class="form-input @error('stock') border-red-500 @enderror"
                               placeholder="0">
                        @error('stock')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Catégorie -->
                <div>
                    <label for="categorie_id" class="form-label">
                        Catégorie <span style="color:#dc2626;">*</span>
                    </label>
                    <select id="categorie_id" name="categorie_id" required
                            class="form-select @error('categorie_id') border-red-500 @enderror">
                        <option value="">Sélectionnez une catégorie</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->type }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div>
                    <label for="image" class="form-label">
                        Image du produit
                    </label>
                    <div class="image-upload-box">
                        <div style="text-align:center;">
                            <svg style="width:48px;height:48px;color:#9ca3af;margin-bottom:1rem;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <label for="image" class="image-upload-label">
                                Télécharger une image
                                <input id="image" name="image" type="file" class="sr-only" accept="image/*" style="display:none;">
                            </label>
                            <span class="image-upload-desc">ou glisser-déposer</span>
                            <div class="image-upload-info">PNG, JPG, GIF jusqu'à 2MB</div>
                        </div>
                    </div>
                    @error('image')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Boutons d'action -->
                <div class="action-btns">
                    <a href="{{ route('admin.produits') }}" class="btn-cancel">
                        Annuler
                    </a>
                    <button type="submit" class="btn-submit">
                        <svg style="width:20px;height:20px;margin-right:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Créer le produit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(loadEvent) {
            const preview = document.createElement('img');
            preview.src = loadEvent.target.result;
            preview.className = 'image-preview';

            // Supprimer l'ancienne prévisualisation
            const existingPreview = document.querySelector('.image-preview');
            if (existingPreview) {
                existingPreview.remove();
            }

            // Attacher l’image à la boîte d’upload
            const container = document.querySelector('.image-upload-box > div');
            container.appendChild(preview);
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection