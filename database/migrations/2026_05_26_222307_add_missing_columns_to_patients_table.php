<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Ajouter les colonnes seulement si elles n'existent pas
            if (!Schema::hasColumn('patients', 'lieu_naissance')) {
                $table->string('lieu_naissance', 100)->nullable()->after('date_naissance');
            }
            if (!Schema::hasColumn('patients', 'nationalite')) {
                $table->string('nationalite', 50)->nullable()->after('lieu_naissance');
            }
            if (!Schema::hasColumn('patients', 'profession')) {
                $table->string('profession', 100)->nullable()->after('nationalite');
            }
            if (!Schema::hasColumn('patients', 'situation_matrimoniale')) {
                $table->string('situation_matrimoniale', 50)->nullable()->after('profession');
            }
            if (!Schema::hasColumn('patients', 'telephone_secondaire')) {
                $table->string('telephone_secondaire', 20)->nullable()->after('telephone');
            }
            if (!Schema::hasColumn('patients', 'email')) {
                $table->string('email', 150)->nullable()->after('telephone_secondaire');
            }
            if (!Schema::hasColumn('patients', 'ville')) {
                $table->string('ville', 100)->nullable()->after('adresse');
            }
            if (!Schema::hasColumn('patients', 'quartier')) {
                $table->string('quartier', 100)->nullable()->after('ville');
            }
            if (!Schema::hasColumn('patients', 'antecedents_chirurgicaux')) {
                $table->text('antecedents_chirurgicaux')->nullable()->after('antecedents_medicaux');
            }
            if (!Schema::hasColumn('patients', 'antecedents_familiaux')) {
                $table->text('antecedents_familiaux')->nullable()->after('antecedents_chirurgicaux');
            }
            if (!Schema::hasColumn('patients', 'traitements_en_cours')) {
                $table->text('traitements_en_cours')->nullable()->after('allergies');
            }
            if (!Schema::hasColumn('patients', 'vaccinations')) {
                $table->text('vaccinations')->nullable()->after('traitements_en_cours');
            }
            if (!Schema::hasColumn('patients', 'handicap')) {
                $table->string('handicap', 255)->nullable()->after('vaccinations');
            }
            if (!Schema::hasColumn('patients', 'contact_urgence_nom')) {
                $table->string('contact_urgence_nom', 100)->nullable()->after('handicap');
            }
            if (!Schema::hasColumn('patients', 'contact_urgence_telephone')) {
                $table->string('contact_urgence_telephone', 20)->nullable()->after('contact_urgence_nom');
            }
            if (!Schema::hasColumn('patients', 'contact_urgence_relation')) {
                $table->string('contact_urgence_relation', 50)->nullable()->after('contact_urgence_telephone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'lieu_naissance', 'nationalite', 'profession', 'situation_matrimoniale',
                'telephone_secondaire', 'email', 'ville', 'quartier',
                'antecedents_chirurgicaux', 'antecedents_familiaux', 'traitements_en_cours',
                'vaccinations', 'handicap', 'contact_urgence_nom', 
                'contact_urgence_telephone', 'contact_urgence_relation'
            ]);
        });
    }
};