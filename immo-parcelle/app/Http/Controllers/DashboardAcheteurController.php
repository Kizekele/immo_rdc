<?php

namespace App\Http\Controllers;

use App\Models\CompteMobile;
use App\Models\Paiement;
use App\Models\Parcelle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardAcheteurController extends Controller
{
    public function index()
    {
        // Récupère l'utilisateur connecté (ou fallback ID 2 pour les tests)
        $user = Auth::user() ?? \App\Models\User::find(2);

        if (!$user) {
            abort(404, "Aucun utilisateur trouvé.");
        }

        $profil = $user->profil;
        $client = $profil ? ($profil->prenom . ' ' . $profil->nom) : $user->name;

        // 1. Récupérer TOUTES les parcelles appartenant à ce client
        $parcelles = Parcelle::where('user_id', $user->id)->get();

        if ($parcelles->isEmpty()) {
            return view('dashboard-acheteur', [
                'client' => $client,
                'parcelles' => collect(),
                'prix_total' => 0,
                'deja_paye' => 0,
                'mois_retard' => null,
                'mois_actuel' => ucfirst(now()->locale('fr')->translatedFormat('F')),
                'comptesMobiles' => CompteMobile::all()
            ]);
        }

        // 2. Calculs globaux (KPI du haut)
        $prix_total = $parcelles->sum('prix_total');
        $mensualite_de_base = $parcelles->sum('mensualite');

        // Total cumulé déjà payé et validé pour toutes ses parcelles
        $deja_paye = Paiement::where('user_id', $user->id)
            ->whereIn('parcelle_id', $parcelles->pluck('id'))
            ->where('statut', 'valide')
            ->sum('montant_paye');

        // 3. Calculer la progression individuelle pour chaque parcelle
        foreach ($parcelles as $parcelle) {
            // Somme des paiements validés spécifiquement pour CETTE parcelle
            $parcelle->somme_payee = Paiement::where('user_id', $user->id)
                ->where('parcelle_id', $parcelle->id)
                ->where('statut', 'valide')
                ->sum('montant_paye');

            // Pourcentage de progression de cette parcelle
            $parcelle->pourcentage = $parcelle->prix_total > 0 
                ? round(($parcelle->somme_payee / $parcelle->prix_total) * 100, 1) 
                : 0;
        }

        // 4. Gestion des retards (Simplifiée globalement sur la mensualité totale requise)
        Carbon::setLocale('fr');
        $mois_actuel = ucfirst(now()->translatedFormat('F'));

        $dejaPayeCeMois = Paiement::where('user_id', $user->id)
            ->where('mois_concerne', $mois_actuel)
            ->exists();

        $mois_retard = null;
        $penalite = 0;
        $montant_actuel = $mensualite_de_base;
        $montant_retard = 0;

        if (!$dejaPayeCeMois) {
            $mois_precedent = ucfirst(now()->subMonth()->translatedFormat('F'));
            $payeMoisPrecedent = Paiement::where('user_id', $user->id)
                ->where('mois_concerne', $mois_precedent)
                ->exists();

            if (!$payeMoisPrecedent) {
                $mois_retard = $mois_precedent;
                $montant_retard = $mensualite_de_base;
                $penalite = $mensualite_de_base * 0.10; 
            }
        }

        $comptesMobiles = CompteMobile::all();

        return view('dashboard-acheteur', compact(
            'client',
            'parcelles',
            'prix_total',
            'deja_paye',
            'mois_retard',
            'mois_actuel',
            'penalite',
            'montant_actuel',
            'montant_retard',
            'comptesMobiles'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'parcelle_id'  => 'required|exists:parcelles,id',
            'preuve_photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $user = Auth::user() ?? \App\Models\User::find(2);
        
        // Vérifie que la parcelle appartient bien à l'utilisateur connecté
        $parcelleEntity = Parcelle::where('id', $request->parcelle_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $path = $request->file('preuve_photo')->store('preuves', 'public');

        Carbon::setLocale('fr');
        $mois_actuel = ucfirst(now()->translatedFormat('F'));

        Paiement::create([
            'parcelle_id'        => $parcelleEntity->id,
            'user_id'            => $user->id,
            'montant_paye'       => $parcelleEntity->mensualite,
            'mois_concerne'      => $mois_actuel,
            'preuve_photo'       => $path,
            'statut'             => 'en_attente',
            'penalite_appliquee' => 0
        ]);

        return redirect()->back()->with('success', 'Votre preuve de virement a été transmise avec succès pour cette parcelle.');
    }
}