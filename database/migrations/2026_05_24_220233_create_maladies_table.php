<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maladies', function (Blueprint $table) {
            $table->id('id_maladie');
            $table->string('code_maladie', 50)->unique();
            $table->string('nom_maladie', 150);
            $table->text('description');
            $table->string('categorie', 100)->nullable();
            $table->string('symptomes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maladies');
    }
};