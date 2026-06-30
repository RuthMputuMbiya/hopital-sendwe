<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            // =============================================
            // CLÉ PRIMAIRE
            // =============================================
            $table->id('id_consultation');
            
            // =============================================
            // NUMÉRO DE CONSULTATION
            // =============================================
            $table->string('numero_consultation', 50)->unique();
            
            // =============================================
            // DATE ET HEURE
            // =============================================
            $table->date('date_consultation');
            $table->time('heure_consultation')->nullable();
            
            // =============================================
            // INFORMATIONS MÉDICALES
            // =============================================
            $table->text('motif')->nullable();
            $table->text('symptomes')->nullable();
            $table->text('diagnostic')->nullable();
            $table->text('prescription')->nullable();
            $table->text('observations_medecin')->nullable();
            
            // =============================================
            // CONSTANTES VITALES (Infirmier)
            // =============================================
            $table->string('tension_arterielle', 20)->nullable();
            $table->decimal('poids', 5, 2)->nullable();
            $table->decimal('taille', 5, 2)->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('frequence_cardiaque')->nullable();
            $table->integer('frequence_respiratoire')->nullable();
            $table->integer('spo2')->nullable();
            $table->decimal('imc', 5, 2)->nullable();
            
            // =============================================
            // HOSPITALISATION
            // =============================================
            $table->boolean('hospitalise')->default(false);
            $table->string('pavillon', 50)->nullable();
            $table->string('chambre', 50)->nullable();
            $table->string('lit', 50)->nullable();
            $table->text('adresse_domicile')->nullable();
            $table->string('moyen', 50)->nullable();
            $table->enum('priorite', ['normal', 'urgent', 'moyen'])->default('normal');
            $table->text('observations_cliniques')->nullable();
            
            // =============================================
            // RELATIONS (Clés étrangères)
            // =============================================
            $table->foreignId('id_patient')->constrained('patients', 'id_patient')->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_infirmier')->nullable()->constrained('users', 'id_user')->onDelete('set null');
            $table->foreignId('id_maladie')->nullable()->constrained('maladies', 'id_maladie')->onDelete('set null');
            
            // =============================================
            // STATUTS
            // =============================================
            // Ancien statut (pour compatibilité)
            $table->enum('statut', ['en_cours', 'termine', 'annule'])->default('en_cours');
            
            // Nouveau statut de consultation
            $table->enum('statut_consultation', [
                'en_attente_infirmier',   // Patient en salle d'attente
                'bilan_fait',             // Bilan infirmier terminé
                'en_consultation',        // En cours de consultation chez le médecin
                'termine',                // Consultation terminée
                'annule'                  // Consultation annulée
            ])->default('en_attente_infirmier');
            
            // =============================================
            // TIMESTAMPS
            // =============================================
            $table->timestamps();
            
            // =============================================
            // INDEX (Pour optimiser les recherches)
            // =============================================
            $table->index('numero_consultation');
            $table->index('statut_consultation');
            $table->index('date_consultation');
            $table->index('id_patient');
            $table->index('medecin_id');
            $table->index('id_infirmier');
            $table->index('id_maladie');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};