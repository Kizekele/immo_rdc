@extends('pdf.layout')
@section('content')
<h1>Rapport des Paiements</h1>
<table>
<thead>
<tr>
<th>Client</th>
<th>Mois</th>
<th>Montant (FC)</th>
<th>Statut</th>
</tr>
</thead>
<tbody>

@foreach($paiements as $p)
<tr>
<td>{{ $p->user->name ?? 'N/A' }}</td>
<td>{{ $p->mois_concerne }}</td>
<td>{{ number_format($p->montant_paye, 0, ',', ' ')

}}</td>

<td>{{ $p->statut }}</td>
</tr>
@endforeach
</tbody>
</table>
@endsection