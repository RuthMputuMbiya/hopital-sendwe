<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id('id_consultation');
            $table->string('numero_consultation', 50)->unique();
            $table->date('date_consultation');
            $table->time('heure_consultation');
            $table->text('motif');
            $table->text('symptomes');
            $table->text('diagnostic');
            $table->text('prescription')->nullable();
            $table->text('observations_medecin')->nullable();
            $table->string('tension_arterielle', 20)->nullable();
            $table->decimal('poids', 5, 2)->nullable();
            $table->decimal('taille', 5, 2)->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('frequence_cardiaque')->nullable();
            $table->foreignId('id_patient')->constrained('patients', 'id_patient')->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_maladie')->nullable()->constrained('maladies', 'id_maladie')->onDelete('set null');
            $table->enum('statut', ['en_cours', 'termine', 'annule'])->default('en_cours');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};