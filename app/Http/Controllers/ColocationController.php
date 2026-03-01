<?php

namespace App\Http\Controllers;

use App\Mail\ColocationInvitation;
use App\Models\Colocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ColocationController extends Controller
{
    /**
     * Affiche la liste des colocations de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        $colocations = $user->colocations()->get();

        return view('colocations.index', compact('colocations'));
    }

    /**
     * Affiche le formulaire de création de colocation.
     */
    public function create()
    {
        if (Auth::user()->colocations()->where('status', 'active')->exists()) {
            return redirect()->route('colocations.index')->with('error', 'Vous avez déjà une colocation active.');
        }

        return view('colocations.create');
    }

    /**
     * Enregistre une nouvelle colocation.
     */
    public function store(Request $request)
    {
        if (Auth::user()->colocations()->where('status', 'active')->exists()) {
            return redirect()->route('colocations.index')->with('error', 'Action impossible : vous avez déjà une colocation active.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $colocation = Colocation::create([
            'name' => $request->name,
            'invitation_token' => Str::random(10),
            'status' => 'active',
        ]);

        // Attacher le créateur comme propriétaire (Owner)
        $colocation->users()->attach(Auth::id(), [
            'role' => 'owner',
        ]);

        return redirect()->route('colocations.index')->with('success', 'Colocation créée avec succès !');
    }

    /**
     * Annule une colocation (uniquement pour le propriétaire).
     */
    public function cancel(Colocation $colocation)
    {
        $isOwner = $colocation->users()
            ->where('user_id', Auth::id())
            ->where('colocation_user.role', 'owner')
            ->exists();

        if (! $isOwner) {
            return redirect()->route('colocations.index')->with('error', 'Action refusée. Seul le propriétaire peut annuler la colocation.');
        }

        $colocation->update(['status' => 'cancelled']);

        return redirect()->route('colocations.index')->with('success', 'La colocation a été annulée avec succès.');
    }

    /**
     * Envoie une invitation par email.
     */
    public function sendInvitation(Request $request, Colocation $colocation)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $isOwner = $colocation->users()
            ->where('user_id', Auth::id())
            ->where('colocation_user.role', 'owner')
            ->exists();

        if (! $isOwner) {
            return redirect()->route('colocations.index')
                ->with('error', 'Seul le propriétaire peut envoyer une invitation.');
        }

        Mail::to($request->email)->send(new ColocationInvitation($colocation));

        return redirect()->route('colocations.index')
            ->with('success', 'Invitation envoyée avec succès à '.$request->email);
    }

    /**
     * Permet à un utilisateur de rejoindre une colocation via un token.
     */
    public function join(Request $request)
    {
        $request->validate(['invitation_token' => 'required|exists:colocations,invitation_token']);

        if (Auth::user()->colocations()->where('status', 'active')->exists()) {
            return redirect()->back()->with('error', 'Impossible de rejoindre : vous faites déjà partie d\'une colocation active.');
        }

        $colocation = Colocation::where('invitation_token', $request->invitation_token)->first();

        if ($colocation->users()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'Vous êtes déjà membre !');
        }

        $colocation->users()->attach(Auth::id(), ['role' => 'member', 'joined_at' => now()]);

        return redirect()->route('colocations.index')->with('success', 'Bienvenue dans la colocation !');
    }

    /**
     * Permet à un utilisateur de quitter la colocation et met à jour sa réputation.
     */
    public function leave(Colocation $colocation)
    {
        $user = Auth::user();

        // Appliquer le système de réputation avant le départ
        $this->updateUserReputation($user, $colocation);

        // Détacher l'utilisateur de la colocation
        $colocation->users()->detach($user->id);

        return redirect()->route('colocations.index')->with('success', 'Vous avez quitté la colocation. Votre réputation a été mise à jour.');
    }

    /**
     * Permet au propriétaire de retirer un membre et met à jour la réputation du membre retiré.
     */
    public function removeMember(Colocation $colocation, $userId)
{
    // 1. التحقق: واش اللي باغي يحيد العضو هو الـ Owner؟
    if (auth()->id() !== $colocation->owner_id) {
        abort(403, 'Seul l owner peut retirer un membre.');
    }

    $member = $colocation->users()->findOrFail($userId);

    // --- بداية لوجيك الحسابات (Ajustement de dette) ---

    // 2. حساب شحال خاص كل واحد يخلص (Fair Share)
    $totalSpent = $colocation->expenses()->sum('amount');
    $membersCount = $colocation->users()->count();

    if ($membersCount > 0) {
        $fairShare = $totalSpent / $membersCount;

        $memberPaid = $colocation->expenses()->where('user_id', $member->id)->sum('amount');

        $debt = $fairShare - $memberPaid;

        if ($debt > 0) {
            $colocation->expenses()->create([
                'title' => "Reprise de dette (Ex-membre: {$member->name})",
                'amount' => $debt,
                'spent_at' => now(),
                'category_id' => $colocation->categories()->first()->id ?? 1,
                'user_id' => auth()->id(), 
            ]);

            $member->decrement('reputation');
        } else {
            $member->increment('reputation');
        }
    }

    $colocation->users()->detach($userId);

    return redirect()->back()->with('success', 'Membre retiré et dettes ajustées avec succès.');
}

    /**
     * Logique de réputation : +1 si le solde est positif ou nul, -1 s'il y a des dettes.
     */
    private function updateUserReputation($user, $colocation)
    {
        $totalExpenses = $colocation->expenses()->sum('amount');
        $userCount = $colocation->users()->count();

        if ($userCount > 0) {
            $fairShare = $totalExpenses / $userCount;
            $userPaid = $colocation->expenses()->where('user_id', $user->id)->sum('amount');

            // Si l'utilisateur a payé au moins sa part, sa réputation augmente
            if ($userPaid >= $fairShare) {
                $user->increment('reputation');
            } else {
                // S'il part avec une dette envers la colocation, sa réputation baisse
                $user->decrement('reputation');
            }
        }
    }
}
