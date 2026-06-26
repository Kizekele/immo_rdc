<?php

namespace App\Http\Controllers;

use App\Models\Parcelle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParcelleController extends Controller
{
    public function adminIndex()
    {
        // On utilise 'with' pour charger les utilisateurs associés (Eager Loading)
        // On récupère les parcelles avec leur utilisateur associé
        $parcelles = Parcelle::with('user')->get();
        return view('dashboard-admin', compact('parcelles'));
    }
        
    public function index()
    {
        // On récupère toutes les parcelles de la BDD
        $parcelles = Parcelle::all();

        // On compte le nombre de parcelles dont le statut est 'disponible'
        $parcellesLibresCount = Parcelle::where('statut', 'disponible')->count();

        // On envoie le tout à la vue "parcelle-show" (ou le nom de ta vue actuelle)
        return view('parcelle-show', compact('parcelles', 'parcellesLibresCount'));

    }

    public function souscrire(Request $request, $id)
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($id) {
            $user = Auth::user();

            // 1. Vérification : L'utilisateur ne doit pas posséder plus de 3 parcelles
            $nombreParcelles = Parcelle::where('user_id', $user->id)->count();

            if ($nombreParcelles >= 3) {
                return back()->withErrors(['message' => 'Vous avez atteint la limite maximale de 3 parcelles.']);
            }

            // 2. Récupération et verrouillage de la parcelle (pessimistic lock) 
            // pour éviter qu'un autre utilisateur ne la prenne au même moment
            $parcelle = Parcelle::where('id', $id)->lockForUpdate()->firstOrFail();

            // 3. Vérification : La parcelle doit être disponible
            if ($parcelle->user_id !== null || $parcelle->statut !== 'disponible') {
                return back()->withErrors(['message' => 'Désolé, cette parcelle n\'est plus disponible.']);
            }

            // 4. Mise à jour de la parcelle
            $parcelle->update([
                'user_id' => $user->id,
                'statut'  => 'reservee'
            ]);

            return redirect()->route('dashboard-acheteur')
                             ->with('success', 'Félicitations ! Vous avez souscrit à la parcelle ' . $parcelle->titre);
        });
    }
}