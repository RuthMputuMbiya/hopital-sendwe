<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validation des champs
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Déterminer si c'est un email ou un matricule
        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'matricule';

        $credentials = [
            $field => $login,
            'password' => $request->password
        ];

        // Tentative de connexion
        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
            
            // Vérifier si le compte est actif
            if (!$user->est_actif) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.',
                ])->onlyInput('login');
            }

            // Rediriger selon le rôle
            return redirect()->intended($this->redirectTo());
        }

        return back()->withErrors([
            'login' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('login');
    }

    protected function redirectTo()
    {
        $user = Auth::user();
        
        // Vérification du rôle et redirection
        if ($user->role_id == 1) {
            return route('gestionnaire.dashboard');
        } elseif ($user->role_id == 2) {
            return route('medecin.dashboard');
        } elseif ($user->role_id == 3) {
            return route('receptionniste.dashboard');
        } elseif ($user->role_id == 4) {
            return route('laborantin.dashboard');
        }
        
        return route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}