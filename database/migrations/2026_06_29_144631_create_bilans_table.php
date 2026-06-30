<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bilans', function (Blueprint $table) {
            $table->id('id_bilan');
            $table->unsignedBigInteger('consultation_id');
            $table->unsignedBigInteger('id_infirmier');
            
            // Constantes vitales
            $table->decimal('poids', 5, 2)->nullable();
            $table->decimal('taille', 5, 2)->nullable();
            $table->decimal('imc', 5, 2)->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->string('tension_arterielle', 20)->nullable();
            $table->integer('frequence_cardiaque')->nullable();
            $table->integer('frequence_respiratoire')->nullable();
            
          // Informations cliniques
            $table->text('motif')->nullable();
            $table->text('symptomes')->nullable();
            $table->text('observations')->nullable();
            $table->text('allergies')->nullable();
            $table->text('traitements_en_cours')->nullable();
            
            $table->boolean('est_transmis')->default(false);
            $table->timestamp('date_transmission')->nullable();
            
            $table->timestamps();
            
            $table->foreign('consultation_id')->references('id_consultation')->on('consultations')->onDelete('cascade');
            $table->foreign('id_infirmier')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bilans');
    }
};