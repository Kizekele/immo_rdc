<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    //
    protected $fillable = [
        'parcelle_id',
        'user_id',
        'mois_concerne',
        'montant_paye',
        'penalite_appliquee',
        'preuve_photo',
        'preuvePhoto_admin',
        'statut'
    ];
    public function parcelle()
    {
        return $this->belongsTo(Parcelle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
