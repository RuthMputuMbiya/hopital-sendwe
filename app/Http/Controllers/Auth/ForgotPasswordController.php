<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Aucun utilisateur trouvé avec cet email.']);
        }

        // Générer un token
        $token = Str::random(60);
        
        // Supprimer les anciens tokens
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        
        // Enregistrer le nouveau token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // Envoyer l'email (simulé pour le moment)
        // Mail::send('auth.emails.reset-password', ['token' => $token, 'user' => $user], ...);

        return back()->with('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
    }
}