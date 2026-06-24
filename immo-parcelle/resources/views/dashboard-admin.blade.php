@extends('layouts.app')

@section('content')

{{--
    VARIABLES BLADE TRANSMISES PAR LE CONTROLLER :
    $total_attendu_global   → string|int (ex: 45200)
    $total_encaisse_global  → string|int (ex: 31800)
    $total_attente_global   → string|int (ex: 4500)
    $pct_encaisse           → int (ex: 70)
    $paiements              → Collection de modèles App\Models\Paiement chargés avec 'user.profil' et 'parcelle'
--}}

<style>
    :root {
        --c-card:     rgba(13, 20, 44, 0.55);
        --c-border:   rgba(255, 255, 255, 0.07);
        --c-border-em: rgba(255, 255, 255, 0.12);
        --c-text:     #e2e8f0;
        --c-muted:    #64748b;
        --c-sub:      #94a3b8;
        --emerald:    #10b981;
        --amber:      #f59e0b;
        --rose:       #f43f5e;
    }

    /* ─── KPI Grid ─── */
    .admin-kpi-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }
    @media (max-width: 768px) { .admin-kpi-grid { grid-template-columns: 1fr; } }

    .akpi {
        background: var(--c-card);
        backdrop-filter: blur(12px);
        border: 1px solid var(--c-border);
        border-radius: 16px;
        padding: 20px 22px;
        position: relative;
        overflow: hidden;
        transition: transform 0.25s, border-color 0.25s;
    }
    .akpi:hover { transform: translateY(-2px); border-color: var(--c-border-em); }
    .akpi.green { border-color: rgba(16,185,129,0.2); }
    .akpi.amber { border-color: rgba(245,158,11,0.15); }

    .akpi-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--c-muted);
    }
    .akpi.green .akpi-label { color: #10b981; }
    .akpi.amber .akpi-label { color: #d97706; }

    .akpi-value {
        font-size: 28px;
        font-weight: 800;
        color: #f1f5f9;
        margin-top: 10px;
        letter-spacing: -1px;
        line-height: 1;
    }
    .akpi.green .akpi-value { color: #10b981; }
    .akpi.amber .akpi-value { color: #d97706; }

    .akpi-bar {
        margin-top: 14px;
        background: rgba(0,0,0,0.3);
        border-radius: 99px;
        height: 4px;
        overflow: hidden;
    }
    .akpi-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, #10b981, #34d399);
        box-shadow: 0 0 8px rgba(16,185,129,0.35);
        animation: bar-grow 1.2s ease-out 0.4s both;
    }
    @keyframes bar-grow { from { width: 0 !important; } }

    /* ─── Tableau principal ─── */
    .table-card {
        background: var(--c-card);
        backdrop-filter: blur(12px);
        border: 1px solid var(--c-border);
        border-radius: 18px;
        overflow: hidden;
    }
    .table-card-header {
        padding: 20px 24px 16px;
        border-bottom: 1px solid var(--c-border);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
    }
    .table-card-title { font-size: 15px; font-weight: 700; color: var(--c-text); }
    .table-card-sub   { font-size: 12px; color: var(--c-muted); margin-top: 3px; max-width: 500px; }

    .count-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        color: #d97706;
        background: rgba(245,158,11,0.08);
        border: 1px solid rgba(245,158,11,0.25);
        padding: 5px 12px;
        border-radius: 8px;
        white-space: nowrap;
    }
    .count-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; }

    /* Filtres */
    .filter-bar {
        padding: 14px 24px;
        border-bottom: 1px solid var(--c-border);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .filter-label { font-size: 11px; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: 0.07em; margin-right: 4px; }
    .filter-pill {
        font-size: 12px;
        font-weight: 600;
        padding: 5px 13px;
        border-radius: 99px;
        border: 1px solid var(--c-border);
        background: transparent;
        color: var(--c-sub);
        cursor: pointer;
        transition: all 0.15s;
    }
    .filter-pill:hover { background: rgba(255,255,255,0.04); color: var(--c-text); }
    .filter-pill.active { background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.3); color: #10b981; }

    /* Table */
    .payments-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .payments-table thead th {
        padding: 12px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: var(--c-muted);
        background: rgba(0,0,0,0.1);
        white-space: nowrap;
    }
    .payments-table tbody tr {
        border-top: 1px solid var(--c-border);
        transition: background 0.15s;
        cursor: pointer;
    }
    .payments-table tbody tr:hover { background: rgba(255,255,255,0.02); }
    .payments-table td {
        padding: 14px 20px;
        vertical-align: middle;
        color: var(--c-sub);
    }
    .payments-table td:first-child { color: var(--c-text); }

    /* Cellule client */
    .client-cell { display: flex; align-items: center; gap: 12px; }
    .client-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        flex-shrink: 0;
        letter-spacing: 0.5px;
    }
    .client-name { font-size: 13px; font-weight: 700; color: var(--c-text); }
    .client-ref  { font-size: 11px; color: var(--c-muted); font-family: monospace; margin-top: 2px; }
    .client-date { font-size: 10px; color: #334155; margin-top: 3px; }

    /* Badges canal */
    .canal-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        white-space: nowrap;
    }
    .canal-badge .cdot { width: 6px; height: 6px; border-radius: 50%; }
    .canal-orange { background: rgba(249,115,22,0.08); border: 1px solid rgba(249,115,22,0.2); color: #fb923c; }
    .canal-orange .cdot { background: #f97316; }
    .canal-mpesa  { background: rgba(37,99,235,0.08); border: 1px solid rgba(37,99,235,0.2); color: #60a5fa; }
    .canal-mpesa  .cdot { background: #2563eb; }
    .canal-airtel { background: rgba(244,63,94,0.08); border: 1px solid rgba(244,63,94,0.2); color: #fb7185; }
    .canal-airtel .cdot { background: #f43f5e; }

    /* Montant */
    .amount-cell { font-family: monospace; font-size: 14px; font-weight: 800; color: var(--c-text); }

    /* Reçu */
    .receipt-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #7c3aed;
        background: rgba(124,58,237,0.06);
        border: 1px solid rgba(124,58,237,0.15);
        padding: 5px 11px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s;
    }
    .receipt-btn:hover { background: rgba(124,58,237,0.12); border-color: rgba(124,58,237,0.3); color: #a78bfa; }

    /* Boutons action */
    .action-cell { display: flex; align-items: center; justify-content: flex-end; gap: 6px; }
    .btn-reject {
        font-size: 11px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid rgba(244,63,94,0.25);
        background: rgba(244,63,94,0.06);
        color: #fb7185;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-reject:hover { background: rgba(244,63,94,0.12); border-color: rgba(244,63,94,0.4); }

    .btn-approve {
        font-size: 11px;
        font-weight: 700;
        padding: 6px 13px;
        border-radius: 8px;
        border: none;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        cursor: pointer;
        transition: all 0.15s;
        box-shadow: 0 2px 10px rgba(16,185,129,0.2);
    }
    .btn-approve:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(16,185,129,0.3); }

    /* Empty state */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        display: none;
    }
    .empty-state.visible { display: block; }
    .empty-icon { font-size: 36px; color: #1e293b; margin-bottom: 12px; }
    .empty-title { font-size: 15px; font-weight: 700; color: #334155; }
    .empty-sub   { font-size: 12px; color: var(--c-muted); margin-top: 4px; }

    /* ─── Modale reçu ─── */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.75);
        backdrop-filter: blur(6px);
        z-index: 200;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s;
        padding: 20px;
    }
    .modal-backdrop.open { opacity: 1; pointer-events: all; }
    .modal-box {
        background: #0d1424;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 20px;
        padding: 24px;
        width: 100%;
        max-width: 440px;
        transform: translateY(16px) scale(0.98);
        transition: transform 0.25s;
        position: relative;
    }
    .modal-backdrop.open .modal-box { transform: translateY(0) scale(1); }
    .modal-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; }
    .modal-title  { font-size: 15px; font-weight: 700; color: var(--c-text); }
    .modal-meta   { font-size: 12px; color: var(--c-muted); margin-top: 3px; }
    .modal-close  { background: rgba(255,255,255,0.05); border: 1px solid var(--c-border); color: var(--c-muted); width: 30px; height: 30px; border-radius: 8px; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: all 0.15s; flex-shrink: 0; }
    .modal-close:hover { background: rgba(255,255,255,0.1); color: var(--c-text); }
    .modal-image-wrap { background: rgba(0,0,0,0.3); border-radius: 12px; height: 220px; border: 1px solid var(--c-border); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .modal-placeholder { text-align: center; width: 100%; height: 100%; }
    .modal-placeholder img { width: 100%; height: 100%; object-fit: cover; }
    .modal-placeholder svg { color: #1e293b; margin: 0 auto 10px; display: block; }
    .modal-placeholder p  { font-size: 12px; color: #334155; font-family: monospace; }

    /* ─── Row d'animation ─── */
    .payment-row { transition: opacity 0.35s, transform 0.35s; }
    .payment-row.removing { opacity: 0; transform: translateX(40px); }

    /* ─── En-tête page ─── */
    .page-header {
        background: linear-gradient(120deg, rgba(13,20,44,0.7) 0%, rgba(124,58,237,0.03) 100%);
        border: 1px solid var(--c-border);
        border-radius: 20px;
        padding: 22px 28px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .page-title {
        font-size: 22px;
        font-weight: 800;
        color: #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .page-title-badge {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #a78bfa;
        background: rgba(124,58,237,0.1);
        border: 1px solid rgba(124,58,237,0.2);
        padding: 3px 10px;
        border-radius: 6px;
        font-family: monospace;
    }
    .page-sub { font-size: 13px; color: var(--c-muted); margin-top: 5px; }
    .search-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .search-icon {
        position: absolute;
        left: 12px;
        color: var(--c-muted);
        pointer-events: none;
    }
    .search-input {
        background: rgba(0,0,0,0.3);
        border: 1px solid var(--c-border);
        color: var(--c-text);
        font-size: 13px;
        border-radius: 12px;
        padding: 9px 14px 9px 36px;
        width: 240px;
        outline: none;
        transition: all 0.2s;
        font-family: inherit;
    }
    .search-input::placeholder { color: #334155; }
    .search-input:focus { border-color: rgba(16,185,129,0.4); background: rgba(16,185,129,0.03); }
</style>

<div x-data="{ search: '' }">

    {{-- Alertes Flash Laravel --}}
    @if(session('success'))
        <div style="background: rgba(16,185,129,0.2); border: 1px solid #10b981; color: #10b981; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- ──────────────── En-tête page ──────────────── --}}
    <div class="page-header">
        <div>
            <div class="page-title">
                Tableau de Bord Admin
                <span class="page-title-badge">Finance</span>
            </div>
            <div class="page-sub">Gestion des recouvrements, validation des preuves de paiement et suivi des parcelles.</div>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition text-sm">
                + Ajouter une parcelle
            </button>

            <div class="search-wrap">
                <div class="search-icon" aria-hidden="true">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       id="admin-search"
                       class="search-input"
                       placeholder="Rechercher un client..."
                       oninput="filterTable(this.value)"
                       aria-label="Rechercher un client dans le tableau">
            </div>
        </div>
    </div>

    {{-- ──────────────── KPI COUPLÉS À LA BDD ──────────────── --}}
    <div class="admin-kpi-grid">
        <div class="akpi">
            <div class="akpi-label">Total Attendu (Ce mois)</div>
            <div class="akpi-value">{{ number_format($total_attendu_global, 2, ',', ' ') }} $</div>
        </div>
        <div class="akpi green">
            <div class="akpi-label">Total Validé &amp; Encaissé</div>
            <div class="akpi-value">{{ number_format($total_encaisse_global, 2, ',', ' ') }} $</div>
            <div class="akpi-bar">
                <div class="akpi-fill" style="width: {{ $pct_encaisse }}%"></div>
            </div>
        </div>
        <div class="akpi amber">
            <div class="akpi-label">Flux en Attente</div>
            <div class="akpi-value">{{ number_format($total_attente_global, 2, ',', ' ') }} $</div>
        </div>
    </div>

    {{-- ──────────────── Tableau paiements ──────────────── --}}
    <div class="table-card">
        <div class="table-card-header">
            <div>
                <div class="table-card-title">Preuves de paiement soumises</div>
                <div class="table-card-sub">Croisez les informations avec vos comptes Mobile Money avant validation.</div>
            </div>
            <span class="count-badge" id="pending-count">
                <span class="dot" aria-hidden="true"></span>
                <span id="count-text">{{ $paiements->count() }} à vérifier</span>
            </span>
        </div>

        {{-- listes clients - parcelles --}}
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Parcelles possédées</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                    <td>
                        @foreach($client->parcelles as $p)
                            <span class="badge">{{ $p->titre }}</span>
                        @endforeach
                    </td>
                    <td>
                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Filtres canal basés sur votre logique front --}}
        <div class="filter-bar" role="group" aria-label="Filtrer par canal de paiement">
            <span class="filter-label">Canal :</span>
            <button class="filter-pill active" onclick="filterCanal('all', this)" type="button">Tous</button>
            <button class="filter-pill" onclick="filterCanal('orange', this)" type="button">Orange Money</button>
            <button class="filter-pill" onclick="filterCanal('mpesa', this)" type="button">M-Pesa</button>
            <button class="filter-pill" onclick="filterCanal('airtel', this)" type="button">Airtel Money</button>
            <button class="filter-pill" onclick="filterCanal('virement bancaire', this)">Virement Bancaire</button>
        </div>

        {{-- evolution suivi individuel --}}
        <div class="mt-8 bg-[var(--c-card)] border border-[var(--c-border)] rounded-xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">Suivi individuel par Client & Mois</h3>
                <table class="table-row-item">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Parcelle</th>
                            <th>Progression</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suiviGlobal as $parcelle)
                            <tr class="table-row-item" data-name="{{ strtolower($parcelle->user->name) }}">
                                <td>{{ $parcelle->user->name }}</td>
                                <td>{{ $parcelle->titre }}</td>
                                <td>
                                    @php 
                                        $progression = ($parcelle->somme_payee / $parcelle->prix_total) * 100;
                                    @endphp
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progression }}%"></div>
                                    </div>
                                    {{ number_format($progression, 0) }}%
                                </td>
                                <td>{{ $parcelle->statut }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    

        {{-- Table --}}
        <div style="overflow-x: auto;">
            <table class="payments-table" id="payments-table" aria-label="Liste des paiements en attente">
                <thead>
                    <tr>
                        <th>Client / Parcelle</th>
                        <th>Canal</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Pièce jointe</th>
                        <th style="text-align: right">Actions</th>
                    </tr>
                </thead>
                <tbody id="payments-tbody">

                    @forelse($paiements as $p)
                        @php
                            $nomComplet = $p->user->profil 
                                ? ($p->user->profil->prenom . ' ' . $p->user->profil->nom) 
                                : $p->user->name;

                            $initiales = collect(explode(' ', $nomComplet))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                            
                            $canalSlug = match(strtolower($p->canal ?? '')) {
                                'orange',
                                'orange money' => 'orange',

                                'mpesa',
                                'm-pesa' => 'mpesa',

                                'airtel',
                                'airtel money' => 'airtel',

                                default => 'airtel',
                            };
                            
                            $avatarColors = [
                                'orange' => 'background:rgba(249,115,22,0.12);color:#fb923c;',
                                'mpesa'  => 'background:rgba(37,99,235,0.12);color:#60a5fa;',
                                'airtel' => 'background:rgba(244,63,94,0.12);color:#fb7185;',
                            ];
                        @endphp
                        <tr class="payment-row table-row-item"
                            data-id="{{ $p->id }}"
                            data-name="{{ strtolower($nomComplet) }}"
                            data-canal="{{ $canalSlug }}">
                            <td>
                                <div class="client-cell">
                                    <div class="client-avatar" style="{{ $avatarColors[$canalSlug] ?? 'background:#334155;' }}">{{ $initiales }}</div>
                                    <div>
                                        <div class="client-name">{{ $nomComplet }}</div>
                                        <div class="client-ref">{{ $p->parcelle->titre ?? 'Sans titre' }} / {{ $p->parcelle->localisation ?? 'Inconnue' }}</div>
                                        <div class="client-date">Mois: {{ $p->mois_concerne }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="canal-badge canal-{{ $canalSlug }}">
                                    <span class="cdot" aria-hidden="true"></span>
                                    {{ ucfirst($canalSlug) }} Money
                                </span>
                            </td>
                            <td>
                                <span class="amount-cell">{{ number_format($p->montant_paye, 2, ',', ' ') }} $</span>
                                @if($p->penalite_appliquee > 0)
                                    <div style="font-size:10px; color:var(--rose);">+{{ $p->penalite_appliquee }} $ Pén.</div>
                                @endif
                            </td>
                            <td style="font-size:12px; color: var(--c-muted); white-space:nowrap;">
                                {{ $p->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <button type="button"
                                        class="receipt-btn"
                                        onclick="openReceiptModal('{{ addslashes($nomComplet) }}', '{{ $p->parcelle->titre ?? '' }}', '{{ $p->preuve_photo }}')"
                                        aria-label="Voir le reçu de {{ $nomComplet }}">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Voir la preuve
                                </button>
                            </td>
                            <td>
                                <div class="action-cell">
                                    <form action="{{ route('admin.paiements.action', [$p->id, 'refuser']) }}" method="POST" onsubmit="return handleFormAction(event, this)">
                                        @csrf
                                        <button type="submit" class="btn-reject" aria-label="Rejeter le paiement">Rejeter</button>
                                    </form>

                                    <form action="{{ route('admin.paiements.action', [$p->id, 'approuver']) }}" method="POST" onsubmit="return handleFormAction(event, this)">
                                        @csrf
                                        <button type="submit" class="btn-approve" aria-label="Approuver le paiement">Approuver</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        {{-- Géré dynamiquement par l'empty-state ci-dessous --}}
                    @endforelse

                </tbody>
            </table>

            {{-- Empty state --}}
            <div class="empty-state {{ $paiements->isEmpty() ? 'visible' : '' }}" id="empty-state" aria-live="polite">
                <div class="empty-icon">
                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="color:#1e293b; margin:0 auto; display:block;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="empty-title">Aucun paiement en attente</div>
                <div class="empty-sub">Tous les flux financiers ont été vérifiés et arbitrés.</div>
            </div>
        </div>
    </div>

</div>

{{-- ──────────────── Modale Reçu Réelle (Preuve photo) ──────────────── --}}
<div id="receipt-modal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-title" onclick="handleModalClick(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="modal-title">Aperçu de la preuve</div>
                <div class="modal-meta" id="modal-meta">—</div>
            </div>
            <button type="button" class="modal-close" onclick="closeReceiptModal()" aria-label="Fermer la modale">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-image-wrap">
            <div class="modal-placeholder">
                <img id="modal-img" src="" alt="Preuve de paiement" style="display:none;">
                <div id="modal-svg-placeholder">
                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p id="modal-filename">—</p>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:8px; margin-top:16px;">
            <button type="button" class="btn-reject" style="flex:1; justify-content:center;" onclick="closeReceiptModal()">Fermer</button>
            <a id="modal-download" href="#" download class="btn-approve" style="flex:1; text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:6px;">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Télécharger
            </a>
        </div>
    </div>
</div>



{{-- ──────────────── POP-UP AJOUT PARCELLE (Modale HTML/Tailwind) ──────────────── --}}
<div id="parcelleModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 hidden items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative text-gray-800">
        
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 font-bold text-xl transition">&times;</button>
        
        <h3 class="text-xl font-bold text-gray-900 mb-4">Ajouter une nouvelle parcelle</h3>
        
        <form id="addParcelleForm" action="{{ route('admin.parcelles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="modal-titre" class="block text-sm font-semibold text-gray-700 mb-1">Numéro / Titre</label>
                <input type="text" id="modal-titre" name="titre" placeholder="Ex: Parcelle #47 - Zone Espoir" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-blue-500 outline-none transition">
            </div>

            <div class="mb-3">
                <label for="modal-localisation" class="block text-sm font-semibold text-gray-700 mb-1">Localisation</label>
                <input type="text" id="modal-localisation" name="localisation" placeholder="Ex: Kinshasa, Maluku" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-blue-500 outline-none transition">
            </div>

            <div class="mb-3">
                <label for="modal-dimensions" class="block text-sm font-semibold text-gray-700 mb-1">Dimensions</label>
                <input type="text" id="modal-dimensions" name="dimensions" placeholder="Ex: 30m x 50m" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-blue-500 outline-none transition">
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5">
                <div>
                    <label for="modal-prix" class="block text-sm font-semibold text-gray-700 mb-1">Prix ($)</label>
                    <input type="number" id="modal-prix" name="prix" placeholder="5000" min="0" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mensualité ($)</label>
                    <input type="number" id="modal-mensualite" name="mensualite" placeholder="100" min="0" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-gray-900 focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Photo de la parcelle <span class="text-gray-400 font-normal">(Optionnel)</span>
                </label>
                
                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors bg-gray-50 flex flex-col items-center justify-center cursor-pointer group" id="drop-zone">
                    <input type="file" id="photo" name="photo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                    
                    <div id="preview-container" class="hidden mb-2 text-center">
                        <img id="image-preview" src="#" alt="Aperçu" class="max-h-32 rounded-lg mx-auto shadow-sm">
                    </div>

                    <svg id="upload-icon" class="w-10 h-10 text-gray-400 group-hover:text-blue-500 mb-2 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    
                    <p class="text-sm text-gray-600 font-medium text-center" id="file-name-preview">
                        Cliquez ou glissez une image ici
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition">Annuler</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div id="client-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div class="bg-[#111c44] border border-white/10 rounded-2xl p-6 w-full max-w-lg shadow-2xl">
        <h3 id="modal-title" class="text-xl font-bold text-white mb-4">Gérer le client</h3>
        
        <form id="client-form" action="" method="POST">
            @csrf
            <div id="method-field"></div> <div class="space-y-4">
                <input type="text" name="name" id="client-name" placeholder="Nom du client" class="w-full p-3 bg-slate-950 rounded-xl text-white">
                <input type="email" name="email" id="client-email" placeholder="Email" class="w-full p-3 bg-slate-950 rounded-xl text-white">
            </div>
            
            <div class="flex gap-4 mt-6">
                <button type="button" onclick="closeModal()" class="flex-1 py-3 bg-slate-800 text-white rounded-xl">Annuler</button>
                <button type="submit" class="flex-1 py-3 bg-emerald-500 text-slate-950 font-bold rounded-xl">Enregistrer</button>
            </div>
        </form>
    </div>
</div>



<script>
(function() {

    // ── 1. Filtrage texte ──
    window.filterTable = function(value) {
        const q = (value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.table-row-item');
        let visible = 0;
        rows.forEach(row => {
            const name  = (row.dataset.name || '').toLowerCase();
            const canal = (row.dataset.canal || '').toLowerCase();
            const show  = name.includes(q) || canal.includes(q);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        toggleEmptyState(visible);
        updateCount(visible);
    };

    // ── 2. Filtre canal ──
    let activeCanal = 'all';
    window.filterCanal = function(canal, btn) {
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        activeCanal = canal;
        applyFilters();
    };

    function applyFilters() {
        const q = (document.getElementById('admin-search').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.table-row-item');
        let visible = 0;
        rows.forEach(row => {
            const name     = (row.dataset.name  || '').toLowerCase();
            const canal    = (row.dataset.canal || '').toLowerCase();
            const matchQ   = name.includes(q) || canal.includes(q);
            const matchC   = (activeCanal === 'all') || (canal === activeCanal);
            const show     = matchQ && matchC;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        toggleEmptyState(visible);
        updateCount(visible);
    }

    function updateCount(n) {
        const el = document.getElementById('count-text');
        if (el) {
            el.textContent = n + ' ' + (n > 1 ? 'à vérifier' : 'à vérifier');
        }
    }

    function toggleEmptyState(visibleCount) {
        const state = document.getElementById('empty-state');
        if (state) {
            if (visibleCount === 0) state.classList.add('visible');
            else state.classList.remove('visible');
        }
    }

    // ── 3. Actions sur les formulaires (Approuver/Rejeter) ──
    window.handleFormAction = function(e, form) {
        e.preventDefault();
        const row = form.closest('.payment-row');
        if (!row) return false;

        row.classList.add('removing');
        
        setTimeout(() => {
            form.submit();
        }, 350);

        return true;
    };

    // ── 4. Modale d'aperçu de preuve/reçu ──
    window.openReceiptModal = function(client, parcelle, filename) {
        const modal = document.getElementById('receipt-modal');
        const mTitle = document.getElementById('modal-title');
        const mMeta = document.getElementById('modal-meta');
        const mImg = document.getElementById('modal-img');
        const mSvg = document.getElementById('modal-svg-placeholder');
        const mFile = document.getElementById('modal-filename');
        const mDl = document.getElementById('modal-download');

        if (!modal) return;

        mTitle.textContent = client;
        mMeta.textContent = parcelle ? 'Parcelle: ' + parcelle : '—';
        
        if (filename && (filename.endsWith('.jpg') || filename.endsWith('.jpeg') || filename.endsWith('.png') || filename.endsWith('.webp'))) {
            mImg.src = "/storage/" + filename;
            mImg.style.display = 'block';
            mSvg.style.display = 'none';
        } else {
            mImg.style.display = 'none';
            mSvg.style.display = 'block';
            mFile.textContent = filename || 'Aucun fichier';
        }

        mDl.href = filename ? "/storage/" + filename : "#";
        modal.classList.add('open');
    };

    window.closeReceiptModal = function() {
        const modal = document.getElementById('receipt-modal');
        if (modal) modal.classList.remove('open');
    };

    window.handleModalClick = function(e) {
        if (e.target.id === 'receipt-modal') {
            closeReceiptModal();
        }
    };

    // ── 5. Modale d'ajout de parcelle (Tailwind) ──
    window.openModal = function() {
        const modal = document.getElementById('parcelleModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    };

    window.closeModal = function() {
        const modal = document.getElementById('parcelleModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    // Preview de l'image pour l'ajout de parcelle
    document.addEventListener('DOMContentLoaded', () => {
        const photoInput = document.getElementById('photo');
        const previewContainer = document.getElementById('preview-container');
        const imagePreview = document.getElementById('image-preview');
        const uploadIcon = document.getElementById('upload-icon');
        const fileNamePreview = document.getElementById('file-name-preview');

        if (photoInput) {
            photoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    fileNamePreview.textContent = file.name;
                    reader.addEventListener('load', function() {
                        imagePreview.setAttribute('src', this.result);
                        previewContainer.classList.remove('hidden');
                        uploadIcon.classList.add('hidden');
                    });
                    reader.readAsDataURL(file);
                }
            });
        }
    });



})();
</script>

@endsection