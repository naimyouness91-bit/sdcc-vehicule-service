@extends('layouts.app')

@section('title', 'Kilométrage des véhicules')

@section('content')
<div style="padding: 24px; max-width: 1100px; margin: 0 auto;">
    <h1>Suivi du Kilométrage des Véhicules</h1>

    @if(session('success'))
        <div style="background:#e6ffed;border-left:4px solid #00c853;padding:12px;margin-bottom:12px;">
            {{ session('success') }}
        </div>
    @endif

    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="text-align:left;border-bottom:2px solid #eee;">
                <th>Nom</th>
                <th>Actuel</th>
                <th>Max</th>
                <th>Restant</th>
                <th>%</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cars as $car)
            @php
                $remaining = $car->remainingKilometers();
                $status = $car->mileageStatus();
                $percent = number_format($car->mileagePercentage(), 1);
            @endphp
            <tr style="border-bottom:1px solid #f4f4f4;">
                <td style="padding:12px 8px;">{{ $car->name }}</td>
                <td style="padding:12px 8px;">{{ number_format($car->kilometrage_actuel) }} km</td>
                <td style="padding:12px 8px;">{{ number_format($car->kilometrage_max) }} km</td>
                <td style="padding:12px 8px;">
                    {{ number_format($remaining) }} km
                    @if($status === 'Dépassé')
                        <div style="color:#d32f2f;font-weight:700;margin-top:6px;">⚠️ Cette voiture a dépassé son kilométrage !</div>
                    @endif
                </td>
                <td style="padding:12px 8px; width:220px;">
                    <div style="background:#f1f5f9;border-radius:8px;height:12px;overflow:hidden;">
                        <div style="width:{{ $percent }}%;background: {{ $status === 'Dépassé' ? '#d32f2f' : ($status === 'Bientôt atteint' ? '#ff9800' : '#4caf50') }};height:100%;"></div>
                    </div>
                    <div style="font-size:12px;color:#666;margin-top:6px;">{{ $percent }}%</div>
                </td>
                <td style="padding:12px 8px;">
                    @if($status === 'Normal')
                        <span style="background:#e8f5e9;color:#2e7d32;padding:6px 10px;border-radius:12px;font-weight:700;">Normal</span>
                    @elseif($status === 'Bientôt atteint')
                        <span style="background:#fff4e5;color:#bf360c;padding:6px 10px;border-radius:12px;font-weight:700;">Bientôt atteint</span>
                    @else
                        <span style="background:#ffebee;color:#c62828;padding:6px 10px;border-radius:12px;font-weight:700;">Dépassé</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
