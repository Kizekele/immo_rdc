<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Immo Parcelle</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col md:flex-row">

    <div class="md:w-1/2 bg-[#061630] text-white flex flex-col justify-center px-12 py-16">
        <div class="max-w-md mx-auto space-y-6">
            <span class="text-sm font-bold tracking-wider text-blue-400 uppercase">Immo Parcelle</span>
            <h1 class="text-5xl font-light leading-tight">Créez Votre Compte <br><span class="font-bold">Aujourd'hui</span></h1>
            <p class="text-gray-400 text-sm">Rejoignez notre plateforme pour planifier vos investissements immobiliers sereinement.</p>
        </div>
    </div>

    <div class="md:w-1/2 bg-white flex flex-col justify-center px-12 py-16 overflow-y-auto">
        <div class="max-w-md mx-auto w-full">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Inscription</h2>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-md text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="border-b border-gray-200 pb-2 mb-2">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Identifiants de connexion</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mot de passe</label>
                        <input type="password" name="password" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmation</label>
                        <input type="password" name="password_confirmation" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-2 pt-4 mb-2">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Détails du Profil</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Postnom</label>
                        <input type="text" name="postnom" value="{{ old('postnom') }}" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Numéro de téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="Ex: +243..." required class="block mt-1 w-full rounded-md border-gray-300 shadow-sm border p-2.5 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pièce d'identité (Photo ou PDF de la carte)</label>
                    <input type="file" name="piece_identite" accept="image/*,application/pdf" required class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border p-1.5 rounded-md">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md shadow-sm transition duration-150 mt-6">
                    S'inscrire
                </button>
            </form>

            <p class="mt-6 text-sm text-center text-gray-600">
                Vous avez déjà un compte ? 
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline">Se connecter</a>
            </p>
        </div>
    </div>

</body>
</html>