@extends('pdf.layout')
@section('content')
<h1>Liste des Clients</h1>
<table>
<thead>
<tr>
<th>Nom</th>
<th>Email</th>
<th>Téléphone</th>
</tr>
</thead>
<tbody>
@foreach($clients as $c)
<tr>
<td>{{ $c->name }}</td>
<td>{{ $c->email }}</td>
<td>{{ $c->profil->telephone ?? 'N/A' }}</td>
</tr>
@endforeach
</tbody>
</table>
@endsection