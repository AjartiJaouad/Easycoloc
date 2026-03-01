<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EasyColoc – Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-6 rounded-xl shadow-md w-80">

        <!-- Title -->
        <h1 class="text-xl font-bold text-center mb-4">EasyColoc</h1>
        <p class="text-sm text-gray-500 text-center mb-4">Connexion</p>

        <!-- Form -->
        <form>

            <!-- Email -->
            <input
                type="email"
                placeholder="Email"
                class="w-full border p-2 rounded mb-3"
                required
            >

            <!-- Password -->
            <input
                type="password"
                placeholder="Mot de passe"
                class="w-full border p-2 rounded mb-3"
                required
            >

            <!-- Checkbox -->
            <div class="mb-3 text-sm">
                <input type="checkbox"> Se souvenir de moi
            </div>

            <!-- Button -->
            <button
                class="w-full bg-indigo-600 text-white p-2 rounded hover:bg-indigo-700">
                Se connecter
            </button>

        </form>

        <!-- Register -->
        <p class="text-sm text-center mt-4">
            Pas encore membre ?
            <a href="#" class="text-indigo-600">Créer un compte</a>
        </p>

    </div>

</body>
</html>
