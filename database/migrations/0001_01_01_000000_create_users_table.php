<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // =============================================
            // IDENTIFICATION
            // =============================================
            $table->id('id_user');
            $table->string('matricule', 50)->unique();
            $table->string('nom', 100);
            $table->string('postnom', 100);
            $table->string('prenom', 100);
            $table->enum('sexe', ['M', 'F']);
            $table->string('email', 150)->unique();
            $table->string('telephone', 20);
            $table->text('adresse');
            $table->string('password');
            
            // =============================================
            // RÔLE
            // =============================================
            $table->foreignId('role_id')
                  ->constrained('roles', 'id_role')
                  ->onDelete('cascade');
            
            // =============================================
            // STATUT
            // =============================================
            $table->boolean('est_actif')->default(true);
            $table->boolean('first_login')->default(true); // ✅ AJOUTÉ
            $table->timestamp('password_changed_at')->nullable(); // ✅ AJOUTÉ
            
            // =============================================
            // INFORMATIONS PROFESSIONNELLES
            // =============================================
            $table->date('date_embauche')->nullable();
            $table->string('specialite')->nullable();
            
            // =============================================
            // TOKEN ET TIMESTAMPS
            // =============================================
            $table->rememberToken();
            $table->timestamps();
            
            // =============================================
            // INDEX
            // =============================================
            $table->index('email');
            $table->index('matricule');
            $table->index('role_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};