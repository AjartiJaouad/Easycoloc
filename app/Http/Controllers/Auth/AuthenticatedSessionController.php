<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Afficher la vue de connexion.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Gérer une demande d'authentification entrante.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Tentative d'authentification (vérification email et mot de passe)
        $request->authenticate();

        // 2. Régénérer la session pour éviter les attaques de fixation de session
        $request->session()->regenerate();

        // --- 🛡️ PROTECTION : Bloquer les utilisateurs bannis ---
        // On vérifie si le champ 'banned_at' n'est pas vide dans la base de données
        if (Auth::user()->banned_at) {

            // Déconnexion immédiate de l'utilisateur
            Auth::guard('web')->logout();

            // Invalidation de la session actuelle et régénération du jeton CSRF
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirection vers la page de login avec un message d'erreur explicite
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été banni par l\'administrateur. Accès refusé.',
            ]);
        }
        // -----------------------------------------------------

        // 3. Redirection vers le Dashboard si l'utilisateur n'est pas banni
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Détruire une session authentifiée (Déconnexion).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
