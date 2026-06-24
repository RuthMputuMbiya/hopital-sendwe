<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_medical', function (Blueprint $table) {
            $table->id('id_historique');
            $table->string('action', 100);
            $table->text('description');
            $table->string('table_concernee', 50);
            $table->foreignId('id_patient')->constrained('patients', 'id_patient')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->text('anciennes_valeurs')->nullable();
            $table->text('nouvelles_valeurs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_medical');
    }
};