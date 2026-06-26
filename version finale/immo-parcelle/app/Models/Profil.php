<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $fillable = [
        'user_id',
        'nom',
        'postnom',
        'prenom',
        'telephone',
        'piece_identite'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
