@extends('layouts.app')

@section('content')

<style>
    :root {
        --c-card:   rgba(13, 20, 44, 0.55);
        --c-border: rgba(255, 255, 255, 0.07);
        --c-text:   #e2e8f0;
        --c-muted:  #64748b;
        --c-sub:    #94a3b8;
        --emerald:  #10b981;
    }
    .prog-bar  { height:6px; border-radius:99px; background:rgba(255,255,255,0.06); overflow:hidden; }
    .prog-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,#10b981,#34d399); }
    .detail-card { background:var(--c-card); backdrop-filter:blur(12px); border:1px solid var(--c-border); border-radius:18px; overflow:hidden; margin-bottom:20px; }
    .detail-header { padding:20px 24px; border-bottom:1px solid var(--c-border); }
    .t-head th { padding:11px 18px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--c-muted); background:rgba(0,0,0,0.1); }
    .t-body td { padding:13px 18px; font-size:13px; color:var(--c-sub); border-top:1px solid var(--c-border); vertical-align:middle; }
    .t-body td:first-child { color:var(--c-text); font-weight:600; }
    .pill { display:inline-block; font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px; }
</style>

{{-- Back --}}
<div style="margin-bottom:20px;">
    <a href="{{ route('dashboard-admin') }}" style="font-size:13px;color:var(--c-muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
        ← Retour au tableau de bord
    </a>
</div>

{{-- En-tête client --}}
<div class="detail-card">
    <div style="padding:28px 28px;">
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div style="width:56px;height:56px;border-radius:16px;background:rgba(99,102,241,0.15);color:#818cf8;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;flex-shrink:0;">
                {{ strtoupper(substr($client->name, 0, 1)) }}
            </div>
            <div style="flex:1;">
                <div style="font-size:20px;font-weight:800;color:var(--c-text);">{{ $client->name }}</div>
                <div style="font-size:13px;color:var(--c-muted);margin-top:2px;">{{ $client->email }}</div>
                @if($client->profil && $client->profil->telephone)
                    <div style="font-size:12px;color:var(--c-muted);">📞 {{ $client->profil->telephone }}</div>
                @endif
                <div style="font-size:11px;color:#334155;margin-top:4px;">Inscrit le {{ $client->created_at->format('d/m/Y') }}</div>
            </div>
            <div style="display:flex;gap:16px;flex-wrap:wrap;">
                <div style="text-align:center;">
                    <div style="font-size:22px;font-weight:800;color:#10b981;">{{ $client->parcelles->count() }}</div>
                    <div style="font-size:10px;color:var(--c-muted);text-transform:uppercase;font-weight:700;">Parcelles</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:22px;font-weight:800;color:#818cf8;">{{ $paiements->where('statut','valide')->count() }}</div>
                    <div style="font-size:10px;color:var(--c-muted);text-transform:uppercase;font-weight:700;">Paiements OK</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:22px;font-weight:800;color:#f43f5e;">{{ $paiements->where('statut','rejete')->count() }}</div>
                    <div style="font-size:10px;color:var(--c-muted);text-transform:uppercase;font-weight:700;">Rejetés</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Parcelles avec progression --}}
<div class="detail-card">
    <div class="detail-header">
        <div style="font-size:15px;font-weight:700;color:var(--c-text);">Évolution par Parcelle</div>
    </div>
    <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
        @forelse($client->parcelles as $parcelle)
            @php
                $pct = $parcelle->prix_total > 0 ? round(($parcelle->somme_payee / $parcelle->prix_total) * 100) : 0;
                $pctColor = $pct >= 80 ? '#10b981' : ($pct >= 40 ? '#f59e0b' : '#f43f5e');
            @endphp
            <div style="background:rgba(0,0,0,0.15);border:1px solid var(--c-border);border-radius:14px;padding:18px 20px;">
                <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                    <div>
                        <div style="font-size:14px;font-weight:700;color:var(--c-text);">{{ $parcelle->titre }}</div>
                        <div style="font-size:12px;color:var(--c-muted);margin-top:2px;">
                            📍 {{ $parcelle->localisation }}
                            @if($parcelle->dimensions) · 📐 {{ $parcelle->dimensions }} @endif
                        </div>
                    </div>
                    <span class="pill" style="background:{{ $parcelle->statut==='reservee' ? 'rgba(245,158,11,0.1)' : 'rgba(16,185,129,0.1)' }};color:{{ $parcelle->statut==='reservee' ? '#f59e0b' : '#10b981' }};">
                        {{ ucfirst($parcelle->statut) }}
                    </span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:14px;">
                    <div>
                        <div style="font-size:10px;color:var(--c-muted);text-transform:uppercase;font-weight:700;">Prix Total</div>
                        <div style="font-size:15px;font-weight:800;color:var(--c-text);">{{ number_format($parcelle->prix_total, 0, ',', ' ') }} $</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--c-muted);text-transform:uppercase;font-weight:700;">Déjà Payé</div>
                        <div style="font-size:15px;font-weight:800;color:#10b981;">{{ number_format($parcelle->somme_payee, 0, ',', ' ') }} $</div>
                    </div>
                    <div>
                        <div style="font-size:10px;color:var(--c-muted);text-transform:uppercase;font-weight:700;">Restant</div>
                        <div style="font-size:15px;font-weight:800;color:#f43f5e;">{{ number_format($parcelle->prix_total - $parcelle->somme_payee, 0, ',', ' ') }} $</div>
                    </div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:11px;color:var(--c-muted);">Progression remboursement</span>
                        <span style="font-size:12px;font-weight:700;color:{{ $pctColor }};">{{ $pct }}%</span>
                    </div>
                    <div class="prog-bar"><div class="prog-fill" style="width:{{ $pct }}%;background:{{ $pctColor }};"></div></div>
                </div>
            </div>
        @empty
            <p style="color:var(--c-muted);font-size:13px;">Aucune parcelle.</p>
        @endforelse
    </div>
</div>

{{-- Historique paiements --}}
<div class="detail-card">
    <div class="detail-header">
        <div style="font-size:15px;font-weight:700;color:var(--c-text);">Historique des Paiements</div>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead class="t-head">
                <tr>
                    <th>Parcelle</th>
                    <th>Mois</th>
                    <th>Montant</th>
                    <th>Pénalité</th>
                    <th>Statut</th>
                    <th>Date soumission</th>
                    <th>Preuve Client</th>
                    <th>Reçu Admin</th>
                </tr>
            </thead>
            <tbody class="t-body">
                @forelse($paiements as $p)
                    @php
                        $sc = match($p->statut) {
                            'valide'  => ['bg'=>'rgba(16,185,129,0.1)','color'=>'#10b981','label'=>'Validé'],
                            'rejete'  => ['bg'=>'rgba(244,63,94,0.1)', 'color'=>'#fb7185','label'=>'Rejeté'],
                            default   => ['bg'=>'rgba(245,158,11,0.1)','color'=>'#f59e0b','label'=>'En attente'],
                        };
                    @endphp
                    <tr>
                        <td>{{ $p->parcelle->titre ?? '—' }}</td>
                        <td>{{ $p->mois_concerne }}</td>
                        <td style="font-family:monospace;font-weight:700;color:var(--c-text);">{{ number_format($p->montant_paye, 0, ',', ' ') }} $</td>
                        <td style="color:#f43f5e;">
                            @if($p->penalite_appliquee > 0) +{{ $p->penalite_appliquee }} $ @else — @endif
                        </td>
                        <td><span class="pill" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">{{ $sc['label'] }}</span></td>
                        <td style="color:var(--c-muted);font-size:12px;">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($p->preuve_photo)
                                <a href="{{ asset('storage/'.$p->preuve_photo) }}" target="_blank" style="font-size:11px;color:#7c3aed;">Voir</a>
                            @else — @endif
                        </td>
                        <td>
                            @if($p->preuve_admin)
                                <a href="{{ asset('storage/'.$p->preuve_admin) }}" target="_blank" style="font-size:11px;color:#10b981;">Voir reçu</a>
                            @else
                                <span style="font-size:11px;color:#334155;">En attente</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:30px;text-align:center;color:var(--c-muted);">Aucun paiement enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>;
</div>


@endsection
