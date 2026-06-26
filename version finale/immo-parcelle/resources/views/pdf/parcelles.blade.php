@extends('pdf.layout')
@section('content')
<h1>État du Parc immobilier</h1>
<table>
<thead>

<tr>
<th>Titre</th>
<th>Localisation</th>
<th>Prix (FC)</th>
<th>Statut</th>
</tr>
</thead>
<tbody>
@foreach($parcelles as $p)
<tr>
<td>{{ $p->titre }}</td>
<td>{{ $p->localisation }}</td>
<td>{{ number_format($p->prix_total, 0, ',', ' ')

}}</td>

<td>{{ $p->statut }}</td>
</tr>
@endforeach
</tbody>
</table>
@endsection