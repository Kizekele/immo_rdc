<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comptes_mobiles', function (Blueprint $table) {
        $table->id();
        $table->string('operateur'); // Orange Money, M-Pesa, Airtel Money
        $table->string('numero');
        $table->string('nom_compte'); // Le nom qui s'affiche (ex: IMMO RDC)
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comptes_mobiles');
    }
};
