<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class GestionnaireSystemeSeeder extends Seeder
{
    public function run(): void
    {
        // Vérifier que le rôle existe
        $roleAdmin = DB::table('roles')->where('id_role', 1)->first();
        
        if (!$roleAdmin) {
            echo "❌ Erreur: Le rôle 'Gestionnaire système' n'existe pas.\n";
            echo "   Exécutez d'abord RolesTableSeeder\n";
            return;
        }
        
        // Supprimer si existe déjà
        Schema::disableForeignKeyConstraints();
        DB::table('users')->where('matricule', 'ADM001')->delete();
        Schema::enableForeignKeyConstraints();
        
        // Créer le gestionnaire système
        DB::table('users')->insert([
            'id_user' => 1,
            'matricule' => 'ADM001',
            'nom' => 'CLEDIA',
            'postnom' => 'ADMIN',
            'prenom' => 'CLADIA',
            'sexe' => 'F',
            'email' => 'cledia@gmail.com',
            'telephone' => '+243999765977',
            'adresse' => 'Lubumbashi, RDC - Hôpital Sendwe',
            'password' => Hash::make('admin2026'),
            'role_id' => 1,
            'est_actif' => true,
            'date_embauche' => '2024-01-01',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "\n✅ Gestionnaire système créé avec succès !\n";
        echo "   👤 Nom: CLEDIA ADMIN\n";
        echo "   📧 Email: cledia@gmail.com\n";
        echo "   🔑 Mot de passe: admin2026\n";
        echo "   🆔 Matricule: ADM001\n";
    }
}