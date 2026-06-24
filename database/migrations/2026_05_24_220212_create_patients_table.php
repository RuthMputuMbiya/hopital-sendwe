<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id('id_patient');
            $table->string('numero_fiche', 50)->unique();
            $table->string('nom', 100);
            $table->string('postnom', 100);
            $table->string('prenom', 100);
            $table->enum('sexe', ['M', 'F']);
            $table->date('date_naissance');
            $table->text('adresse');
            $table->string('telephone', 20);
            $table->text('antecedents_medicaux')->nullable();
            $table->text('allergies')->nullable();
            $table->string('groupe_sanguin', 5)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};