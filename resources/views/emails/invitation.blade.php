<x-mail::message>
    # Salut ! 👋

    Vous avez été invité(e) à rejoindre la colocation **{{ $colocation->name }}**.

    Voici le code secret (Token) pour la rejoindre :
    <x-mail::panel>
        **{{ $colocation->invitation_token }}**
    </x-mail::panel>

    <x-mail::button :url="route('register')">
        Créer un compte et rejoindre
    </x-mail::button>

    À très vite,<br>
    L'équipe {{ config('app.name') }}
</x-mail::message>
