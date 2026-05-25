@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 30px 20px;">
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; font-weight: bold; color: #1f2937; margin-bottom: 10px;">
            Services de l'Organisation
        </h1>
        <p style="color: #6b7280; font-size: 1.1rem;">
            Découvrez les différents services et départements de SDCC
        </p>
    </div>

    @php
        $groupedServices = \App\Models\Service::grouped();
    @endphp

    @forelse ($groupedServices as $department => $services)
        <div style="margin-bottom: 50px;">
            <div style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 4px solid #4caf50;">
                <h2 style="font-size: 1.8rem; font-weight: bold; color: #1f2937;">
                    <i class="fas fa-building" style="color: #2e7d32; margin-right: 10px;"></i>{{ $department }}
                </h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; margin-bottom: 30px;">
                @forelse ($services as $service)
                    <div style="
                        background: #ffffff;
                        border-radius: 8px;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                        border-left: 4px solid #4caf50;
                        padding: 24px;
                        transition: all 0.3s ease;
                        hover: box-shadow 0 6px 16px rgba(0, 0, 0, 0.12);
                    " onmouseover="this.style.boxShadow='0 6px 16px rgba(0, 0, 0, 0.12)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0, 0, 0, 0.08)'">
                        <div style="margin-bottom: 16px;">
                            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937;">
                                {{ $service->display_name }}
                            </h3>
                            <p style="font-size: 0.9rem; color: #999; margin-top: 4px;">
                                {{ $service->name }}
                            </p>
                        </div>

                        @if ($service->description)
                            <p style="color: #6b7280; font-size: 0.95rem; margin-bottom: 16px; line-height: 1.5;">
                                {{ substr($service->description, 0, 150) }}
                                @if (strlen($service->description) > 150)...@endif
                            </p>
                        @endif

                        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                            <span style="
                                display: inline-block;
                                background: #e8f5e9;
                                color: #2e7d32;
                                padding: 4px 12px;
                                border-radius: 20px;
                                font-size: 0.8rem;
                                font-weight: 600;
                            ">
                                ✓ Actif
                            </span>
                            <span style="color: #999; font-size: 0.8rem;">
                                Ordre: {{ $service->sort_order }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px;">
                        <p style="color: #6b7280;">Aucun service dans ce département</p>
                    </div>
                @endforelse
            </div>
        </div>
    @empty
        <div style="
            background: #f3f4f6;
            border-radius: 8px;
            padding: 60px 20px;
            text-align: center;
        ">
            <i class="fas fa-inbox" style="font-size: 3rem; color: #999; margin-bottom: 20px; display: block;"></i>
            <p style="color: #6b7280; font-size: 1.1rem;">Aucun service disponible pour le moment.</p>
        </div>
    @endforelse

    <!-- Summary Statistics -->
    <div style="margin-top: 60px; padding-top: 40px; border-top: 1px solid #e5e7eb;">
        <h3 style="font-size: 1.5rem; font-weight: bold; color: #1f2937; margin-bottom: 30px;">
            Résumé
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <!-- Card 1 -->
            <div style="
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                border-radius: 8px;
                padding: 24px;
                border-left: 4px solid #3b82f6;
            ">
                <p style="color: #6b7280; font-size: 0.9rem; font-weight: 500;">Départements Totaux</p>
                <p style="font-size: 2.5rem; font-weight: bold; color: #3b82f6; margin-top: 10px;">
                    {{ count($groupedServices) }}
                </p>
            </div>

            <!-- Card 2 -->
            <div style="
                background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
                border-radius: 8px;
                padding: 24px;
                border-left: 4px solid #22c55e;
            ">
                <p style="color: #6b7280; font-size: 0.9rem; font-weight: 500;">Services Actifs</p>
                <p style="font-size: 2.5rem; font-weight: bold; color: #22c55e; margin-top: 10px;">
                    @php
                        echo \App\Models\Service::where('is_active', true)->count();
                    @endphp
                </p>
            </div>

            <!-- Card 3 -->
            <div style="
                background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
                border-radius: 8px;
                padding: 24px;
                border-left: 4px solid #a855f7;
            ">
                <p style="color: #6b7280; font-size: 0.9rem; font-weight: 500;">Services Totaux</p>
                <p style="font-size: 2.5rem; font-weight: bold; color: #a855f7; margin-top: 10px;">
                    @php
                        echo \App\Models\Service::count();
                    @endphp
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
