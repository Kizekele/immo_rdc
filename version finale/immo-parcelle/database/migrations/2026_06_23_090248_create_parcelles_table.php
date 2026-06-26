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
        Schema::create('parcelles', function (Blueprint $table) {
        $table->id();
        $table->string('titre');
        $table->string('localisation');
        $table->decimal('prix_total', 10, 2);
        $table->decimal('mensualite', 10, 2);
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // L'acheteur (peut être vide au début)
        $table->string('photo')->nullable(); // Chemin de la photo de la parcelle
        $table->string('statut')->default('disponible'); // 'disponible', 'en_cours', 'solde'
        $table->string('dimensions')->nullable(); // Nouvelle colonne pour la dimension
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelles');
    }
};
