<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Profil;
use App\Models\CompteMobile;
use App\Models\Parcelle;
use App\Models\Paiement;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@immo.com',
            'password' => Hash::make('password'),
            'role' => 'administrateur',
        ]);
        
        Profil::create([
            'user_id' => $admin->id,
            'nom' => 'Kasongo',
            'postnom' => 'Mukendi',
            'prenom' => 'Alain',
            'telephone' => '+243810000000',
            'piece_identite' => 'identites/admin.jpg'
        ]);

        // 2. Création de l'Acheteur de test (Jean Mwamba)
        $client = User::create([
            'name' => 'Jean Mwamba',
            'email' => 'jean@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);
        
        Profil::create([
            'user_id' => $client->id,
            'nom' => 'Mwamba',
            'postnom' => 'Mulumba',
            'prenom' => 'Jean',
            'telephone' => '+243899999999',
            'piece_identite' => 'identites/jean_carte.jpg'
        ]);

        // 3. Insertion des comptes de réception Mobile Money
        CompteMobile::create(['operateur' => 'Orange Money', 'numero' => '+243891234567', 'nom_compte' => 'IMMO RDC SARL']);
        CompteMobile::create(['operateur' => 'M-Pesa', 'numero' => '+243811234567', 'nom_compte' => 'IMMO RDC SARL']);
        CompteMobile::create(['operateur' => 'Airtel Money', 'numero' => '+243991234567', 'nom_compte' => 'IMMO RDC SARL']);
        CompteMobile::create(['operateur' => 'Virement bancaire', 'numero' => '1234567890', 'nom_compte' => 'IMMO RDC SARL']);

        // 4. Création des parcelles
        $parcelleJean = Parcelle::create([
            'titre' => 'Concession Espoir - Parcelle #42',
            'localisation' => 'Kinshasa, Maluku',
            'prix_total' => 5000,
            'mensualite' => 100,
            'user_id' => $client->id, // Assignée à Jean
            'statut' => 'en_cours',
            'dimensions' => '20m x 30m',
        ]);

        Parcelle::create([
            'titre' => 'Concession Espoir - Parcelle #43',
            'localisation' => 'Kinshasa, Maluku',
            'prix_total' => 5000,
            'mensualite' => 100,
            'statut' => 'disponible', // Libre à l'achat
            'dimensions' => '20m x 30m',
        ]);

        // 5. Simulation du paiement validé de Janvier pour Jean
        Paiement::create([
            'parcelle_id' => $parcelleJean->id,
            'user_id' => $client->id,
            'mois_concerne' => 'Janvier',
            'montant_paye' => 100,
            'preuve_photo' => 'captures/janvier.jpg',
            'statut' => 'valide',
        ]);

        
    }
}
