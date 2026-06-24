<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnifiedRedirect
{
    /**
     * Gère toutes les redirections selon le rôle de l'utilisateur
     * UN SEUL FICHIER POUR TOUS LES UTILISATEURS
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }
        
        $user = Auth::user();
        
        // Vérifier si le compte est actif
        if (!$user->est_actif) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé.');
        }
        
        // Définir la route de redirection selon le rôle
        $redirectRoute = $this->getRedirectRoute($user);
        
        // Si la requête actuelle est une page d'accueil ou login, rediriger vers le dashboard
        if ($request->routeIs('home') || $request->routeIs('login') || $request->routeIs('/')) {
            return redirect()->route($redirectRoute);
        }
        
        return $next($request);
    }
    
    /**
     * Obtenir la route de redirection selon le rôle
     */
    private function getRedirectRoute($user)
    {
        if ($user->estGestionnaire()) {
            return 'gestionnaire.dashboard';
        } elseif ($user->estMedecin()) {
            return 'medecin.dashboard';
        } elseif ($user->estReceptionniste()) {
            return 'receptionniste.dashboard';
        } elseif ($user->estLaborantin()) {
            return 'laborantin.dashboard';
        }
        
        return 'dashboard';
    }
    
    /**
     * Obtenir l'URL de redirection selon le rôle
     */
    public static function getRedirectUrl($user)
    {
        if ($user->estGestionnaire()) {
            return route('gestionnaire.dashboard');
        } elseif ($user->estMedecin()) {
            return route('medecin.dashboard');
        } elseif ($user->estReceptionniste()) {
            return route('receptionniste.dashboard');
        } elseif ($user->estLaborantin()) {
            return route('laborantin.dashboard');
        }
        
        return route('dashboard');
    }
}