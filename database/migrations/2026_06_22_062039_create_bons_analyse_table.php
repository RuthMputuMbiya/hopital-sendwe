<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bons_analyse', function (Blueprint $table) {
            $table->id('id_bon');
            $table->string('numero_bon', 50)->unique();
            
            // Patient
            $table->foreignId('id_patient')->constrained('patients', 'id_patient')->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('laborantin_id')->nullable()->constrained('users', 'id_user')->onDelete('set null');
            
            // Infos patient
            $table->boolean('hospitalise')->default(false);
            $table->string('pavillon')->nullable();
            $table->string('chambre')->nullable();
            $table->string('lit')->nullable();
            $table->text('adresse_domicile')->nullable();
            
            // Infos médecin demandeur
            $table->string('nom_medecin_demandeur', 100);
            $table->string('prenom_medecin_demandeur', 100);
            $table->date('date_demande');
            $table->time('heure_demande');
            $table->string('service_demandeur', 100);
            $table->string('telephone_demandeur', 20);
            
            // Préleveur
            $table->string('nom_preleveur', 100)->nullable();
            $table->string('prenom_preleveur', 100)->nullable();
            $table->date('date_prelevement')->nullable();
            $table->time('heure_prelevement')->nullable();
            $table->string('id_client_labo', 50)->nullable();
            $table->string('telephone_preleveur', 20)->nullable();
            
            // Types d'échantillon
            $table->enum('type_echantillon', ['sang', 'selles', 'urines', 'lcr', 'crachat', 'autres'])->nullable();
            $table->string('autres_echantillon', 100)->nullable();
            
            // Examens (JSON)
            $table->json('examens')->nullable();
            
            // Priorité
            $table->enum('priorite', ['normal', 'urgent'])->default('normal');
            
            // Statut
            $table->enum('statut', ['en_attente', 'en_cours', 'termine'])->default('en_attente');
            
            // Observations et résultats
            $table->text('observations_laborantin')->nullable();
            $table->json('resultats')->nullable();
            $table->date('date_retour')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bons_analyse');
    }
};