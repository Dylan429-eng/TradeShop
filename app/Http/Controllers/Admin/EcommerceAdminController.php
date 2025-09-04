<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Livraison;
use App\Models\Message;
use App\Models\Produit;
use App\Models\TransactionPaiement;
use App\Models\User;
use Illuminate\Http\Request;

class EcommerceAdminController extends Controller
{
    /**
     * Dashboard principal de l'admin e-commerce
     */
    public function dashboard()
    {
        $totalProduits = Produit::count();
        $totalCommandes = Commande::count();
        $totalClients = Client::count();
        $totalUsers = User::count();
        $commandesEnAttente = Commande::pending()->count();
        $livraisonsEnAttente = Livraison::pending()->count();
        $messagesNonLus = Message::unread()->count();
        
        // Statistiques de ventes
        $ventesTotales = Commande::sum('total_prix');
        $ventesAujourdhui = Commande::whereDate('date_cmd', today())->sum('total_prix');
        
        // Commandes récentes
        $commandesRecentes = Commande::with(['client', 'commandeDetails.produit'])
            ->latest()
            ->take(5)
            ->get();

        // Produits les plus vendus
        $produitsPopulaires = Produit::withCount('commandeDetails')
            ->orderBy('commande_details_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProduits',
            'totalCommandes',
            'totalClients',
            'totalUsers',
            'commandesEnAttente',
            'livraisonsEnAttente',
            'messagesNonLus',
            'ventesTotales',
            'ventesAujourdhui',
            'commandesRecentes',
            'produitsPopulaires'
        ));
    }
    public function storeCategorie(Request $request)
{
    $request->validate([
        'type' => 'required|string|max:255|unique:categories,type',
    ]);

    \App\Models\Categorie::create([
        'type' => $request->type,
    ]);

    return redirect()->route('admin.produits')->with('cat_success', 'Catégorie ajoutée avec succès !');
}
    /**
     * Gestion des produits
     */
    public function produits()
    {
        $produits = Produit::with(['categorie', 'user'])->paginate(15);
        $categories = Categorie::all();
        return view('admin.produits.index', compact('produits', 'categories'));
    }
     /**
     * Formulaire d'ajout de produit
     */
    public function createProduit()
    {
        $categories = Categorie::all();
        return view('admin.produits.create', compact('categories'));
    }
    /**
     * Enregistrer un nouveau produit
     */
    public function storeProduit(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        // Gestion de l'upload d'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/produits'), $imageName);
           $imagePath = 'images/produits/' . $imageName;
            $data['image'] = $imagePath;
        }
         // Vérifier si un produit identique existe déjà
        $existingProduit = Produit::where('nom', $data['nom'])
            ->where('description', $data['description'])
            ->where('prix', $data['prix'])
            ->where('categorie_id', $data['categorie_id'])
            ->where('user_id', $data['user_id'])
            ->first();

        if ($existingProduit) {
            // Additionner le stock au produit existant
            $existingProduit->increment('stock', $data['stock']);
            $message = 'Stock mis à jour pour le produit existant (+' . $data['stock'] . ' unités)';
        } else {
            // Créer un nouveau produit
            Produit::create($data);
            $message = 'Produit ajouté avec succès';
        }

        return redirect()->route('admin.produits')->with('success', $message);
    }
        /**
     * Formulaire d'édition de produit
     */
    public function editProduit($id)
    {
        $produit = Produit::findOrFail($id);
        $categories = Categorie::all();
        return view('admin.produits.edit', compact('produit', 'categories'));
    }
        /**
     * Mettre à jour un produit
     */
    public function updateProduit(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        // Gestion de l'upload d'image
        
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($produit->image && file_exists(public_path($produit->image))) {
                unlink(public_path($produit->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/produits'), $imageName);
            $data['image'] = 'images/produits/' . $imageName;
        }

        $produit->update($data);

        return redirect()->route('admin.produits')->with('success', 'Produit modifié avec succès');
    }
        /**
     * Supprimer un produit
     */
    public function deleteProduit($id)
    {
        $produit = Produit::findOrFail($id);

        // Supprimer l'image
        if ($produit->image && file_exists(public_path($produit->image))) {
            unlink(public_path($produit->image));
        }

        $produit->delete();

        return redirect()->route('admin.produits')->with('success', 'Produit supprimé avec succès');
    }

    /**
     * Gestion des commandes
     */
    public function commandes()
    {
        $commandes = Commande::with(['client', 'commandeDetails.produit','livraison.user'])
            ->latest()
            ->paginate(15);
        $montantTotal=Commande::sum('total_prix');
        $livreurs=User::byRole('livreur')->get();
        return view('admin.commandes.index', compact('commandes','montantTotal','livreurs'));
    }
    /**
     * assigner à un livreur une commande
     */
    public function assignerLivreur(Request $request, $commandeId)
{
    $request->validate([
        'livreur_id' => 'required|exists:users,id',
    ]);
    $commande = Commande::findOrFail($commandeId);

    // Créer ou mettre à jour la livraison
    $livraison = $commande->livraison ?: new Livraison();
    $livraison->commande_id = $commande->id;
    $livraison->user_id = $request->livreur_id;
    $livraison->client_id = $commande->client_id;
    $livraison->statut = 'en attente';
    $livraison->save();

     $livreur = User::find($request->livreur_id);
    $livreur->notify(new \App\Notifications\CommandeAssigneeNotification($commande));

    return back()->with('success', 'Livreur assigné avec succès.');
}
    //Retrait via CAMPAY
    public function retrait(Request $request)
{
    $request->validate([
        'phone_number' => 'required|string',
        'amount' => 'required|numeric',
    ]);

    $montantBrut = $request->amount;
    $frais = $montantBrut * 0.05;
    $montantNet = floor($montantBrut - $frais);
    $phone = $request->phone_number;
    $externalRef = \Illuminate\Support\Str::uuid()->toString();

    // Récupère le token depuis .env
    $token = env('CAMPAY_TOKEN');

    try {
        $withdrawResp = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Token ' . $token,
            'Content-Type' => 'application/json',
        ])->post('https://demo.campay.net/api/withdraw/', [
            'amount' => $montantNet,
            'to' => $phone,
            'description' => 'Retrait plateforme',
            'external_reference' => $externalRef,
        ]);

        $data = $withdrawResp->json();
        $reference = $data['reference'] ?? null;

        if (!$reference) {
            return back()->with('error', 'Erreur  : Solde insuffisant ou autre problème.');
        }

        $txResp = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Token ' . $token,
            'Content-Type' => 'application/json',
        ])->get("https://demo.campay.net/api/transaction/{$reference}/");

        $txData = $txResp->json();
        $status = $txData['status'] ?? 'inconnu';
        $operator = $txData['operator'] ?? 'mobile money';
        $type = 'retrait';
       TransactionPaiement::create([
            'type_transaction'   => $type, 
            'mode_paiement'      => $operator, 
            'statut'             => $status, 
            'date_transaction'   => now(),
            'montant_transaction'=> $montantNet,
            'user_id'            => $type === 'retrait' ? auth()->id() : null,
            'client_id'          => $type === 'depot' ? auth()->id() : null,
        ]);
        return back()->with('success', "Retrait de {$montantNet} FCFA lancé. Statut : $status");
    } catch (\Exception $e) {
        return back()->with('error', 'Erreur API Campay : ' . $e->getMessage());
    }
}
    public function showCommande($id)
{
    $commande = Commande::with(['client', 'commandeDetails.produit', 'livraison.user'])->findOrFail($id);
    return view('admin.commandes.show', compact('commande'));
}
    /**
     * Gestion des clients
     */
    public function clients()
    {
        $clients = Client::withCount('commandes')->paginate(15);
         
        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Gestion des utilisateurs (vendeurs, livreurs, etc.)
     */
    public function users()
    {
        $users = User::where('role', '!=', 'admin')->paginate(15);
        return view('admin.employes.index', compact('users'));
    }

    /**
     * Gestion des catégories
     */
    public function categories()
    {
        $categories = Categorie::withCount('produits')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Gestion des livraisons
     */
    public function livraisons()
    {
        $livraisons = Livraison::with(['user', 'client'])->latest()->paginate(15);
        return view('admin.livraisons.index', compact('livraisons'));
    }

    /**
     * Gestion des transactions de paiement
     */
    public function transactions()
    {
        $transactions = TransactionPaiement::with(['user', 'client'])
            ->latest()
            ->paginate(15);
        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Gestion des messages/support
     */
    public function messages()
    {
        $messages = Message::latest()->paginate(15);
        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Statistiques détaillées
     */
   
public function statistiques(Request $request)
    {
        $periode = $request->input('periode', 'mois_actuel');
        $now = now();

        switch ($periode) {
            case 'mois_actuel':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'mois_precedent':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                break;
            case 'deux_derniers_mois':
                $start = $now->copy()->subMonths(2)->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'semaine_actuelle':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'semaine_precedente':
                $start = $now->copy()->subWeek()->startOfWeek();
                $end = $now->copy()->subWeek()->endOfWeek();
                break;
            case 'deux_derniere_semaines':
                $start = $now->copy()->subWeeks(2)->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
        }

        // Statistiques produits vendus sur la période (CORRIGÉ)
        $produitsStats = \DB::table('produits')
            ->select(
                'produits.id',
                'produits.nom', 
                \DB::raw('COALESCE(SUM(CASE 
                    WHEN commandes.date_cmd BETWEEN ? AND ? 
                    AND commandes.statut IN ("livré", "confirmé") 
                    THEN commande_details.quantity 
                    ELSE 0 END), 0) as ventes')
            )
            ->leftJoin('commande_details', 'produits.id', '=', 'commande_details.produit_id')
            ->leftJoin('commandes', 'commande_details.commande_id', '=', 'commandes.id')
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('ventes')
            ->setBindings([$start, $end])
            ->get();

        $plusVendus = $produitsStats->sortByDesc('ventes')->take(5)->values();
        $moinsVendus = $produitsStats->sortBy('ventes')->take(5)->values();

        return view('admin.statistiques.index', compact(
            'plusVendus',
            'moinsVendus',
            'produitsStats',
            'periode'
        ));
    }
}