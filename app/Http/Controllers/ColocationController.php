<?php

namespace App\Http\Controllers;

use App\Mail\ColocationInvitation;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ColocationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $colocations = $user->colocations()->get();

        return view('colocations.index', compact('colocations'));
    }

    public function create()
    {
        if (Auth::user()->colocations()->where('status', 'active')->exists()) {
            return redirect()->route('colocations.index')->with('error', 'Vous avez déjà une colocation active.');
        }

        return view('colocations.create');
    }

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

        $colocation->users()->attach(Auth::id(), [
            'role' => 'owner',
        ]);

        return redirect()->route('colocations.index')->with('success', 'Colocation créée avec succès !');
    }

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

    public function join(Request $request)
    {
        $request->validate(['invitation_token' => 'required|exists:colocations,invitation_token']);

        if (Auth::user()->colocations()->where('status', 'active')->exists()) {
            return redirect()->back()->with('error', 'Impossible de rejoindre : vous faites déjà partie d\'une colocation active.');
        }

        $colocation = \App\Models\Colocation::where('invitation_token', $request->invitation_token)->first();

        if ($colocation->users()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('error', 'Vous êtes déjà membre !');
        }

        $colocation->users()->attach(Auth::id(), ['role' => 'member', 'joined_at' => now()]);

        return redirect()->route('colocations.index')->with('success', 'Bienvenue dans la colocation !');

    }

    public function leave(Colocation $colocation)
    {
        $colocation->users()->updateExistingPivot(auth()->id(), [
            'left_at' => now(),
        ]);

        return redirect()->route('colocations.index')->with('success', 'Vous avez quitté la colocation.');
    }

    public function removeMember(Colocation $colocation, $userId)
    {
        $isOwner = $colocation->users()
            ->where('user_id', auth()->id())
            ->where('colocation_user.role', 'owner')
            ->exists();
        if (! $isOwner) {
            return redirect()->back()->with('error', 'Action non autorisée.');
        }
        $colocation->users()->detach($userId);

        return redirect()->back()->with('success', 'Membre retiré avec succès.');
    }
}
