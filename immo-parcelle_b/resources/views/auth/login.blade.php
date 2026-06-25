<!DOCTYPE html>
<html lang="fr" class="h-full antialiased selection:bg-emerald-500 selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - IMMO.RDC</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #080d1e;
        }
        /* Grille cyber subtile à gauche */
        .bg-grid {
            background-image: 
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 24px 24px;
        }
        /* Animation fluide des orbes */
        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(4%, 5%) scale(1.1); }
        }
        .animate-orb {
            animation: drift 15s ease-in-out infinite alternate;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row text-slate-200 overflow-x-hidden">

    <div class="md:w-1/2 bg-[#050a18] relative flex flex-col justify-between p-8 md:p-16 overflow-hidden border-b md:border-b-0 md:border-r border-white/[0.04]">
        <div class="absolute -top-20 -left-20 w-[80%] h-[80%] bg-radial from-emerald-500/10 to-transparent rounded-full blur-[100px] pointer-events-none animate-orb"></div>
        <div class="absolute inset-0 bg-grid pointer-events-none"></div>

        <a href="/" class="relative z-10 flex items-center gap-3 group no-underline text-current">
            <div class="w-9 h-9 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg flex items-center justify-center font-extrabold text-white tracking-tighter shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                IR
            </div>
            <span class="text-xl font-extrabold tracking-tight text-slate-100">IMMO<span class="text-amber-500">.</span>RDC</span>
        </a>

        <div class="relative z-10 max-w-md my-auto pt-16 pb-8 space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-xs font-semibold text-emerald-400 tracking-wide uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Espace sécurisé
            </div>
            <h1 class="text-4xl lg:text-5xl font-light leading-[1.15] text-white">
                Gérez vos parcelles <br>
                <span class="font-extrabold bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">En toute sérénité.</span>
            </h1>
            <p class="text-slate-400 text-sm leading-relaxed">
                Suivez vos plans de paiement mensuels, téléchargez vos documents certifiés et pilotez vos investissements fonciers en République Démocratique du Congo.
            </p>
        </div>

        <div class="relative z-10 text-xs text-slate-500 font-medium">
            &copy; {{ date('Y') }} IMMO.RDC &mdash; Investissements immobiliers premiums.
        </div>
    </div>

    <div class="md:w-1/2 bg-[#080d1e] flex flex-col justify-center p-8 md:p-16 relative">
        <div class="absolute -bottom-40 -right-40 w-[60%] h-[60%] bg-radial from-amber-500/5 to-transparent rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-md mx-auto w-full relative z-10">
            <h2 class="text-3xl font-extrabold text-white tracking-tight mb-2">Bienvenue</h2>
            <p class="text-slate-400 text-sm mb-8">Authentifiez-vous pour accéder à votre tableau de bord.</p>

            @if ($errors->any())
                <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl text-sm space-y-1 backdrop-blur-md">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Erreur de connexion
                    </div>
                    @foreach ($errors->all() as $error)
                        <p class="pl-6 text-slate-300 text-xs">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Adresse Email</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                            class="block w-full pl-11 pr-4 py-3 bg-white/[0.02] border border-white/[0.08] rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200" 
                            placeholder="nom@exemple.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Mot de passe</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" name="password" required 
                            class="block w-full pl-11 pr-4 py-3 bg-white/[0.02] border border-white/[0.08] rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="sr-only peer">
                        <div class="w-4 h-4 bg-white/[0.04] border border-white/[0.1] rounded peer-checked:bg-emerald-500 peer-checked:border-emerald-500 transition-colors flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-slate-900 font-bold hidden peer-checked:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <span class="ml-2.5 text-xs font-medium text-slate-400 hover:text-slate-300 transition-colors">Se souvenir de moi</span>
                    </label>
                </div>

                <button type="submit" 
                    class="w-full mt-2 py-3 px-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-bold rounded-xl shadow-[0_4px_20px_rgba(16,185,129,0.2)] hover:shadow-[0_4px_25px_rgba(16,185,129,0.35)] transition-all duration-200 transform active:scale-[0.99]">
                    Se connecter au portail
                </button>
            </form>

            <p class="mt-8 text-xs text-center text-slate-500 font-medium">
                Vous n'avez pas encore de compte utilisateur ? 
                <a href="{{ route('register') }}" class="font-bold text-emerald-400 hover:text-emerald-300 hover:underline ml-1 transition-colors">Créer un compte</a>
            </p>
        </div>
    </div>

</body>
</html>