<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('id_notification');
            $table->string('titre', 200);
            $table->text('message');
            $table->string('type', 50)->default('info'); // info, success, warning, danger
            $table->boolean('est_lue')->default(false);
            $table->timestamp('date_lue')->nullable();
            $table->foreignId('user_id')->constrained('users', 'id_user')->onDelete('cascade');
            $table->string('lien')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};