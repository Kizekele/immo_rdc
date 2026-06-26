@extends('layouts.app')

@section('content')
<div class="space-y-12">
    
    <div class="relative rounded-3xl overflow-hidden bg-gray-900 min-h-[440px] flex items-center p-8 md:p-16 shadow-xl group">
        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 transform group-hover:scale-105" style="background-image: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80')"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
        
        <div class="relative z-10 max-w-xl space-y-6 animate-fadeIn text-white">
            <span class="inline-flex items-center gap-1.5 bg-yellow-500 text-gray-900 text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest">
                <span class="w-2 h-2 rounded-full bg-gray-900 animate-ping"></span>
                Nouveau projet disponible
            </span>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-none">
                Trouvez la parcelle de vos rêves.
            </h1>
            <p class="text-gray-300 text-base md:text-lg leading-relaxed">
                Devenez propriétaire aujourd'hui grâce à nos options de financement échelonné uniques : versez des mensualités souples sur <span class="text-yellow-400 font-bold underline">50 mois</span>.
            </p>
            <div class="flex flex-wrap gap-4 pt-2">
                <a href="#catalogue" class="bg-white text-gray-900 hover:bg-yellow-400 font-extrabold px-6 py-3.5 rounded-xl transition duration-300 shadow-md transform active:scale-95 text-sm uppercase tracking-wider">
                    Explorer le catalogue
                </a>
                <div class="flex items-center gap-2 text-xs font-bold text-gray-300 bg-white/10 backdrop-blur-md px-4 py-3 rounded-xl border border-white/10">
                    🔥 Plus que {{ $parcellesLibresCount }} parcelles libres
                </div>
            </div>
        </div>
    </div>

    <div id="catalogue" class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center sticky top-4 z-40 backdrop-blur-md bg-white/90">
        <div class="relative w-full md:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" id="search-bar" onkeyup="filterParcelles()" placeholder="Rechercher une commune, réf..." class="w-full bg-gray-50 border border-gray-200 text-gray-900 placeholder-gray-400 text-sm rounded-xl pl-9 pr-4 py-3 focus:outline-none focus:border-gray-900 transition">
        </div>
        
        <div class="flex flex-wrap gap-2 w-full md:w-auto justify-end">
            <button onclick="filterStatus('all')" class="filter-btn active-filter px-4 py-2 text-xs font-bold rounded-lg border bg-gray-900 text-white transition">Tout voir</button>
            <button onclick="filterStatus('disponible')" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg border bg-white text-gray-600 border-gray-200 hover:border-gray-900 transition">Disponible</button>
            <button onclick="filterStatus('en_cours')" class="filter-btn px-4 py-2 text-xs font-bold rounded-lg border bg-white text-gray-600 border-gray-200 hover:border-gray-900 transition">Déjà Optionné</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="parcelles-container">
        
        @foreach($parcelles as $parcelle)
            <div class="parcelle-card bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:border-gray-200 transition-all duration-300 transform hover:-translate-y-1 {{ $parcelle->statut === 'en_cours' ? 'opacity-75' : '' }}" 
                 data-status="{{ $parcelle->statut }}" 
                 data-location="{{ strtolower($parcelle->localisation) }}">
                
                <div class="h-48 bg-gray-100 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110" 
                         style="background-image: url('{{ $parcelle->photo && !str_contains($parcelle->photo, 'mimes') ? asset('storage/' . $parcelle->photo) : 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=600&q=80' }}')">
                    </div>
                    
                    @if($parcelle->statut === 'disponible')
                        <span class="absolute top-4 left-4 bg-emerald-500 text-white text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider shadow-sm animate-pulse">Disponible</span>
                    @else
                        <span class="absolute top-4 left-4 bg-amber-500 text-white text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider shadow-sm">Sous Option</span>
                    @endif
                    
                    <span class="absolute bottom-4 right-4 bg-gray-900/80 backdrop-blur-md text-white text-xs font-mono font-bold px-2 py-1 rounded">Réf: #{{ $parcelle->id }}</span>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-start">
                        <h3 class="text-xl font-black text-gray-900 client-title-text">
                            {{ $parcelle->titre }}
                        </h3>

                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">
                            {{ $parcelle->dimension }}
                        </span>


                        


                    </div>
                    
                    
                                        
                    <div class="border-t border-gray-100 pt-4 flex justify-between items-end">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Prix Global</span>
                            <span class="text-2xl font-black text-gray-900">{{ number_format($parcelle->prix_total, 0, ',', ' ') }} $</span>
                        </div>
                        
                        @if($parcelle->statut === 'disponible')
                            <div class="text-right">
                                <span class="text-[10px] text-gray-400 font-bold block uppercase tracking-wider">Échelonné</span>
                                <span class="text-sm font-extrabold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded">{{ number_format($parcelle->mensualite, 0, ',', ' ') }} $/mois</span>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-2 text-xs pt-2">
                        @if($parcelle->statut === 'disponible')
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="openSimulator({{ json_encode([
                                    'titre' => $parcelle->titre,
                                    'localisation' => $parcelle->localisation,
                                    'dimensions' => $parcelle->dimensions,
                                    'prix_total' => $parcelle->prix_total,
                                    'mensualite' => $parcelle->mensualite,
                                    'statut' => $parcelle->statut,
                                    'photo' => $parcelle->photo]) }})" class="w-full bg-gray-50 text-gray-900 font-bold py-3 rounded-xl hover:bg-gray-100 transition border border-gray-200 active:scale-98">
                                    Voir plus
                                </button>
                                <form action="{{ route('parcelle.souscrire', $parcelle->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-emerald-600 text-center transition shadow-sm active:scale-98 block">
                                        Souscrire
                                    </button>
                                </form>
                            </div>
                        @else
                            <button disabled class="w-full bg-gray-100 text-gray-400 font-bold py-3.5 rounded-xl cursor-not-allowed text-xs uppercase tracking-wide">
                                En attente de validation finale
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>

<div id="simulator-modal" class="fixed inset-0 bg-gray-900/70 hidden items-center justify-center z-50" style="backdrop-filter:blur(4px);">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative text-gray-800">
        <button onclick="closeSimulator()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 font-bold text-xl">&times;</button>
        
        <h3 id="sim-titre" class="text-xl font-bold text-gray-900 mb-4"></h3>
        <div class="space-y-2 text-sm text-gray-600">
            <img id="sim-image" src="" alt="Parcelle" class="w-full h-48 object-cover rounded-lg mb-4 hidden">
            <h3 id="sim-titre" class="text-xl font-bold text-gray-900 mb-4"></h3>
            <p>Localisation: <span id="sim-localisation" class="font-semibold text-gray-800"></span></p>
            <p>Dimensions: <span id="sim-dimensions" class="font-semibold text-gray-800"></span></p>
            <p>Prix Total: <span id="sim-prix" class="font-semibold text-gray-800"></span></p>
            <p>Mensualité: <span id="sim-mensualite" class="font-semibold text-gray-800"></span></p>
            <p>Statut: <span id="sim-statut" class="inline-block px-2 py-1 rounded bg-gray-100 text-xs font-bold uppercase"></span></p>

            <form action="{{ route('parcelle.souscrire', $parcelle->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-emerald-600 text-center transition shadow-sm active:scale-98 block">
                    Souscrire
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    let currentBasePrice = 0;

    // 1. Recherche instantanée par mot-clé (Commune, nom, réf)
    function filterParcelles() {
        const query = document.getElementById('search-bar').value.toLowerCase();
        const cards = document.querySelectorAll('.parcelle-card');

        cards.forEach(card => {
            const title = card.querySelector('.client-title-text').innerText.toLowerCase();
            const location = card.getAttribute('data-location');
            
            if (title.includes(query) || location.includes(query)) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    }

    // 2. Filtrage par bouton de catégorie de statut ('all', 'disponible', 'en_cours')
    function filterStatus(status) {
        const cards = document.querySelectorAll('.parcelle-card');
        const buttons = document.querySelectorAll('.filter-btn');

        buttons.forEach(btn => btn.classList.remove('bg-gray-900', 'text-white'));
        event.target.classList.add('bg-gray-900', 'text-white');

        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            if (status === 'all' || cardStatus === status) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    }

    // 3. Ouvrir le calculateur de crédit dynamique
    function openSimulator(parcelle) {
        // 1. Récupérer les éléments HTML de votre modale de simulation
        // (Adaptez les ID selon votre code HTML)
        document.getElementById('sim-titre').innerText = parcelle.titre;
        document.getElementById('sim-localisation').innerText = parcelle.localisation;
        document.getElementById('sim-dimensions').innerText = parcelle.dimensions || 'Non spécifié';
        document.getElementById('sim-prix').innerText = parcelle.prix_total + ' $';
        document.getElementById('sim-mensualite').innerText = parcelle.mensualite + ' $';
        document.getElementById('sim-statut').innerText = parcelle.statut;

        // Gestion de l'image
        const imgElement = document.getElementById('sim-image');
        if (parcelle.photo) {
            imgElement.src = "/storage/" + parcelle.photo;
            imgElement.classList.remove('hidden');
        } else {
            imgElement.classList.add('hidden');
        }

        // 2. Afficher la modale
        document.getElementById('simulator-modal').classList.remove('hidden');
        document.getElementById('simulator-modal').classList.add('flex');

        const modal = document.getElementById('sim-modal');
        modal.classList.remove('opacity-0', 'pointer-events-none');
        modal.querySelector('.transform').classList.remove('scale-95');
    }

    function closeSimulator() {
        document.getElementById('simulator-modal').classList.add('hidden');
        document.getElementById('simulator-modal').classList.remove('flex');
    }

    // 4. Calcul de la mensualité restante (Reste à payer divisé par 50 mois)
    function calculateCredit() {
        const acompte = parseInt(document.getElementById('acompte-slider').value);
        document.getElementById('acompte-val').innerText = acompte.toLocaleString() + " $";

        const resteAPayer = currentBasePrice - acompte;
        const nouvelleMensualite = Math.round(resteAPayer / 50);

        document.getElementById('modal-new-monthly').innerText = nouvelleMensualite + " $/m";
    }
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection