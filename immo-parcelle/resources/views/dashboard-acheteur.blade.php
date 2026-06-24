@extends('layouts.app')

@section('content')
<div class="space-y-10" x-data="{ copied: '' }">
    
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-gradient-to-r from-[#111c44]/40 to-transparent p-6 rounded-2xl border border-white/5">
        <div>
            <h1 class="text-3xl font-extrabold text-white">Bonjour, {{ Auth::user()->name }}</h1>
            <p class="text-slate-400 text-sm mt-1">Espace Portefeuille Multi-acquisitions</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-[#111c44]/50 backdrop-blur-md p-6 rounded-2xl border border-white/5 space-y-6">
            <h2 class="text-xl font-bold text-white">Comment payer ?</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <button onclick="openModal('modal-mobile')" class="p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl hover:bg-emerald-500/20 transition text-center">
                    <span class="block text-emerald-400 font-bold">Mobile Money</span>
                </button>
                
                <button onclick="openModal('modal-bancaire')" class="p-6 bg-blue-500/10 border border-blue-500/20 rounded-2xl hover:bg-blue-500/20 transition text-center">
                    <span class="block text-blue-400 font-bold">Bancaire</span>
                </button>
            </div>
        </div>

        <form action="{{ route('dashboard-acheteur.store') }}" method="POST" enctype="multipart/form-data" class="bg-[#111c44]/50 p-6 rounded-2xl border border-white/5">
            @csrf
            <h2 class="text-xl font-bold text-white mb-4">Envoyer la preuve</h2>
            
            <select name="parcelle_id" required class="w-full bg-slate-950/60 border border-white/10 rounded-xl p-3.5 text-white mb-4">
                @foreach($parcelles as $p)
                    <option value="{{ $p->id }}">{{ $p->titre }}</option>
                @endforeach
            </select>

            <div id="drop-zone" onclick="triggerFileInput()" class="border-2 border-dashed border-slate-700 p-6 rounded-xl text-center cursor-pointer">
                <input type="file" id="file-input" name="preuve_photo" class="hidden" onchange="handleFileSelect(this)">
                <span id="upload-text" class="text-slate-300">Cliquez pour ajouter votre reçu</span>
            </div>

            <button type="submit" class="w-full mt-4 bg-emerald-500 py-3 rounded-xl font-bold text-slate-950">Valider</button>
        </form>
    </div>
</div>

<div id="modal-mobile" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div class="bg-[#111c44] border border-white/10 rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-white font-bold mb-4">Numéros Mobile Money</h3>
        <div class="space-y-3">
            @foreach($comptesMobiles as $compte)
                <button onclick="copyToClipboard('{{ $compte->numero }}')" class="w-full flex justify-between p-4 bg-slate-950/40 rounded-xl border border-white/5">
                    <span class="text-emerald-400 font-bold">{{ $compte->banque }}</span>
                    <span class="text-white font-mono">{{ $compte->numero }}</span>
                </button>
            @endforeach
        </div>
        <button onclick="closeModal('modal-mobile')" class="mt-6 w-full py-2 bg-slate-800 rounded-lg text-white">Fermer</button>
    </div>
</div>

<div id="modal-bancaire" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div class="bg-[#111c44] border border-white/10 rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-white font-bold mb-4">Coordonnées Bancaires</h3>
        <div class="bg-slate-950/40 p-4 rounded-xl text-slate-300 text-sm space-y-2">
            <p><strong>Banque :</strong> Nom de la Banque</p>
            <p><strong>RIB :</strong> 0000 0000 0000 0000</p>
            <p><strong>Titulaire :</strong> Société XYZ</p>
        </div>
        <button onclick="closeModal('modal-bancaire')" class="mt-6 w-full py-2 bg-slate-800 rounded-lg text-white">Fermer</button>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    
    function triggerFileInput() { document.getElementById('file-input').click(); }
    
    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            document.getElementById('upload-text').innerText = "Fichier : " + input.files[0].name;
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('Numéro copié !');
    }
</script>
@endsection