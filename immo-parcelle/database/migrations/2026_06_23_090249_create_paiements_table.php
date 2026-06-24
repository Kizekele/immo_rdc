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
        Schema::create('paiements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('parcelle_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a payé
        $table->string('mois_concerne'); // Ex: 'Janvier', 'Février'
        $table->decimal('montant_paye', 10, 2);
        $table->decimal('penalite_appliquee', 10, 2)->default(0.00); // 10% si en retard
        $table->string('preuve_photo'); // Capture d'écran mobile money
        $table->string('statut')->default('en_attente'); // 'en_attente', 'valide', 'rejete'
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
