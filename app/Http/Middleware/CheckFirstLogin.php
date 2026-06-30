<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFirstLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (auth()->check()) {
            $user = auth()->user();
            
            // Vérifier si la méthode needsPasswordChange existe
            if (method_exists($user, 'needsPasswordChange') && $user->needsPasswordChange()) {
                
                // ✅ EXCLURE CES ROUTES POUR ÉVITER LA BOUCLE
                $excludedRoutes = [
                    'password.change',
                    'password.update',
                    'login',
                    'logout',
                    'register',
                    'password.request',
                    'password.email',
                    'password.reset',
                    'welcome'
                ];
                
                // Si déjà sur une route exclue, on laisse passer
                if ($request->route() && in_array($request->route()->getName(), $excludedRoutes)) {
                    return $next($request);
                }
                
                // Sinon, rediriger vers la page de changement
                return redirect()->route('password.change')
                    ->with('warning', '⚠️ Veuillez changer votre mot de passe avant de continuer.');
            }
        }
        
        return $next($request);
    }
}