<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Seul le nécessaire pour démarrer l'application.
     */
    public function run(): void
    {
        $this->call([
            // ========== 1. RÔLES ==========
            RolesTableSeeder::class,              // Créer les 4 rôles (Gestionnaire, Médecin, Réceptionniste, Laborantin)
            
            // ========== 2. GESTIONNAIRE SYSTÈME ==========
            GestionnaireSystemeSeeder::class,     // SEUL le compte admin est créé ici
            
            // ========== PAS DE MALADIES ==========
            // Les maladies seront ajoutées par les Médecins ou Gestionnaire via l'interface
            
            // ========== PAS DE PATIENTS ==========
            // Les patients seront enregistrés par les Réceptionnistes via l'interface
            
            // ========== PAS D'AUTRES UTILISATEURS ==========
            // Médecins, Réceptionnistes, Laborantins s'inscrivent eux-mêmes
        ]);
    }
}