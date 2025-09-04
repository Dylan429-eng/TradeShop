<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EcommerceAdminController;
use App\Http\Controllers\Livreur\LivreurController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('InterneHomePage');
});

// Route dashboard supprimée car elle entre en conflit avec admin.dashboard

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Auth::routes();

// Routes admin pour l'e-commerce
Route::middleware(['auth'])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [EcommerceAdminController::class, 'dashboard'])->name('dashboard');
    // Gestion des produits
    Route::get('/produits', [EcommerceAdminController::class, 'produits'])->name('produits');
    Route::get('/produits/create', [EcommerceAdminController::class, 'createProduit'])->name('produits.create');
    Route::post('/produitStore', [EcommerceAdminController::class, 'storeProduit'])->name('produits.store');
    Route::post('/categories/store', [EcommerceAdminController::class, 'storeCategorie'])->name('categories.store');
    Route::get('/produits/{id}/edit', [EcommerceAdminController::class, 'editProduit'])->name('produits.edit');
    Route::put('/produitUpdate/{id}', [EcommerceAdminController::class, 'updateProduit'])->name('produits.update');
    Route::delete('/produits/{id}', [EcommerceAdminController::class, 'deleteProduit'])->name('produits.delete');
    //Gestion des Commandes
    Route::get('/commandes', [EcommerceAdminController::class, 'commandes'])->name('commandes');
    Route::get('/commandes/{commande}', [EcommerceAdminController::class, 'showCommande'])->name('commandes.show');
    Route::post('/commandes/{commande}/assigner', [EcommerceAdminController::class, 'assignerLivreur'])->name('commandes.assigner');
    Route::post('/commandes/retrait', [EcommerceAdminController::class, 'retrait'])->name('commandes.retrait');
    
    Route::get('/clients', [EcommerceAdminController::class, 'clients'])->name('clients');
    Route::get('/users', [EcommerceAdminController::class, 'users'])->name('users');
    Route::delete('/admin/users/{id}/delete', [EcommerceAdminController::class, 'deleteUser'])->name('users.delete');
    
    Route::get('/categories', [EcommerceAdminController::class, 'categories'])->name('categories');
    Route::get('/livraisons', [EcommerceAdminController::class, 'livraisons'])->name('livraisons');
    Route::get('/transactions', [EcommerceAdminController::class, 'transactions'])->name('transactions');
    Route::get('/messages', [EcommerceAdminController::class, 'messages'])->name('messages');
    Route::get('/statistiques', [EcommerceAdminController::class, 'statistiques'])->name('statistiques');
});
Route::middleware(['auth'])->prefix('/livreur')->name('livreur.')->group(function () {
   Route::get('/dashboard', [LivreurController::class, 'dashboard'])->name('dashboard');
    Route::post('/commandes/{commande}/confirmer',[LivreurController::class, 'confirmerLivraison'])
    ->name('commandes.confirmer');
});