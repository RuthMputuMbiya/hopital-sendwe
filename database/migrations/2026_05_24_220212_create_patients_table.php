<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            // =============================================
            // IDENTIFICATION
            // =============================================
            $table->id('id_patient');
            $table->string('numero_fiche', 50)->unique();
            
            // =============================================
            // IDENTITÉ
            // =============================================
            $table->string('nom', 100);
            $table->string('postnom', 100);
            $table->string('prenom', 100);
            $table->enum('sexe', ['M', 'F']);
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance', 100)->nullable();
            $table->string('nationalite', 50)->nullable();
            $table->string('profession', 100)->nullable();
            $table->string('situation_matrimoniale', 50)->nullable();
            
            // =============================================
            // CONTACT
            // =============================================
            $table->string('telephone', 20)->nullable();
            $table->string('telephone_secondaire', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('quartier', 100)->nullable();
            
            // =============================================
            // ANTÉCÉDENTS
            // =============================================
            $table->text('antecedents_medicaux')->nullable();
            $table->text('antecedents_chirurgicaux')->nullable();
            $table->text('antecedents_familiaux')->nullable();
            
            // =============================================
            // ALLERGIES ET TRAITEMENTS
            // =============================================
            $table->text('allergies')->nullable();
            $table->text('traitements_en_cours')->nullable();
            $table->text('vaccinations')->nullable();
            $table->string('groupe_sanguin', 5)->nullable();
            $table->string('handicap', 255)->nullable();
            
            // =============================================
            // CONTACT URGENCE
            // =============================================
            $table->string('contact_urgence_nom', 100)->nullable();
            $table->string('contact_urgence_telephone', 20)->nullable();
            $table->string('contact_urgence_relation', 50)->nullable();
            
            // =============================================
            // TIMESTAMPS
            // =============================================
            $table->timestamps();
            
            // =============================================
            // INDEX
            // =============================================
            $table->index('numero_fiche');
            $table->index('nom');
            $table->index('prenom');
            $table->index('telephone');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};