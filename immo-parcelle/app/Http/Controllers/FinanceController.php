<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\Parcelle;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index()
    {
        // 1. Calcul des KPI financiers basés sur vos tables SQL
        
        // Somme de toutes les mensualités dues pour les parcelles occupées (non disponibles)
        $total_attendu_global = Parcelle::where('statut', '!=', 'disponible')->sum('mensualite');

        // Somme des paiements approuvés/validés (Statut supposé 'valide')
        $total_encaisse_global = Paiement::where('statut', 'valide')->sum('montant_paye');

        // Somme des paiements actuellement en attente d'arbitrage admin
        $total_attente_global = Paiement::where('statut', 'en_attente')->sum('montant_paye');

        // Calcul du pourcentage de recouvrement réalisé
        $pct_encaisse = $total_attendu_global > 0 
            ? round(($total_encaisse_global / $total_attendu_global) * 100) 
            : 0;

        // 2. Récupération des paiements en attente avec leurs relations (Eager Loading)
        // Jointures automatiques sur les clés étrangères définies dans votre structure SQL
        $paiements = Paiement::with(['user.profil', 'parcelle'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.finance.index', compact(
            'total_attendu_global',
            'total_encaisse_global',
            'total_attente_global',
            'pct_encaisse',
            'paiements'
        ));
    }

    /**
     * Traitement de l'approbation ou du rejet d'un paiement en BDD
     */
    public function actionPaiement(Request $request, $id, $action)
    {
        $paiement = Paiement::findOrFail($id);

        if ($action === 'approuver') {
            $paiement->update(['statut' => 'valide']);
            
            // Logique optionnelle additionnelle : Mettre à jour le statut de la parcelle liée si nécessaire
            // $paiement->parcelle->update(['statut' => 'occupe']);

            $message = "Le paiement a été approuvé avec succès.";
        } else {
            $paiement->update(['statut' => 'rejete']);
            $message = "Le paiement a été refusé.";
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }
}