<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Parcelle;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class RapportController extends Controller
{
    //
    public function imprimerClientsParcelles()
    {
        // Récupère les clients avec leurs parcelles associées
        $clients = User::where('role', 'client')->with('parcelles')->get();

        // Charge la vue et lui passe les données
        $pdf = Pdf::loadView('pdf.clients_parcelles', compact('clients'));

        // Télécharge le PDF
        return $pdf->download('liste_clients_parcelles.pdf');
    }
}
