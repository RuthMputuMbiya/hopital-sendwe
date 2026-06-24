<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  IDs des rôles autorisés (1,2,3,4)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        $user = Auth::user();
        
        // Convertir les paramètres en entiers
        $allowedRoles = array_map('intval', $roles);
        
        // Vérifier si le rôle de l'utilisateur est autorisé
        if (in_array($user->role_id, $allowedRoles)) {
            return $next($request);
        }
        
        // Redirection selon le rôle si accès non autorisé
        switch ($user->role_id) {
            case 1: // Gestionnaire
                return redirect()->route('gestionnaire.dashboard')
                    ->with('error', 'Accès non autorisé à cette page.');
            case 2: // Médecin
                return redirect()->route('medecin.dashboard')
                    ->with('error', 'Accès non autorisé à cette page.');
            case 3: // Réceptionniste
                return redirect()->route('receptionniste.dashboard')
                    ->with('error', 'Accès non autorisé à cette page.');
            case 4: // Laborantin
                return redirect()->route('laborantin.dashboard')
                    ->with('error', 'Accès non autorisé à cette page.');
            default:
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Rôle non reconnu. Veuillez contacter l\'administrateur.');
        }
    }
}