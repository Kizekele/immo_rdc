<style>
    body { font-family: DejaVu Sans, sans-serif; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
    th { background-color: #f2f2f2; }
</style>

<h1>Liste des clients et parcelles</h1>
<table border="1" width="100%" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th>Client</th>
            <th>Email</th>
            <th>Parcelle(s)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clients as $client)
        <tr>
            <td>{{ $client->name }}</td>
            <td>{{ $client->email }}</td>
            <td>
                @foreach($client->parcelles as $parcelle)
                    {{ $parcelle->titre }} ({{ $parcelle->localisation }})<br>
                @endforeach
            </td>
        </tr>
        @endforeach
    </tbody>
</table>