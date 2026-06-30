<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();
        
        $roles = [
            [
                'id_role' => 1,
                'nom_role' => 'Gestionnaire système',
                'description' => 'Administrateur principal - Gère tous les utilisateurs et le système',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_role' => 2,
                'nom_role' => 'Médecin',
                'description' => 'Consultations, prescriptions, ajout de maladies',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_role' => 3,
                'nom_role' => 'Réceptionniste',
                'description' => 'Enregistrement des patients',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_role' => 4,
                'nom_role' => 'Laborantin',
                'description' => 'Examens et analyses médicales',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // ✅ AJOUT DE L'INFIRMIER
            [
                'id_role' => 5,
                'nom_role' => 'Infirmier',
                'description' => 'Bilan initial, constantes vitales, soins infirmiers',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        
        DB::table('roles')->insert($roles);
        
        echo "\n✅ 5 rôles créés avec succès !\n";
        echo "   - Gestionnaire système (ID: 1)\n";
        echo "   - Médecin (ID: 2)\n";
        echo "   - Réceptionniste (ID: 3)\n";
        echo "   - Laborantin (ID: 4)\n";
        echo "   - Infirmier (ID: 5) 🩺\n";
    }
}