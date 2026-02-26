<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ColocationInvitation;

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
        return view('colocations.create');
    }

    public function store(Request $request)
    {
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

        return redirect()->route('colocations.index')->with('success', 'Colocation créée avec succès ! Token d\'invitation : ' . $colocation->invitation_token);
    }

    public function cancel(Colocation $colocation)
    {
        $isOwner = $colocation->users()
            ->where('user_id', Auth::id())
            ->where('colocation_user.role', 'owner')
            ->exists();

        if (!$isOwner) {
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
            ->with('success', 'Invitation envoyée avec succès à ' . $request->email);
    }
}
