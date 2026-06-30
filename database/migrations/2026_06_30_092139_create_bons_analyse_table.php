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
            
            // ============================================================
            // RELATIONS - SANS CONTRAINTES POUR ÉVITER LES ERREURS
            // ============================================================
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_consultation')->nullable();
            $table->unsignedBigInteger('medecin_id');
            $table->unsignedBigInteger('id_infirmier')->nullable();
            $table->unsignedBigInteger('bilan_infirmier_id')->nullable();
            $table->unsignedBigInteger('laborantin_id')->nullable();
            
            // ============================================================
            // INFORMATIONS PATIENT
            // ============================================================
            $table->boolean('hospitalise')->default(false);
            $table->string('pavillon', 50)->nullable();
            $table->string('chambre', 50)->nullable();
            $table->string('lit', 50)->nullable();
            $table->text('adresse_domicile')->nullable();
            
            // ============================================================
            // MÉDECIN DEMANDEUR
            // ============================================================
            $table->string('nom_medecin_demandeur', 100);
            $table->string('prenom_medecin_demandeur', 100);
            $table->date('date_demande');
            $table->time('heure_demande');
            $table->string('service_demandeur', 100);
            $table->string('telephone_demandeur', 20);
            
            // ============================================================
            // PRÉLEVEUR
            // ============================================================
            $table->string('nom_preleveur', 100)->nullable();
            $table->string('prenom_preleveur', 100)->nullable();
            $table->date('date_prelevement')->nullable();
            $table->time('heure_prelevement')->nullable();
            $table->string('id_client_labo', 50)->nullable();
            $table->string('telephone_preleveur', 20)->nullable();
            
            // ============================================================
            // TYPE D'ÉCHANTILLON
            // ============================================================
            $table->enum('type_echantillon', ['sang', 'selles', 'urines', 'lcr', 'crachat', 'autres'])->nullable();
            $table->string('autres_echantillon', 100)->nullable();
            
            // ============================================================
            // EXAMENS (JSON)
            // ============================================================
            $table->json('examens')->nullable();
            
            // ============================================================
            // PRIORITÉ
            // ============================================================
            $table->enum('priorite', ['normal', 'urgent'])->default('normal');
            
            // ============================================================
            // STATUT
            // ============================================================
            $table->enum('statut', ['en_attente', 'preleve', 'analyse', 'termine', 'retourne'])->default('en_attente');
            
            // ============================================================
            // OBSERVATIONS
            // ============================================================
            $table->text('observations_laborantin')->nullable();
            $table->text('observations_cliniques')->nullable();
            
            // ============================================================
            // RÉSULTATS
            // ============================================================
            $table->json('resultats')->nullable();
            $table->timestamp('date_retour')->nullable();
            
            // ============================================================
            // BILAN INFIRMIER
            // ============================================================
            $table->text('motif_infirmier')->nullable();
            $table->text('symptomes_infirmier')->nullable();
            $table->text('observations_infirmier')->nullable();
            $table->text('allergies_infirmier')->nullable();
            $table->text('traitements_infirmier')->nullable();
            
            // Constantes vitales de l'infirmier
            $table->decimal('poids_infirmier', 5, 2)->nullable();
            $table->decimal('taille_infirmier', 5, 2)->nullable();
            $table->decimal('imc_infirmier', 5, 2)->nullable();
            $table->decimal('temperature_infirmier', 4, 1)->nullable();
            $table->string('tension_arterielle_infirmier', 20)->nullable();
            $table->integer('frequence_cardiaque_infirmier')->nullable();
            $table->integer('frequence_respiratoire_infirmier')->nullable();
            $table->integer('spo2_infirmier')->nullable();
            
            // ============================================================
            // TIMESTAMPS
            // ============================================================
            $table->timestamps();
            
            // ============================================================
            // INDEX
            // ============================================================
            $table->index('numero_bon');
            $table->index('statut');
            $table->index('priorite');
            $table->index('created_at');
            $table->index('date_demande');
            $table->index('id_patient');
            $table->index('medecin_id');
            $table->index('id_infirmier');
            $table->index('laborantin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bons_analyse');
    }
};