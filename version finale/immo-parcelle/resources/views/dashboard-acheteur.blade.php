@extends('layouts.app')

@section('content')
<div class="space-y-10 p-4 md:p-8">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-emerald-500/15 border border-emerald-500 text-emerald-400 px-5 py-3 rounded-xl text-sm">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-rose-500/15 border border-rose-500 text-rose-400 px-5 py-3 rounded-xl text-sm">
        ✗ {{ $errors->first() }}
    </div>
    @endif

    {{-- Header --}}
    <div class="bg-gradient-to-r from-[#111c44] to-slate-900 p-8 rounded-3xl border border-white/10 shadow-xl">
        <h1 class="text-3xl font-extrabold text-white">Mon Portefeuille</h1>
        <p class="text-slate-400 mt-1">Suivi détaillé de vos acquisitions et règlements, {{ $client }}</p>
    </div>

    {{-- KPI globaux --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-[#111c44]/40 border border-white/10 rounded-2xl p-5">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Total Engagé</div>
            <div class="text-2xl font-black text-white">{{ number_format($prix_total, 0, ',', ' ') }} $</div>
        </div>
        <div class="bg-[#111c44]/40 border border-emerald-500/20 rounded-2xl p-5">
            <div class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-2">Déjà Payé</div>
            <div class="text-2xl font-black text-emerald-400">{{ number_format($deja_paye, 0, ',', ' ') }} $</div>
        </div>
        <div class="bg-[#111c44]/40 border border-white/10 rounded-2xl p-5">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Restant</div>
            <div class="text-2xl font-black text-rose-400">{{ number_format(max(0, $prix_total - $deja_paye), 0, ',', ' ') }} $</div>
        </div>
        <div class="bg-[#111c44]/40 border border-white/10 rounded-2xl p-5">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Parcelles</div>
            <div class="text-2xl font-black text-white">{{ $parcelles->count() }}</div>
        </div>
    </div>

    {{-- Alerte retard --}}
    @if($mois_retard)
    <div class="bg-rose-500/10 border border-rose-500/30 rounded-2xl p-5 flex items-start gap-4">
        <div class="text-rose-400 text-2xl">⚠️</div>
        <div>
            <div class="text-rose-400 font-bold text-sm">Paiement en retard — {{ $mois_retard }}</div>
            <div class="text-slate-400 text-xs mt-1">
                Mensualité due : <strong class="text-white">{{ number_format($montant_retard, 0, ',', ' ') }} $</strong>
                @if(isset($penalite) && $penalite > 0)
                    + Pénalité : <strong class="text-rose-400">{{ number_format($penalite, 0, ',', ' ') }} $</strong>
                @endif
            </div>
        </div>
        <div class="ml-auto">
            <button onclick="openModal('modal-preuve')" class="bg-rose-500 hover:bg-rose-400 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                Régulariser maintenant
            </button>
        </div>
    </div>
    @endif

    {{-- Parcelles détaillées --}}
    @forelse($parcelles as $p)
    <div class="bg-[#111c44]/40 backdrop-blur-md rounded-3xl border border-white/10 overflow-hidden shadow-lg">

        {{-- Header parcelle --}}
        <div class="p-6 border-b border-white/5">
            <div class="flex flex-wrap justify-between items-start gap-4">
                <div>
                    <h3 class="text-xl font-bold text-white">{{ $p->titre }}</h3>
                    <div class="text-slate-400 text-sm mt-1 flex flex-wrap gap-4">
                        <span>📍 {{ $p->localisation }}</span>
                        @if($p->dimensions)<span>📐 {{ $p->dimensions }}</span>@endif
                        <span>💰 {{ number_format($p->prix_total, 0, ',', ' ') }} $ total</span>
                        <span>📅 {{ number_format($p->mensualite, 0, ',', ' ') }} $/mois</span>
                    </div>
                </div>
                <button onclick="openModal('modal-preuve')" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-5 py-2 rounded-xl font-bold text-sm transition">
                    + Envoyer un paiement
                </button>
            </div>

            {{-- Barre de progression --}}
            <div class="mt-5">
                @php
                    $pct = $p->pourcentage ?? 0;
                    $pctColor = $pct >= 80 ? '#10b981' : ($pct >= 40 ? '#f59e0b' : '#f43f5e');
                @endphp
                <div class="flex justify-between text-xs text-slate-400 mb-2">
                    <span>Progression du remboursement</span>
                    <span class="font-bold" style="color:{{ $pctColor }}">{{ $pct }}%</span>
                </div>
                <div class="h-2 rounded-full bg-slate-900 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-1000" style="width:{{ $pct }}%;background:{{ $pctColor }};"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-500 mt-1">
                    <span>Payé : <strong class="text-emerald-400">{{ number_format($p->somme_payee ?? 0, 0, ',', ' ') }} $</strong></span>
                    <span>Restant : <strong class="text-rose-400">{{ number_format(max(0, $p->prix_total - ($p->somme_payee ?? 0)), 0, ',', ' ') }} $</strong></span>
                </div>
            </div>
        </div>

        {{-- Tableau mensualités --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-950/50 text-slate-500 text-[11px] uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Mois</th>
                        <th class="px-6 py-4">Montant</th>
                        <th class="px-6 py-4">Pénalité</th>
                        <th class="px-6 py-4">État</th>
                        <th class="px-6 py-4">Ma Preuve</th>
                        <th class="px-6 py-4">Confirmation Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @php
                        // Paiements liés à cette parcelle
                        $paiementsParcelle = \App\Models\Paiement::where('parcelle_id', $p->id)
                            ->where('user_id', auth()->id())
                            ->orderBy('created_at', 'desc')
                            ->get();
                    @endphp
                    @forelse($paiementsParcelle as $m)
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-6 py-4 text-white font-medium">{{ $m->mois_concerne }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ number_format($m->montant_paye, 0, ',', ' ') }} $</td>
                        <td class="px-6 py-4">
                            @if($m->penalite_appliquee > 0)
                                <span class="text-rose-400 text-xs font-bold">+{{ number_format($m->penalite_appliquee, 0, ',', ' ') }} $</span>
                            @else
                                <span class="text-slate-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($m->statut === 'valide')
                                <span class="text-emerald-400 text-xs font-bold bg-emerald-500/10 px-2 py-1 rounded">VALIDÉ</span>
                            @elseif($m->statut === 'rejete')
                                <span class="text-rose-400 text-xs font-bold bg-rose-500/10 px-2 py-1 rounded">REJETÉ</span>
                            @else
                                <span class="text-amber-400 text-xs font-bold bg-amber-500/10 px-2 py-1 rounded">EN ATTENTE</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($m->preuve_photo)
                                <a href="{{ asset('storage/'.$m->preuve_photo) }}" target="_blank" class="text-violet-400 hover:underline text-xs">
                                    Voir ma preuve
                                </a>
                            @else
                                <span class="text-slate-600 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($m->preuve_admin)
                                <a href="{{ asset('storage/'.$m->preuve_admin) }}" target="_blank" class="text-emerald-400 hover:underline text-xs flex items-center gap-1">
                                    ✓ Voir reçu admin
                                </a>
                            @else
                                <span class="text-slate-600 text-xs italic">
                                    @if($m->statut === 'en_attente') En cours de vérification...
                                    @elseif($m->statut === 'rejete') Paiement non confirmé
                                    @else En attente...
                                    @endif
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-600 text-sm">
                                Aucun paiement enregistré pour cette parcelle.
                                <br><button onclick="openModal('modal-preuve')" class="text-emerald-400 underline mt-2 inline-block">Envoyer votre premier paiement →</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Récap mini --}}
        <div class="px-6 py-4 border-t border-white/5 flex flex-wrap gap-6 text-xs text-slate-500">
            <span>✅ Validés : <strong class="text-emerald-400">{{ $paiementsParcelle->where('statut','valide')->count() }}</strong></span>
            <span>⏳ En attente : <strong class="text-amber-400">{{ $paiementsParcelle->where('statut','en_attente')->count() }}</strong></span>
            <span>❌ Rejetés : <strong class="text-rose-400">{{ $paiementsParcelle->where('statut','rejete')->count() }}</strong></span>
            <span>📆 Mois en cours : <strong class="text-white">{{ $mois_actuel }}</strong></span>
        </div>

    </div>
    @empty
        <div class="bg-[#111c44]/40 border border-white/10 rounded-3xl p-16 text-center">
            <div class="text-4xl mb-4">🏡</div>
            <div class="text-white font-bold text-lg mb-2">Aucune parcelle pour l'instant</div>
            <p class="text-slate-400 text-sm mb-6">Explorez notre catalogue et souscrivez à une parcelle pour commencer.</p>
            <a href="{{ route('home') }}" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-6 py-3 rounded-xl transition inline-block">
                Voir le catalogue
            </a>
        </div>
    @endforelse

</div>

{{-- Modal envoi preuve --}}
<div id="modal-preuve" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-sm">
    <div class="bg-[#111c44] border border-white/10 rounded-3xl p-8 w-full max-w-lg shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-white font-bold text-xl">Envoyer ma preuve de paiement</h2>
            <button onclick="closeModal('modal-preuve')" class="text-slate-400 hover:text-white text-2xl font-bold">✕</button>
        </div>

        <form action="{{ route('dashboard-acheteur.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            {{-- Parcelle --}}
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Parcelle concernée</label>
                <select name="parcelle_id" required class="w-full bg-slate-950/60 border border-white/10 rounded-xl p-4 text-white text-sm focus:border-emerald-500 outline-none">
                    @foreach($parcelles as $p)
                        <option value="{{ $p->id }}">{{ $p->titre }} — {{ $p->localisation }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mode de paiement (Intégré à l'intérieur du formulaire) --}}
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Mode de paiement</label>
                <select name="compte_mobile_id" required class="w-full font-mono bg-slate-950/60 border border-white/10 rounded-xl p-4 text-white text-sm focus:border-emerald-500 outline-none">
                    <option value="" class="text-slate-900">Sélectionnez un mode de paiement</option>
                    @foreach($comptesMobiles as $compte)
                        <option value="{{ $compte->id }}" class="text-slate-900">
                            {{ str_pad("Opérateur: " . $compte->operateur, 80) }}  
                            {{ str_pad("Numéro: " . $compte->numero, 80) }} 
                            Nom: {{ $compte->nom_compte }}  
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Photo du reçu --}}
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Photo de votre reçu / virement</label>
                <div id="drop-zone"
                    onclick="document.getElementById('file-input').click()"
                    class="border-2 border-dashed border-slate-700 p-8 rounded-2xl text-center cursor-pointer hover:border-emerald-500 transition bg-slate-950/30">
                    <input type="file" id="file-input" name="preuve_photo" class="hidden" accept="image/*" onchange="updateFileName(this)">
                    <div class="text-3xl mb-2">📷</div>
                    <span id="upload-text" class="text-slate-400 text-sm">Cliquez pour joindre la capture</span>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-500 py-4 rounded-xl font-extrabold text-slate-950 uppercase tracking-widest text-xs hover:bg-emerald-400 transition">
                Valider le versement
            </button>
        </form>
    </div>
</div>

<script>
    function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    function updateFileName(input) {
        const el = document.getElementById('upload-text');
        if (input.files[0]) el.textContent = '✓ ' + input.files[0].name;
    }

    @if($errors->any() || session('success'))
        // Rouvrir la modale si erreur ou succès récent
    @endif
</script>

@endsection
