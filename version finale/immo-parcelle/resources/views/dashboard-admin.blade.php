@extends('layouts.app')

@section('content')

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
    .admin-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
    @media (max-width: 900px) { .admin-kpi-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 540px)  { .admin-kpi-grid { grid-template-columns: 1fr; } }
    .akpi { background: var(--c-card); backdrop-filter: blur(12px); border: 1px solid var(--c-border); border-radius: 16px; padding: 20px 22px; transition: transform .25s, border-color .25s; }
    .akpi:hover { transform: translateY(-2px); border-color: var(--c-border-em); }
    .akpi.green { border-color: rgba(16,185,129,0.2); }
    .akpi.amber { border-color: rgba(245,158,11,0.15); }
    .akpi.blue  { border-color: rgba(99,102,241,0.2); }
    .akpi-label { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--c-muted); }
    .akpi.green .akpi-label { color:#10b981; }
    .akpi.amber .akpi-label { color:#d97706; }
    .akpi.blue  .akpi-label { color:#818cf8; }
    .akpi-value { font-size:26px; font-weight:800; color:#f1f5f9; margin-top:10px; letter-spacing:-1px; line-height:1; }
    .akpi.green .akpi-value { color:#10b981; }
    .akpi.amber .akpi-value { color:#d97706; }
    .akpi.blue  .akpi-value { color:#818cf8; }
    .akpi-bar  { margin-top:14px; background:rgba(0,0,0,0.3); border-radius:99px; height:4px; overflow:hidden; }
    .akpi-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,#10b981,#34d399); animation:bar-grow 1.2s ease-out .4s both; }
    @keyframes bar-grow { from { width:0 !important; } }

    /* Tabs */
    .tab-nav { display:flex; gap:4px; background:rgba(0,0,0,0.2); border-radius:12px; padding:4px; margin-bottom:20px; flex-wrap:wrap; }
    .tab-btn { flex:1; min-width:120px; padding:9px 16px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none; border-radius:9px; cursor:pointer; background:transparent; color:var(--c-muted); transition:all .2s; }
    .tab-btn.active { background:var(--c-card); color:var(--c-text); box-shadow:0 2px 8px rgba(0,0,0,0.3); }

    /* Table card */
    .table-card { background:var(--c-card); backdrop-filter:blur(12px); border:1px solid var(--c-border); border-radius:18px; overflow:hidden; }
    .table-card-header { padding:20px 24px 16px; border-bottom:1px solid var(--c-border); display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; }
    .table-card-title { font-size:15px; font-weight:700; color:var(--c-text); }
    .table-card-sub   { font-size:12px; color:var(--c-muted); margin-top:3px; }
    .count-badge { display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:#d97706; background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.25); padding:5px 12px; border-radius:8px; }
    .count-badge .dot { width:6px; height:6px; border-radius:50%; background:#f59e0b; }

    /* Filter bar */
    .filter-bar { padding:12px 24px; border-bottom:1px solid var(--c-border); display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .filter-label { font-size:11px; font-weight:700; color:var(--c-muted); text-transform:uppercase; letter-spacing:.07em; }
    .filter-pill { font-size:12px; font-weight:600; padding:5px 13px; border-radius:99px; border:1px solid var(--c-border); background:transparent; color:var(--c-sub); cursor:pointer; transition:all .15s; }
    .filter-pill:hover { background:rgba(255,255,255,0.04); color:var(--c-text); }
    .filter-pill.active { background:rgba(16,185,129,0.1); border-color:rgba(16,185,129,0.3); color:#10b981; }

    /* Table */
    .payments-table { width:100%; border-collapse:collapse; font-size:13px; }
    .payments-table thead th { padding:12px 20px; text-align:left; font-size:11px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:var(--c-muted); background:rgba(0,0,0,0.1); white-space:nowrap; }
    .payments-table tbody tr { border-top:1px solid var(--c-border); transition:background .15s; }
    .payments-table tbody tr:hover { background:rgba(255,255,255,0.02); }
    .payments-table td { padding:14px 20px; vertical-align:middle; color:var(--c-sub); }
    .payments-table td:first-child { color:var(--c-text); }

    .client-cell { display:flex; align-items:center; gap:12px; }
    .client-avatar { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0; }
    .client-name  { font-size:13px; font-weight:700; color:var(--c-text); }
    .client-ref   { font-size:11px; color:var(--c-muted); font-family:monospace; margin-top:2px; }
    .client-date  { font-size:10px; color:#334155; margin-top:3px; }

    .canal-badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; padding:4px 10px; border-radius:8px; white-space:nowrap; }
    .canal-badge .cdot { width:6px; height:6px; border-radius:50%; }
    .canal-orange { background:rgba(249,115,22,0.08); border:1px solid rgba(249,115,22,0.2); color:#fb923c; }
    .canal-orange .cdot { background:#f97316; }
    .canal-mpesa  { background:rgba(37,99,235,0.08);  border:1px solid rgba(37,99,235,0.2);  color:#60a5fa; }
    .canal-mpesa  .cdot { background:#2563eb; }
    .canal-airtel { background:rgba(244,63,94,0.08);  border:1px solid rgba(244,63,94,0.2);  color:#fb7185; }
    .canal-airtel .cdot { background:#f43f5e; }

    .amount-cell { font-family:monospace; font-size:14px; font-weight:800; color:var(--c-text); }
    .receipt-btn { display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:600; color:#7c3aed; background:rgba(124,58,237,0.06); border:1px solid rgba(124,58,237,0.15); padding:5px 11px; border-radius:8px; cursor:pointer; transition:all .15s; }
    .receipt-btn:hover { background:rgba(124,58,237,0.12); color:#a78bfa; }
    .action-cell { display:flex; align-items:center; justify-content:flex-end; gap:6px; }
    .btn-reject  { font-size:11px; font-weight:700; padding:6px 12px; border-radius:8px; border:1px solid rgba(244,63,94,0.25); background:rgba(244,63,94,0.06); color:#fb7185; cursor:pointer; transition:all .15s; }
    .btn-reject:hover  { background:rgba(244,63,94,0.12); }
    .btn-approve { font-size:11px; font-weight:700; padding:6px 13px; border-radius:8px; border:none; background:linear-gradient(135deg,#10b981,#059669); color:#fff; cursor:pointer; transition:all .15s; }
    .btn-approve:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(16,185,129,0.3); }

    .empty-state { padding:60px 20px; text-align:center; display:none; }
    .empty-state.visible { display:block; }
    .empty-icon  { font-size:36px; color:#1e293b; margin-bottom:12px; }
    .empty-title { font-size:15px; font-weight:700; color:#334155; }
    .empty-sub   { font-size:12px; color:var(--c-muted); margin-top:4px; }

    /* Modales */
    .modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,0.75); backdrop-filter:blur(6px); z-index:200; display:flex; align-items:center; justify-content:center; opacity:0; pointer-events:none; transition:opacity .25s; padding:20px; }
    .modal-backdrop.open { opacity:1; pointer-events:all; }
    .modal-box { background:#0d1424; border:1px solid rgba(255,255,255,0.1); border-radius:20px; padding:24px; width:100%; max-width:480px; transform:translateY(16px) scale(.98); transition:transform .25s; position:relative; }
    .modal-backdrop.open .modal-box { transform:translateY(0) scale(1); }
    .modal-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px; }
    .modal-title  { font-size:15px; font-weight:700; color:var(--c-text); }
    .modal-meta   { font-size:12px; color:var(--c-muted); margin-top:3px; }
    .modal-close  { background:rgba(255,255,255,0.05); border:1px solid var(--c-border); color:var(--c-muted); width:30px; height:30px; border-radius:8px; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center; transition:all .15s; flex-shrink:0; }
    .modal-close:hover { background:rgba(255,255,255,0.1); color:var(--c-text); }
    .modal-image-wrap { background:rgba(0,0,0,0.3); border-radius:12px; height:200px; border:1px solid var(--c-border); display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .modal-image-wrap img { width:100%; height:100%; object-fit:cover; }

    /* Page header */
    .page-header { background:linear-gradient(120deg,rgba(13,20,44,0.7) 0%,rgba(124,58,237,0.03) 100%); border:1px solid var(--c-border); border-radius:20px; padding:22px 28px; display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:24px; flex-wrap:wrap; }
    .page-title  { font-size:22px; font-weight:800; color:#f1f5f9; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .page-title-badge { font-size:11px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#a78bfa; background:rgba(124,58,237,0.1); border:1px solid rgba(124,58,237,0.2); padding:3px 10px; border-radius:6px; font-family:monospace; }
    .page-sub    { font-size:13px; color:var(--c-muted); margin-top:5px; }
    .search-wrap { position:relative; display:flex; align-items:center; }
    .search-icon { position:absolute; left:12px; color:var(--c-muted); pointer-events:none; }
    .search-input { background:rgba(0,0,0,0.3); border:1px solid var(--c-border); color:var(--c-text); font-size:13px; border-radius:12px; padding:9px 14px 9px 36px; width:220px; outline:none; transition:all .2s; }
    .search-input::placeholder { color:#334155; }
    .search-input:focus { border-color:rgba(16,185,129,0.4); }

    /* Progress bar */
    .prog-bar { height:6px; border-radius:99px; background:rgba(255,255,255,0.06); overflow:hidden; }
    .prog-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,#10b981,#34d399); transition:width .8s ease; }

    /* Clients cards */
    .client-row-card { background:rgba(0,0,0,0.15); border:1px solid var(--c-border); border-radius:14px; padding:16px 20px; display:flex; align-items:center; gap:16px; flex-wrap:wrap; transition:border-color .2s; }
    .client-row-card:hover { border-color:var(--c-border-em); }

    /* Confirmation popup */
    #confirm-modal .modal-box { max-width:360px; }
    .confirm-msg { font-size:14px; color:var(--c-sub); margin-bottom:20px; line-height:1.5; }

    /* Mois select */
    .mois-select { background:rgba(0,0,0,0.3); border:1px solid var(--c-border); color:var(--c-text); border-radius:10px; padding:8px 14px; font-size:12px; font-weight:600; outline:none; cursor:pointer; }

    .payment-row { transition:opacity .35s, transform .35s; }
    .payment-row.removing { opacity:0; transform:translateX(40px); }

    /* Approve form in modal */
    .approve-drop { border:2px dashed rgba(16,185,129,0.3); border-radius:12px; padding:16px; text-align:center; cursor:pointer; transition:all .2s; }
    .approve-drop:hover { border-color:rgba(16,185,129,0.6); background:rgba(16,185,129,0.04); }
</style>

{{-- ──────────────── Flash messages ──────────────── --}}
@if(session('success'))
<div style="background:rgba(16,185,129,0.15); border:1px solid #10b981; color:#10b981; padding:12px 20px; border-radius:10px; margin-bottom:20px;">
    ✓ {{ session('success') }}
</div>
@endif
@if(session('error') || $errors->any())
<div style="background:rgba(244,63,94,0.15); border:1px solid #f43f5e; color:#fb7185; padding:12px 20px; border-radius:10px; margin-bottom:20px;">
    ✗ {{ session('error') ?? $errors->first() }}
</div>
@endif

{{-- ──────────────── En-tête ──────────────── --}}
<div class="page-header">
    <a href="{{ route('pdf.clients') }}" class="btn btn-primary">
        Télécharger la liste des clients et parcelles en PDF
    </a>
    <div>
        <div class="page-title">
            Tableau de Bord Admin
            <span class="page-title-badge">Manager</span>
            
        </div>
        <div class="page-sub">Recouvrements · Clients · Parcelles · Validation des paiements</div>
    </div>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        {{-- Bouton PDF ici --}}
        
        {{-- Filtre par mois --}}
        <form method="GET" action="{{ route('dashboard-admin') }}" style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:11px; font-weight:700; color:var(--c-muted); text-transform:uppercase;">Mois :</label>
            <select name="mois" class="mois-select" onchange="this.form.submit()">
                <option value="">Tous</option>
                @foreach($moisDisponibles as $m)
                    <option value="{{ $m }}" {{ $moisFiltre === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </form>

        <button onclick="openParcelleModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
            + Parcelle
        </button>
        <button onclick="openClientModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
            + Client
        </button>

        <div class="search-wrap">
            <div class="search-icon">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" id="admin-search" class="search-input" placeholder="Rechercher..." oninput="applyFilters()" aria-label="Rechercher">
        </div>
    </div>
</div>



{{-- ──────────────── KPI ──────────────── --}}
<div class="admin-kpi-grid">
    <div class="akpi">
        <div class="akpi-label">Total Attendu</div>
        <div class="akpi-value">{{ number_format($total_attendu_global, 0, ',', ' ') }} $</div>
    </div>
    <div class="akpi green">
        <div class="akpi-label">Total Encaissé</div>
        <div class="akpi-value">{{ number_format($total_encaisse_global, 0, ',', ' ') }} $</div>
        <div class="akpi-bar"><div class="akpi-fill" style="width:{{ $pct_encaisse }}%"></div></div>
    </div>
    <div class="akpi amber">
        <div class="akpi-label">En Attente</div>
        <div class="akpi-value">{{ number_format($total_attente_global, 0, ',', ' ') }} $</div>
    </div>
    <div class="akpi blue">
        <div class="akpi-label">Clients Actifs</div>
        <div class="akpi-value">{{ $nb_clients_actifs }}</div>
    </div>
</div>

{{-- ──────────────── TABS ──────────────── --}}
<div class="tab-nav" role="tablist">
    <button class="tab-btn active" onclick="switchTab('tab-paiements', this)" role="tab">
        Paiements <span id="pending-count-badge" style="background:rgba(245,158,11,0.2);color:#f59e0b;border-radius:99px;padding:1px 8px;font-size:10px;margin-left:4px;">{{ $paiements->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('tab-clients', this)" role="tab">Clients ({{ $nb_clients_actifs }})</button>
    <button class="tab-btn" onclick="switchTab('tab-suivi', this)" role="tab">Suivi Parcelles</button>
    <button class="tab-btn" onclick="switchTab('tab-historique', this)" role="tab">Historique</button>
    <a href="{{ route('admin.export.pdf', ['type' => 'paiements']) }}" 
   class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
    Exporter PDF
</a>
</div>

{{-- ══════════════════════════════════════════════
     TAB 1 : PAIEMENTS EN ATTENTE
═══════════════════════════════════════════════--}}
<div id="tab-paiements" class="tab-content">
    <div class="table-card">
        <div class="table-card-header">
            <div>
                <div class="table-card-title">Preuves de paiement soumises</div>
                <div class="table-card-sub">Vérifiez chaque reçu avant de valider. L'approbation nécessite une photo de confirmation.</div>
            </div>
            <span class="count-badge"><span class="dot"></span><span id="count-text">{{ $paiements->count() }} à vérifier</span></span>
        </div>

        {{-- Filtres canal --}}
        <div class="filter-bar">
            <span class="filter-label">Canal :</span>
            <button class="filter-pill active" onclick="filterCanal('all', this)">Tous</button>
            <button class="filter-pill" onclick="filterCanal('orange', this)">Orange Money</button>
            <button class="filter-pill" onclick="filterCanal('mpesa', this)">M-Pesa</button>
            <button class="filter-pill" onclick="filterCanal('airtel', this)">Airtel Money</button>
            <button class="filter-pill" onclick="filterCanal('virement bancaire', this)">Virement bancaire</button>
        </div>

        <div style="overflow-x:auto;">
            <table class="payments-table" id="payments-table">
                <thead>
                    <tr>
                        <th>Client / Parcelle</th>
                        <th>Canal</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Preuve Client</th>
                        <th style="text-align:right;">Actions</th>
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
                                'orange','orange money' => 'orange',
                                'mpesa','m-pesa'        => 'mpesa',
                                default                 => 'airtel',
                            };
                            $avatarColors = ['orange'=>'background:rgba(249,115,22,0.12);color:#fb923c;','mpesa'=>'background:rgba(37,99,235,0.12);color:#60a5fa;','airtel'=>'background:rgba(244,63,94,0.12);color:#fb7185;'];
                        @endphp
                        <tr class="payment-row table-row-item"
                            data-id="{{ $p->id }}"
                            data-name="{{ strtolower($nomComplet) }}"
                            data-canal="{{ $canalSlug }}"
                            data-mois="{{ strtolower($p->mois_concerne) }}">
                            <td>
                                <div class="client-cell">
                                    <div class="client-avatar" style="{{ $avatarColors[$canalSlug] ?? '' }}">{{ $initiales }}</div>
                                    <div>
                                        <div class="client-name">{{ $nomComplet }}</div>
                                        <div class="client-ref">{{ $p->parcelle->titre ?? '—' }} · {{ $p->parcelle->localisation ?? '' }}</div>
                                        <div class="client-date">Mois : {{ $p->mois_concerne }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="canal-badge canal-{{ $canalSlug }}">
                                    <span class="cdot"></span>{{ ucfirst($canalSlug) }}
                                </span>
                            </td>
                            <td>
                                <span class="amount-cell">{{ number_format($p->montant_paye, 2, ',', ' ') }} $</span>
                                @if($p->penalite_appliquee > 0)
                                    <div style="font-size:10px;color:var(--rose);">+{{ $p->penalite_appliquee }} $ Pén.</div>
                                @endif
                            </td>
                            <td style="font-size:12px;color:var(--c-muted);white-space:nowrap;">
                                {{ $p->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <button type="button" class="receipt-btn"
                                    onclick="openReceiptModal('{{ addslashes($nomComplet) }}', '{{ $p->parcelle->titre ?? '' }}', '{{ $p->preuve_photo }}')">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Voir preuve
                                </button>
                            </td>
                            <td>
                                <div class="action-cell">
                                    {{-- Rejeter --}}
                                    <button type="button" class="btn-reject"
                                        onclick="openConfirmAction('{{ route('admin.paiements.action', [$p->id, 'refuser']) }}', 'refuser', '{{ addslashes($nomComplet) }}')">
                                        Rejeter
                                    </button>
                                    {{-- Approuver — ouvre modal avec upload photo --}}
                                    <button type="button" class="btn-approve"
                                        onclick="openApproveModal({{ $p->id }}, '{{ addslashes($nomComplet) }}', '{{ $p->parcelle->titre ?? '' }}')">
                                        Approuver
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="padding:40px;text-align:center;color:var(--c-muted);font-size:13px;">Aucun paiement en attente.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     TAB 2 : CLIENTS
═══════════════════════════════════════════════--}}
<div id="tab-clients" class="tab-content" style="display:none;">
    <div class="table-card">
        <div class="table-card-header">
            <div>
                <div class="table-card-title">Gestion des Clients</div>
                <div class="table-card-sub">Suivez l'évolution de chaque client, ses parcelles et son taux de remboursement.</div>
            </div>
        </div>

        <div style="padding:16px 20px; display:flex; flex-direction:column; gap:10px;" id="clients-list">
            @forelse($clients as $client)
                @php
                    $initC = strtoupper(substr($client->name, 0, 1));
                    $badgeColor = $client->pct_progression >= 80 ? '#10b981' : ($client->pct_progression >= 40 ? '#f59e0b' : '#f43f5e');
                @endphp
                <div class="client-row-card client-item" data-name="{{ strtolower($client->name) }}">
                    <div style="width:42px;height:42px;border-radius:12px;background:rgba(99,102,241,0.15);color:#818cf8;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;flex-shrink:0;">
                        {{ $initC }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:14px;font-weight:700;color:var(--c-text);">{{ $client->name }}</div>
                        <div style="font-size:11px;color:var(--c-muted);">{{ $client->email }}</div>
                        <div style="font-size:11px;color:var(--c-muted);margin-top:2px;">
                            {{ $client->parcelles->count() }} parcelle(s) · {{ $client->nb_paiements }} paiement(s) validé(s)
                            @if($client->nb_retards > 0)
                                · <span style="color:var(--rose);">{{ $client->nb_retards }} refus</span>
                            @endif
                        </div>
                    </div>
                    <div style="min-width:140px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <span style="font-size:10px;color:var(--c-muted);">Progression</span>
                            <span style="font-size:11px;font-weight:700;color:{{ $badgeColor }};">{{ $client->pct_progression }}%</span>
                        </div>
                        <div class="prog-bar"><div class="prog-fill" style="width:{{ $client->pct_progression }}%;background:{{ $badgeColor }};"></div></div>
                        <div style="font-size:10px;color:var(--c-muted);margin-top:3px;">{{ number_format($client->total_paye, 0, ',', ' ') }} $ / {{ number_format($client->total_du, 0, ',', ' ') }} $</div>
                    </div>
                    <div style="display:flex;gap:6px;flex-shrink:0;">
                        <a href="{{ route('clients.show', $client->id) }}" style="font-size:11px;font-weight:600;padding:6px 12px;border-radius:8px;background:rgba(99,102,241,0.1);color:#818cf8;border:1px solid rgba(99,102,241,0.2);text-decoration:none;">
                            Détails
                        </a>
                        <button type="button"
                            onclick="openEditClientModal({{ $client->id }}, '{{ addslashes($client->name) }}', '{{ $client->email }}')"
                            style="font-size:11px;font-weight:600;padding:6px 12px;border-radius:8px;background:rgba(245,158,11,0.08);color:#f59e0b;border:1px solid rgba(245,158,11,0.2);cursor:pointer;">
                            Modifier
                        </button>
                        <button type="button"
                            onclick="openConfirmDelete({{ $client->id }}, '{{ addslashes($client->name) }}')"
                            style="font-size:11px;font-weight:600;padding:6px 12px;border-radius:8px;background:rgba(244,63,94,0.06);color:#fb7185;border:1px solid rgba(244,63,94,0.2);cursor:pointer;">
                            Supprimer
                        </button>
                    </div>
                </div>
            @empty
                <div style="padding:40px;text-align:center;color:var(--c-muted);">Aucun client enregistré.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     TAB 3 : SUIVI PARCELLES
═══════════════════════════════════════════════--}}
<div id="tab-suivi" class="tab-content" style="display:none;">
    <div class="table-card">
        <div class="table-card-header">
            <div>
                <div class="table-card-title">Suivi des Parcelles par Client</div>
                <div class="table-card-sub">Progression du remboursement de chaque parcelle attribuée.</div>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <span style="font-size:12px;color:var(--c-muted);">{{ $nb_parcelles_libres }} / {{ $nb_parcelles_total }} disponibles</span>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Parcelle</th>
                        <th>Localisation</th>
                        <th>Dimensions</th>
                        <th>Prix Total</th>
                        <th>Payé</th>
                        <th>Progression</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suiviGlobal as $parcelle)
                    @php
                        // 1. Calcul du pourcentage
                        $pct = $parcelle->prix_total > 0 ? round(($parcelle->somme_payee / $parcelle->prix_total) * 100) : 0;
                        $pctColor = $pct >= 80 ? '#10b981' : ($pct >= 40 ? '#f59e0b' : '#f43f5e');

                        // 2. Vérification sécurisée de l'utilisateur et du profil
                        if ($parcelle->user && $parcelle->user->profil) {
                            $nomClient = $parcelle->user->profil->prenom . ' ' . $parcelle->user->profil->nom;
                        } else {
                            $nomClient = $parcelle->user ? $parcelle->user->name : 'Non attribué';
                        }
                    @endphp
                        <tr>
                            <td>{{ $nomClient }}</td>
                            <td style="font-weight:700;color:var(--c-text);">{{ $parcelle->titre }}</td>
                            <td>{{ $parcelle->localisation }}</td>
                            <td>{{ $parcelle->dimensions ?? '—' }}</td>
                            <td><span class="amount-cell">{{ number_format($parcelle->prix_total, 0, ',', ' ') }} $</span></td>
                            <td style="color:#10b981;font-weight:700;">{{ number_format($parcelle->somme_payee, 0, ',', ' ') }} $</td>
                            <td style="min-width:120px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="prog-bar" style="flex:1;"><div class="prog-fill" style="width:{{ $pct }}%;background:{{ $pctColor }};"></div></div>
                                    <span style="font-size:11px;font-weight:700;color:{{ $pctColor }};white-space:nowrap;">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px;
                                    background:{{ $parcelle->statut === 'reservee' ? 'rgba(245,158,11,0.1)' : 'rgba(16,185,129,0.1)' }};
                                    color:{{ $parcelle->statut === 'reservee' ? '#f59e0b' : '#10b981' }};">
                                    {{ ucfirst($parcelle->statut) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="padding:40px;text-align:center;color:var(--c-muted);">Aucune parcelle attribuée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     TAB 4 : HISTORIQUE COMPLET
═══════════════════════════════════════════════--}}
<div id="tab-historique" class="tab-content" style="display:none;">
    <div class="table-card">
        <div class="table-card-header">
            <div>
                <div class="table-card-title">Historique des Transactions</div>
                <div class="table-card-sub">Tous les paiements (validés, rejetés, en attente){{ $moisFiltre ? ' · Filtre: '.$moisFiltre : '' }}.</div>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Parcelle</th>
                        <th>Mois</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Preuve Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historiqueTransactions as $h)
                        @php
                            $nom = $h->user->profil
                                ? ($h->user->profil->prenom . ' ' . $h->user->profil->nom)
                                : ($h->user->name ?? '—');
                            $statutColor = match($h->statut) {
                                'valide'     => ['bg'=>'rgba(16,185,129,0.1)','color'=>'#10b981','label'=>'Validé'],
                                'rejete'     => ['bg'=>'rgba(244,63,94,0.1)', 'color'=>'#fb7185','label'=>'Rejeté'],
                                default      => ['bg'=>'rgba(245,158,11,0.1)','color'=>'#f59e0b','label'=>'En attente'],
                            };
                        @endphp
                        <tr>
                            <td>{{ $nom }}</td>
                            <td>{{ $h->parcelle->titre ?? '—' }}</td>
                            <td>{{ $h->mois_concerne }}</td>
                            <td><span class="amount-cell">{{ number_format($h->montant_paye, 0, ',', ' ') }} $</span></td>
                            <td>
                                <span style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:6px;background:{{ $statutColor['bg'] }};color:{{ $statutColor['color'] }};">
                                    {{ $statutColor['label'] }}
                                </span>
                            </td>
                            <td style="font-size:12px;color:var(--c-muted);">{{ $h->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($h->preuve_admin)
                                    <button type="button" class="receipt-btn"
                                        onclick="openReceiptModal('Confirmation Admin', '{{ $h->parcelle->titre ?? '' }}', '{{ $h->preuve_admin }}')">
                                        Voir reçu admin
                                    </button>
                                @else
                                    <span style="font-size:11px;color:#334155;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="padding:40px;text-align:center;color:var(--c-muted);">Aucune transaction.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>




{{-- ══════════════════════════════════════════════
     MODALES
═══════════════════════════════════════════════--}}

{{-- Modale Aperçu Preuve --}}
<div id="receipt-modal" class="modal-backdrop" onclick="if(event.target===this)closeReceiptModal()">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="rm-title">Aperçu de la preuve</div>
                <div class="modal-meta" id="rm-meta">—</div>
            </div>
            <button class="modal-close" onclick="closeReceiptModal()">✕</button>
        </div>
        <div class="modal-image-wrap" id="rm-img-wrap">
            <img id="rm-img" src="" alt="Preuve" style="display:none;">
            <div id="rm-placeholder" style="text-align:center;color:var(--c-muted);font-size:13px;">Aucun fichier</div>
        </div>
        <div style="display:flex;gap:8px;margin-top:16px;">
            <button class="btn-reject" style="flex:1;" onclick="closeReceiptModal()">Fermer</button>
            <a id="rm-dl" href="#" download class="btn-approve" style="flex:1;text-align:center;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px;">
                Télécharger
            </a>
        </div>
    </div>
</div>

{{-- Modale Approbation avec photo obligatoire --}}
<div id="approve-modal" class="modal-backdrop" onclick="if(event.target===this)closeApproveModal()">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">Confirmer le paiement</div>
                <div class="modal-meta" id="approve-meta">—</div>
            </div>
            <button class="modal-close" onclick="closeApproveModal()">✕</button>
        </div>
        <form id="approve-form" method="POST" enctype="multipart/form-data">
            @csrf
            <p style="font-size:13px;color:var(--c-sub);margin-bottom:14px;line-height:1.5;">
                Joignez votre <strong style="color:var(--c-text);">photo de confirmation</strong> (reçu de votre côté, capture d'écran, etc.) pour valider ce paiement.
            </p>
            <div class="approve-drop" onclick="document.getElementById('admin-proof-input').click()">
                <input type="file" id="admin-proof-input" name="preuve_admin" accept="image/*" style="display:none;" onchange="previewAdminProof(this)" required>
                <div id="approve-preview-wrap" style="display:none;margin-bottom:8px;">
                    <img id="approve-preview-img" src="" alt="Aperçu" style="max-height:120px;border-radius:8px;margin:0 auto;display:block;">
                </div>
                <div id="approve-drop-text">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="1.5" style="margin:0 auto 6px;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p style="font-size:12px;color:var(--c-muted);">Cliquez pour joindre votre photo de confirmation</p>
                </div>
            </div>
            <div id="approve-file-name" style="font-size:11px;color:#10b981;margin-top:6px;display:none;"></div>
            <div style="display:flex;gap:8px;margin-top:16px;">
                <button type="button" class="btn-reject" style="flex:1;" onclick="closeApproveModal()">Annuler</button>
                <button type="submit" class="btn-approve" style="flex:1;">✓ Valider le paiement</button>
            </div>
        </form>
    </div>
</div>

{{-- Modale Confirmation (Rejeter / Supprimer client) --}}
<div id="confirm-modal" class="modal-backdrop" onclick="if(event.target===this)closeConfirmModal()">
    <div class="modal-box" id="confirm-modal" style="max-width:360px;">
        <div class="modal-header">
            <div class="modal-title" id="confirm-title">Confirmer l'action</div>
            <button class="modal-close" onclick="closeConfirmModal()">✕</button>
        </div>
        <p class="confirm-msg" id="confirm-msg">Êtes-vous sûr ?</p>
        <form id="confirm-form" method="POST">
            @csrf
            <div id="confirm-method-field"></div>
            <div style="display:flex;gap:8px;">
                <button type="button" class="btn-reject" style="flex:1;padding:10px;" onclick="closeConfirmModal()">Annuler</button>
                <button type="submit" id="confirm-submit-btn" class="btn-approve" style="flex:1;padding:10px;">Confirmer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modale Ajout Parcelle --}}
<div id="parcelle-modal" class="fixed inset-0 bg-gray-900/70 hidden items-center justify-center z-50" style="backdrop-filter:blur(4px);">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative text-gray-800">
        <button onclick="closeParcelleModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 font-bold text-xl">&times;</button>
        <h3 class="text-xl font-bold text-gray-900 mb-4">Ajouter une parcelle</h3>
        <form action="{{ route('admin.parcelles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Titre / Numéro</label>
                <input type="text" name="titre" placeholder="Ex: Parcelle #47 — Zone Espoir" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Localisation</label>
                <input type="text" name="localisation" placeholder="Ex: Kinshasa, Maluku" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Dimensions</label>
                <input type="text" name="dimensions" value="{{ old('dimensions') }}" placeholder="Ex: 30m × 50m" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Prix Total ($)</label>
                    <input type="number" name="prix_total" min="0" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mensualité ($)</label>
                    <input type="number" name="mensualite" min="0" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-50 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Photo (optionnel)</label>
                <input type="file" name="photo" accept="image/*" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-gray-50">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeParcelleModal()" class="px-4 py-2 text-sm rounded-lg border text-gray-600 hover:bg-gray-50">Annuler</button>
                <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<div id="client-modal" class="modal-backdrop" style="display:none;" onclick="if(event.target===this)closeClientModal()">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="client-modal-title">Ajouter un client</div>
            <button class="modal-close" onclick="closeClientModal()">✕</button>
        </div>
        <form id="client-form" method="POST" action="{{ route('clients.store') }}">
            @csrf
            <div id="client-method-field"></div>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div>
                    <label>Nom complet</label>
                    <input type="text" name="name" id="client-name-input" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" id="client-email-input" required>
                </div>
                <div id="client-password-field">
                    <label>Mot de passe</label>
                    <input type="password" name="password" id="client-password-input">
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:20px;">
                <button type="button" onclick="closeClientModal()" class="btn-reject">Annuler</button>
                <button type="submit" class="btn-approve" id="client-submit-btn">Ajouter</button>
            </div>
        </form>
    </div>

</div>
<a href="{{ route('pdf.clients') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition">
    📄 Télécharger PDF
</a>



<script>
// ─── TABS ───────────────────────────────────────────────
function switchTab(id, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(id).style.display = '';
    btn.classList.add('active');
}

// ─── FILTRES (recherche + canal) ───────────────────────
let activeCanal = 'all';

window.filterCanal = function(canal, btn) {
    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    activeCanal = canal;
    applyFilters();
};

window.applyFilters = function() {
    const q = (document.getElementById('admin-search').value || '').toLowerCase().trim();

    // Lignes paiements
    const rows = document.querySelectorAll('#payments-tbody .table-row-item');
    let visible = 0;
    rows.forEach(row => {
        const name  = (row.dataset.name  || '').toLowerCase();
        const canal = (row.dataset.canal || '').toLowerCase();
        const matchQ = name.includes(q) || canal.includes(q);
        const matchC = (activeCanal === 'all') || (canal === activeCanal);
        const show = matchQ && matchC;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const ct = document.getElementById('count-text');
    if (ct) ct.textContent = visible + ' à vérifier';

    // Lignes clients
    document.querySelectorAll('.client-item').forEach(item => {
        const name = (item.dataset.name || '').toLowerCase();
        item.style.display = name.includes(q) ? '' : 'none';
    });
};

// ─── MODALE APERÇU PREUVE ───────────────────────────────
window.openReceiptModal = function(client, parcelle, filename) {
    const modal = document.getElementById('receipt-modal');
    document.getElementById('rm-title').textContent = client;
    document.getElementById('rm-meta').textContent = parcelle ? 'Parcelle : ' + parcelle : '—';
    const img = document.getElementById('rm-img');
    const ph  = document.getElementById('rm-placeholder');
    if (filename && /\.(jpg|jpeg|png|webp)$/i.test(filename)) {
        img.src = '/storage/' + filename;
        img.style.display = 'block';
        ph.style.display = 'none';
    } else {
        img.style.display = 'none';
        ph.style.display = 'block';
        ph.textContent = filename || 'Aucun fichier';
    }
    document.getElementById('rm-dl').href = filename ? '/storage/' + filename : '#';
    modal.classList.add('open');
};
window.closeReceiptModal = function() {
    document.getElementById('receipt-modal').classList.remove('open');
};

// ─── MODALE APPROBATION AVEC PHOTO ──────────────────────
window.openApproveModal = function(id, client, parcelle) {
    const url = '/admin/paiements/' + id + '/approuver';
    document.getElementById('approve-form').action = url;
    document.getElementById('approve-meta').textContent = client + ' · ' + parcelle;
    // Reset
    document.getElementById('admin-proof-input').value = '';
    document.getElementById('approve-preview-wrap').style.display = 'none';
    document.getElementById('approve-drop-text').style.display = '';
    document.getElementById('approve-file-name').style.display = 'none';
    document.getElementById('approve-modal').classList.add('open');
};
window.closeApproveModal = function() {
    document.getElementById('approve-modal').classList.remove('open');
};
window.previewAdminProof = function(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('approve-preview-img').src = e.target.result;
        document.getElementById('approve-preview-wrap').style.display = '';
        document.getElementById('approve-drop-text').style.display = 'none';
        document.getElementById('approve-file-name').textContent = '✓ ' + file.name;
        document.getElementById('approve-file-name').style.display = '';
    };
    reader.readAsDataURL(file);
};

// ─── MODALE CONFIRMATION GÉNÉRIQUE ──────────────────────
window.openConfirmAction = function(url, action, label) {
    const modal  = document.getElementById('confirm-modal');
    const form   = document.getElementById('confirm-form');
    const title  = document.getElementById('confirm-title');
    const msg    = document.getElementById('confirm-msg');
    const mfield = document.getElementById('confirm-method-field');
    const sbtn   = document.getElementById('confirm-submit-btn');

    form.action = url;
    mfield.innerHTML = '';

    if (action === 'refuser') {
        title.textContent = 'Rejeter ce paiement ?';
        msg.textContent   = 'Vous êtes sur le point de rejeter le paiement de ' + label + '. Cette action peut être révisée plus tard.';
        sbtn.textContent  = 'Rejeter';
        sbtn.className    = 'btn-reject';
        sbtn.style.flex   = '1';
        sbtn.style.padding = '10px';
    } else if (action === 'delete_client') {
        title.textContent = 'Supprimer ce client ?';
        msg.textContent   = 'Attention : supprimer ' + label + ' libérera toutes ses parcelles. Cette action est irréversible.';
        sbtn.textContent  = 'Supprimer définitivement';
        sbtn.className    = 'btn-reject';
        sbtn.style.flex   = '1';
        sbtn.style.padding = '10px';
        mfield.innerHTML  = '<input type="hidden" name="_method" value="DELETE">';
    }

    modal.classList.add('open');
};
window.closeConfirmModal = function() {
    document.getElementById('confirm-modal').classList.remove('open');
};

// ─── MODALE CLIENT (Ajout / Modif) ──────────────────────
window.openClientModal = function() {
    const modal = document.getElementById('client-modal');
    document.getElementById('client-modal-title').textContent = 'Ajouter un client';
    document.getElementById('client-form').action = '{{ route("clients.store") }}';
    document.getElementById('client-method-field').innerHTML = '';
    document.getElementById('client-name-input').value  = '';
    document.getElementById('client-email-input').value = '';
    document.getElementById('client-password-field').style.display = '';
    const pwInput = document.getElementById('client-password-input');
    if (pwInput) pwInput.required = true;
    document.getElementById('client-submit-btn').textContent = 'Ajouter';
    modal.classList.add('open');
};
window.openEditClientModal = function(id, name, email) {
    const modal = document.getElementById('client-modal');
    document.getElementById('client-modal-title').textContent = 'Modifier le client';
    document.getElementById('client-form').action = '/admin/clients/' + id;
    document.getElementById('client-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('client-name-input').value  = name;
    document.getElementById('client-email-input').value = email;
    document.getElementById('client-password-field').style.display = 'none';
    const pwInput = document.getElementById('client-password-input');
    if (pwInput) pwInput.required = false;
    document.getElementById('client-submit-btn').textContent = 'Enregistrer';
    modal.classList.add('open');
};
window.closeClientModal = function() {
    document.getElementById('client-modal').classList.remove('open');
};
window.openConfirmDelete = function(id, name) {
    openConfirmAction('/admin/clients/' + id, 'delete_client', name);
};

// ─── MODALE PARCELLE ────────────────────────────────────
window.openParcelleModal = function() {
    const m = document.getElementById('parcelle-modal');
    m.classList.remove('hidden');
    m.classList.add('flex');
};
window.closeParcelleModal = function() {
    const m = document.getElementById('parcelle-modal');
    m.classList.add('hidden');
    m.classList.remove('flex');
};

// Si erreur de validation → rouvrir la modale parcelle
@if($errors->has('titre') || $errors->has('dimensions') || $errors->has('prix_total') || $errors->has('mensualite') || $errors->has('localisation'))
    document.addEventListener('DOMContentLoaded', openParcelleModal);
@endif
@if($errors->has('preuve_admin'))
    document.addEventListener('DOMContentLoaded', function() {
        // Cherche le paiement concerné et rouvre la modale d'approbation
    });
@endif
</script>

@endsection
