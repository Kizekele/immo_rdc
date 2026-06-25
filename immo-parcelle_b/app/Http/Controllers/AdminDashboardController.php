<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Parcelle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminDashboardController extends Controller
{
    public function index()
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

        return view('dashboard-admin', compact(
            'total_attendu_global',
            'total_encaisse_global',
            'total_attente_global',
            'pct_encaisse',
            'paiements'
        ));

        //evolution individuelle des clients
        $historique = Paiement::with(['user', 'parcelle'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy('user_id');

        return view('dashboard-admin', compact('total_attendu_global', 'total_encaisse_global', 'total_attente_global', 'pct_encaisse', 'paiements', 'historique'));

        $paiementsIndividuel = Paiement::with(['user', 'parcelle'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('dashboard-admin', compact('total_attendu_global', 'total_encaisse_global', 'total_attente_global', 'pct_encaisse', 'paiements', 'historique', 'paiementsIndividuel'));

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
            'prix'         => 'required|numeric|min:0', 
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
                'prix_total'   => $validated['prix'],
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