<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Afficher la page d'inscription
    public function showRegister()
    {
        return view('auth.register');
    }

    // Traiter l'inscription
    public function register(Request $request)
    {
        // 1. Validation des données
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'nom' => 'required|string|max:255',
            'postnom' => 'nullable|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'piece_identite' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        // 2. Stockage de la pièce d'identité
        $cheminFichier = '';
        if ($request->hasFile('piece_identite')) {
            $cheminFichier = $request->file('piece_identite')->store('pieces_identite', 'public');
        }

        // 3. Création de l'utilisateur avec la clé 'name' explicite
        $user = User::create([
            'name' => $request->prenom . ' ' . $request->nom, // Requis par SQL
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hachage sécurisé
            'role' => 'client', // Assignation du rôle client par défaut
        ]);

        // 4. Création du profil lié
        Profil::create([
            'user_id' => $user->id,
            'nom' => $request->nom,
            'postnom' => $request->postnom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'piece_identite' => $cheminFichier,
        ]);

        // 5. Connexion automatique
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Votre compte a été créé avec succès !');
    }

    // Afficher la page de connexion
    public function showLogin()
    {
        return view('auth.login');
    }

    // Traiter la connexion
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Rejoint la route pivot qui gère la redirection par rôle
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}