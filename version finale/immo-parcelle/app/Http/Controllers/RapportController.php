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
        // 1. Récupérer les données nécessaires
        $clients = User::where('role', 'client')->with('profil', 'parcelles')->get();

        // 2. Charger la vue HTML qui servira de modèle au PDF
        $pdf = Pdf::loadView('pdf.clients_rapport', compact('clients'));

        // 3. Prévisualiser dans le navigateur (stream)
        return $pdf->stream('rapport_clients.pdf');
    }

}
