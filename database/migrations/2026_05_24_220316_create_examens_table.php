<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examens', function (Blueprint $table) {
            $table->id('id_examen');
            $table->string('numero_examen', 50)->unique();
            $table->string('type_examen', 100);
            $table->text('resultat')->nullable();
            $table->date('date_examen');
            $table->time('heure_examen');
            $table->string('fichier_resultat')->nullable();
            $table->foreignId('id_patient')->constrained('patients', 'id_patient')->onDelete('cascade');
            $table->foreignId('id_maladie')->nullable()->constrained('maladies', 'id_maladie')->onDelete('set null');
            $table->foreignId('laborantin_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->enum('statut', ['en_attente', 'termine', 'annule'])->default('en_attente');
            $table->text('commentaires')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examens');
    }
};