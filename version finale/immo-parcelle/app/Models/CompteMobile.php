<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompteMobile extends Model
{
    //
    protected $table = 'comptes_mobiles';
    protected $fillable = [
        'operateur', 
        'numero', 
        'nom_compte'
    ];
}
