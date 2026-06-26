<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Parcelle;
use App\Models\Paiement;
use Illuminate\Support\Facades\Hash;

class GestionClientController extends Controller
{
    /**
     * Liste des clients avec leurs stats d'évolution
     */
    public function index()
    {
        $clients = User::with(['parcelles', 'profil'])
            ->where('role', '!=', 'administrateur')
            ->get()
            ->map(function ($client) {
                $client->total_du   = $client->parcelles->sum('prix_total');
                $client->total_paye = Paiement::where('user_id', $client->id)
                    ->where('statut', 'valide')->sum('montant_paye');
                $client->pct = $client->total_du > 0
                    ? round(($client->total_paye / $client->total_du) * 100)
                    : 0;
                return $client;
            });

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Créer un client depuis l'admin
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'acheteur',
        ]);

        return back()->with('success', 'Client ajouté avec succès.');
    }

    /**
     * Mettre à jour un client
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user->update($data);

        return back()->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Supprimer un client
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Libérer les parcelles liées avant suppression
        Parcelle::where('user_id', $id)->update(['user_id' => null, 'statut' => 'disponible']);
        $user->delete();

        return back()->with('success', 'Client supprimé avec succès.');
    }

    /**
     * Voir l'évolution détaillée d'un client spécifique
     */
    public function show($id)
    {
        $client = User::with(['parcelles', 'profil'])->findOrFail($id);

        $paiements = Paiement::with('parcelle')
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($client->parcelles as $parcelle) {
            $parcelle->somme_payee = Paiement::where('user_id', $id)
                ->where('parcelle_id', $parcelle->id)
                ->where('statut', 'valide')
                ->sum('montant_paye');
            $parcelle->pct = $parcelle->prix_total > 0
                ? round(($parcelle->somme_payee / $parcelle->prix_total) * 100)
                : 0;
        }

        return view('admin.clients.show', compact('client', 'paiements'));
    }
}
