<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ParcelleController;
use App\Http\Controllers\DashboardAcheteurController;
use App\Http\Controllers\GestionClientController;
use App\Http\Controllers\RapportController;


/*
|--------------------------------------------------------------------------
| 1. Routes Publiques
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('parcelle-show', [
        'parcelles' => \App\Models\Parcelle::all(),
        'parcellesLibresCount' => \App\Models\Parcelle::where('statut', 'disponible')->count()
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| 2. Routes Invités (guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| 3. Routes Sécurisées (auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Pivot : redirige vers l'espace approprié selon le rôle
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'administrateur') {
            return redirect()->route('dashboard-admin');
        }
        return redirect()->route('dashboard-acheteur');
    })->name('dashboard');

    //---------------------------------------------------------
    // Espace Acheteur
    //---------------------------------------------------------
    Route::get('/dashboard/acheteur', [DashboardAcheteurController::class, 'index'])->name('dashboard-acheteur');
    Route::post('/dashboard/acheteur', [DashboardAcheteurController::class, 'store'])->name('dashboard-acheteur.store');
    Route::post('/parcelle/souscrire/{id}', [ParcelleController::class, 'souscrire'])->name('parcelle.souscrire');

    //---------------------------------------------------------
    // Espace Administration
    //---------------------------------------------------------
    Route::prefix('admin')->group(function () {

        // Dashboard principal (supporte ?mois=Janvier pour le filtre)
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard-admin');

        // Traitement paiements — maintenant avec upload photo obligatoire à l'approbation
        Route::post('/paiements/{id}/{action}', [AdminDashboardController::class, 'traitementAction'])
            ->where('action', 'approuver|refuser')
            ->name('admin.paiements.action');

        // Ajout parcelle (bug dimensions corrigé)
        Route::post('/parcelles', [AdminDashboardController::class, 'storeParcelle'])->name('admin.parcelles.store');

        Route::get('/parcelles-suivi', [ParcelleController::class, 'adminIndex'])->name('admin.parcelles.suivi');

        // Gestion clients CRUD — avec route show pour détail client
        Route::resource('clients', GestionClientController::class);

        //confirmation paiement
        Route::post('/admin/paiements/{id}/approuver', [AdminDashboardController::class, 'validerApprobation'])
        ->name('admin.paiements.approuver');


        //pdf
        Route::get('/admin/pdf/clients', [RapportController::class, 'imprimerClientsParcelles'])->name('pdf.clients');
        Route::get('/admin/export/{type}', [AdminDashboardController::class, 'exportPdf'])
            ->name('admin.export.pdf');

    });
});
