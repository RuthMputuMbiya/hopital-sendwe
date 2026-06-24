<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'matricule';

        $credentials = [
            $field => $login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            if (!$user->est_actif) {
                Auth::logout();
                return back()->withErrors(['login' => 'Votre compte est désactivé.']);
            }
            
            $request->session()->regenerate();
            
            // Redirection directe selon le rôle
            return redirect()->intended($this->redirectTo($user));
        }

        return back()->withErrors(['login' => 'Identifiants incorrects.']);
    }

    protected function redirectTo($user): string
    {
        switch ($user->role_id) {
            case 1: return route('gestionnaire.dashboard');
            case 2: return route('medecin.dashboard');
            case 3: return route('receptionniste.dashboard');
            case 4: return route('laborantin.dashboard');
            default: return route('dashboard');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}