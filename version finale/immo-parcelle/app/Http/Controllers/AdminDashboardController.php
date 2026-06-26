<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Parcelle;
use App\Models\CompteMobile;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Vérification simple du rôle
        if (auth()->user()->role !== 'administrateur') {
            abort(403, 'Accès refusé.');
        }
        // 1. Calcul du total attendu global
        $total_attendu_global = Parcelle::whereNotNull('user_id')->sum('prix_total');

        // 2. Calcul du total déjà encaissé (statut valide)
        $total_encaisse_global = Paiement::where('statut', 'valide')->sum('montant_paye');

        // 3. Calcul du flux en attente de validation
        $total_attente_global = Paiement::where('statut', 'en_attente')->sum('montant_paye');

        // 4. Calcul du pourcentage de progression
        $pct_encaisse = $total_attendu_global > 0 
            ? round(($total_encaisse_global / $total_attendu_global) * 100) 
            : 0;

        // 5. Récupération des paiements en attente avec eager loading
        $paiements = Paiement::with(['user.profil', 'parcelle'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->get();

        $moisDisponibles = Paiement::distinct()->pluck('mois_concerne');

        // Récupérer le filtre depuis la requête GET
        $moisFiltre = $request->input('mois');

        // Calcul du nombre de clients actifs (ex: nombre d'utilisateurs avec des parcelles)
        $nb_clients_actifs = \App\Models\User::where('role', 'client')
            ->whereHas('parcelles') 
            ->count();
        // Récupération de tous les utilisateurs avec le rôle 'client'
        $clients = \App\Models\User::where('role', 'client')->get();

        // Calcul des parcelles libres et du total
        $nb_parcelles_libres = Parcelle::where('statut', 'disponible')->count();
        $nb_parcelles_total = Parcelle::count();

        // Récupération des parcelles avec leurs paiements pour le suivi
        $suiviGlobal = Parcelle::with(['user.profil'])
            ->withSum('paiements as somme_payee', 'montant_paye')
            ->get();
        // Récupération de l'historique complet des paiements/transactions
        $historiqueTransactions = Paiement::with(['user.profil', 'parcelle'])
            ->orderBy('created_at', 'desc')
            ->get();


        return view('dashboard-admin', compact(
            'total_attendu_global',
            'total_encaisse_global',
            'total_attente_global',
            'pct_encaisse',
            'paiements',
            'moisDisponibles',
            'moisFiltre',
            'nb_clients_actifs',
            'clients',
            'nb_parcelles_libres',
            'nb_parcelles_total',
            'suiviGlobal',
            'historiqueTransactions'
        ));

    }

    public function exportPdf($type)
    {
        // On récupère les données dynamiquement
        $data = match($type) {
            'paiements' => ['paiements' => Paiement::all()],
            'clients'   => ['clients'   => User::where('role', 'client')->get()],
            'parcelles' => ['parcelles' => Parcelle::all()],
            default     => abort(404)
        };

        // La vue est cherchée dans resources/views/pdf/{type}.blade.php
        $pdf = Pdf::loadView("pdf.$type", $data);
        
        return $pdf->download("Rapport_" . ucfirst($type) . "_" . date('Y-m-d') . ".pdf");
    }
    public function validerApprobation(Request $request, $id)
    {
        // 1. Validation : la photo est obligatoire
        $request->validate([
            'preuvePhoto_admin' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $paiement = Paiement::findOrFail($id);

        // 2. Traitement de la photo
        if ($request->hasFile('preuve_admin')) {
            $path = $request->file('preuvePhoto_admin')->store('preuves_validation', 'public');
            
            // 3. Mise à jour du paiement
            $paiement->update([
                'statut'         => 'valide',
                'preuvePhoto_admin'   => $path, // Assurez-vous que cette colonne existe dans votre table paiements
                'valide_par'     => auth()->id(), // Optionnel : pour savoir quel admin a validé
                'date_validation'=> now(),
            ]);
        }

        return back()->with('success', 'Paiement approuvé et preuve enregistrée.');
    }

    public function traitementAction(Request $request, $id, $action)
    {
        // Sécurité : On vérifie immédiatement si l'action est autorisée
        if (!in_array($action, ['approuver', 'refuser'])) {
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Action non autorisée.'], 400)
                : back()->with('error', 'Action non autorisée.');
        }

        $paiement = Paiement::findOrFail($id);

        // Assignation du nouveau statut
        $paiement->statut = ($action === 'approuver') ? 'valide' : 'rejete';
        $paiement->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'statut' => $paiement->statut]);
        }

        return back()->with('success', 'Le paiement a été traité avec succès.');
    }

    public function storeParcelle(Request $request)
    {
        // 1. Validation des données reçues
        $validated = $request->validate([
            'titre'        => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'dimensions'   => 'required|string|max:255',
            'prix_total'         => 'required|numeric|min:0', 
            'mensualite'   => 'required|numeric|min:0',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // 2. Traitement du fichier image s'il existe
            $path = null;
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('parcelles', 'public');
            }

            // 3. Création de la parcelle en base de données
            Parcelle::create([
                'titre'        => $validated['titre'],
                'localisation' => $validated['localisation'],
                'dimensions'   => $validated['dimensions'], // Optionnel
                'prix_total'   => $validated['prix_total'],
                'mensualite'   => $validated['mensualite'],
                'statut'       => 'disponible',
                'user_id'      => null,
                'photo'        => $path,
            ]);

             return redirect()->back()->with(
                'success',
                'Parcelle enregistrée avec succès.'
            );

        } catch (Exception $e) {
            // Sécurité : On log l'erreur réelle pour le dev, mais on évite d'exposer les détails SQL en production
            Log::error("Erreur storeParcelle: " . $e->getMessage());

            return response()->json([
                'success' => false, 
                'message' => 'Erreur lors de l\'enregistrement technique.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Une erreur interne est survenue.'
            ], 500);
        }
    }
}