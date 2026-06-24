<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Parcelle;


class GestionClientController extends Controller
{
    //
    // Afficher la liste des clients avec leurs parcelles
    public function index()
    {
        $clients = User::with('parcelles')->where('role', 'acheteur')->get();
        return view('admin.clients.index', compact('clients'));
    }

    // Ajouter un client (formulaire)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        
        $data['password'] = bcrypt($data['password']);
        $data['role'] = 'acheteur';
        
        User::create($data);
        return back()->with('success', 'Client ajouté avec succès');
    }

    // Mise à jour
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email']));
        return back()->with('success', 'Informations mises à jour');
    }

    // Suppression
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Client supprimé');
    }
}
