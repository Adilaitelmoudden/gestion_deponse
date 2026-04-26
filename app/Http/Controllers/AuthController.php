<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Afficher formulaire login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Afficher formulaire register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Traiter le login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email non trouvé.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Mot de passe incorrect.');
        }

        if (!$user->is_active) {
            return back()->with('error', 'Votre compte est désactivé. Contactez l\'administrateur.');
        }

        // Connexion réussie
        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);

        // Mettre à jour dernière connexion
        $user->update(['last_login_at' => now()]);

        if ($user->role === 'admin') {
            return redirect('/admin/users')->with('success', 'Bienvenue Admin ' . $user->name . ' !');
        }

        return redirect()->route('dashboard')->with('success', 'Bienvenue ' . $user->name . ' !');
    }

    // Traiter le register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => true
        ]);

        // Connexion automatique après inscription
        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role
        ]);

        return redirect()->route('dashboard')->with('success', 'Compte créé avec succès ! Bienvenue ' . $user->name);
    }

    // Déconnexion
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Déconnecté avec succès.');
    }

    // Profil utilisateur
    public function profile()
    {
        $user = User::find(session('user_id'));
        if (!$user) {
            return redirect()->route('login')->with('error', 'Session expirée.');
        }
        return view('auth.profile', compact('user'));
    }

    // Mettre à jour profil
    public function updateProfile(Request $request)
    {
        $user = User::find(session('user_id'));

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Mettre à jour session
        session([
            'user_name' => $user->name,
            'user_email' => $user->email
        ]);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}