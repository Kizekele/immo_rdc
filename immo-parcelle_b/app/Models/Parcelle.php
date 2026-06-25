<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parcelle extends Model
{
    //
    protected $fillable = [
        'titre',
        'localisation',
        'prix_total',
        'mensualite',
        'dimensions',
        'user_id',
        'statut',
        'photo'
    ];
    public function acheteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
