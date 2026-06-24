<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_user');           // Clé primaire
            $table->string('matricule', 50)->unique();
            $table->string('nom', 100);
            $table->string('postnom', 100);
            $table->string('prenom', 100);
            $table->enum('sexe', ['M', 'F']);
            $table->string('email', 150)->unique();
            $table->string('telephone', 20);
            $table->text('adresse');
            $table->string('password');
            
            // Clé étrangère vers roles
            $table->foreignId('role_id')
                  ->constrained('roles', 'id_role')  // référence vers id_role dans table roles
                  ->onDelete('cascade');
                  
            $table->boolean('est_actif')->default(true);
            $table->date('date_embauche')->nullable();
            $table->string('specialite')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};