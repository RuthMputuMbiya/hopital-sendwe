<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'matricule';

        // ✅ Vérifier si l'utilisateur existe
        $user = User::where($field, $login)->first();
        if (!$user) {
            return back()->withErrors(['login' => 'Identifiants incorrects.']);
        }

        // ✅ Vérifier le mot de passe
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Identifiants incorrects.']);
        }

        $credentials = [
            $field => $login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // ✅ Vérifier si le compte est actif
            if (!$user->est_actif) {
                Auth::logout();
                return back()->withErrors(['login' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.']);
            }

            $request->session()->regenerate();

            // ✅ Vérifier si c'est la première connexion
            if ($user->first_login) {
                return redirect()->route('password.change')
                    ->with('warning', '⚠️ Veuillez changer votre mot de passe avant de continuer.');
            }

            // ✅ Redirection selon le rôle
            return redirect()->intended($this->redirectTo($user));
        }

        return back()->withErrors(['login' => 'Identifiants incorrects.']);
    }

    /**
     * Redirection après connexion selon le rôle
     */
    protected function redirectTo($user): string
    {
        switch ($user->role_id) {
            case 1:
                return route('gestionnaire.dashboard');
            case 2:
                return route('medecin.dashboard');
            case 3:
                return route('receptionniste.dashboard');
            case 4:
                return route('laborantin.dashboard');
            case 5:  // ✅ INFIRMIER
                return route('infirmier.dashboard');
            default:
                return route('dashboard');
        }
    }

    /**
     * Déconnexion
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}