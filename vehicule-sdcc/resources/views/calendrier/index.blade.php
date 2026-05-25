@extends('layouts.app')

@section('title', 'SDCC - Calendrier des affectations')

@section('content')
<style>
    /* Content Wrapper - Main Container */
    .content-wrapper {
        padding: 30px 20px;
        background: #f5f5f5;
        min-height: 100vh;
    }

    /* Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
        padding: 28px 32px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-bottom: 40px;
        gap: 30px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: white;
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 0;
    }

    .page-title i {
        color: white;
        font-size: 28px;
    }

    .header-actions {
        display: flex;
        gap: 20px;
        align-items: center;
        flex-wrap: wrap;
    }

    .vehicle-dropdown-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-radius: 6px;
        padding: 12px 16px;
        min-width: 220px;
    }

    .vehicle-dropdown-wrapper i {
        color: white;
        font-size: 18px;
    }

    .vehicle-dropdown {
        flex: 1;
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 14px;
        color: white;
        outline: none;
        font-family: inherit;
        font-weight: 500;
    }

    .vehicle-dropdown option {
        background: white;
        color: #2E7D32;
    }

    .vehicle-dropdown::placeholder {
        color: rgba(255, 255, 255, 0.8);
    }

    .today-btn {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.5);
        padding: 12px 28px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 650;
        font-size: 13px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
    }

    .today-btn:hover {
        background: linear-gradient(135deg, #66BB6A 0%, #81C784 100%);
        border-color: rgba(255, 255, 255, 0.7);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        transform: translateY(-1px);
    }

    /* Legend */
    .legend {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
        justify-content: flex-end;
        align-items: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        white-space: nowrap;
        color: #2E7D32;
        font-weight: 700;
    }

    .legend-item span {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #2E7D32;
    }

    .legend-color {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .legend-approved {
        background: #C8E6C9;
        border: 2px solid #2E7D32;
    }

    .legend-pending {
        background: #FFE0B2;
        border: 2px solid #FFA726;
    }

    .legend-cancelled {
        background: #FFCDD2;
        border: 2px solid #E53935;
    }

    .legend-today {
        background: #FFA500;
        border: 2px solid #FF8C00;
    }

    /* Calendar */
    .calendar-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        padding: 40px;
        width: 100%;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        gap: 30px;
    }

    .calendar-header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .calendar-month {
        font-size: 32px;
        font-weight: 700;
        color: #2E7D32;
        min-width: 220px;
        text-align: center;
        margin: 0;
        cursor: pointer;
        padding: 12px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        user-select: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .calendar-month:hover {
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.15) 0%, rgba(255, 167, 38, 0.15) 100%);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
    }

    .calendar-month i {
        font-size: 20px;
        color: #FFA726;
    }

    /* Month/Year Picker Modal */
    .month-year-picker-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .month-year-picker-overlay.active {
        display: flex;
    }

    .month-year-picker-modal {
        background: white;
        border-radius: 16px;
        padding: 32px;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            transform: translateY(40px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .month-year-picker-header {
        font-size: 20px;
        font-weight: 700;
        color: #2E7D32;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .month-year-picker-header i {
        color: #FFA726;
        font-size: 24px;
    }

    .picker-section {
        margin-bottom: 24px;
    }

    .picker-label {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: #2E7D32;
        margin-bottom: 12px;
        letter-spacing: 0.8px;
        display: block;
    }

    .month-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }

    .month-btn {
        padding: 12px;
        border: 2px solid #e0e0e0;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        color: #2E7D32;
        transition: all 0.3s ease;
    }

    .month-btn:hover {
        border-color: #4CAF50;
        background: #e8f5e9;
    }

    .month-btn.active {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
        color: white;
        border-color: #4CAF50;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .year-input-wrapper {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .year-input {
        flex: 1;
        padding: 12px 14px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #2E7D32;
        transition: all 0.3s ease;
    }

    .year-input:focus {
        outline: none;
        border-color: #4CAF50;
        background: #f9fafb;
    }

    .year-btn {
        width: 44px;
        height: 44px;
        border: 2px solid #e0e0e0;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        color: #2E7D32;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .year-btn:hover {
        background: #e8f5e9;
        border-color: #4CAF50;
    }

    .picker-actions {
        display: flex;
        gap: 12px;
        margin-top: 28px;
    }

    .picker-btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .picker-btn-cancel {
        background: #f0f0f0;
        color: #666;
    }

    .picker-btn-cancel:hover {
        background: #e0e0e0;
    }

    .picker-btn-apply {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .picker-btn-apply:hover {
        box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
        transform: translateY(-2px);
    }

    .calendar-nav {
        display: flex;
        gap: 12px;
    }

    .calendar-nav-btn {
        background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
        border: none;
        color: white;
        width: 44px;
        height: 44px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(76, 175, 80, 0.2);
    }

    .calendar-nav-btn i {
        color: white;
        font-size: 18px;
    }

    .calendar-nav-btn:hover {
        background: linear-gradient(135deg, #66BB6A 0%, #FF8A65 100%);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        transform: translateY(-2px);
    }

    /* Calendar Grid */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        background: #C8E6C9;
        border: 2px solid #C8E6C9;
        border-radius: 10px;
        overflow: hidden;
    }

    .calendar-day-header {
        background: #C8E6C9;
        padding: 18px 16px;
        text-align: center;
        font-weight: 700;
        font-size: 13px;
        color: #2E7D32;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .calendar-day {
        background: white;
        padding: 18px 16px;
        min-height: 140px;
        font-size: 14px;
        color: #2E7D32;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .calendar-day.other-month {
        background: #E8F5E9;
        color: #A5D6A7;
    }

    .calendar-day.today {
        border: 3px solid #FFA500;
        box-shadow: inset 0 0 0 1px #FFA500;
    }

    .calendar-day.approved {
        background: #C8E6C9;
    }

    .calendar-day.pending {
        background: #FFE0B2;
    }

    .calendar-day.free {
        background: #E8F5E9;
    }

    .calendar-day.cancelled {
        background: #FFEBEE;
    }

    .calendar-day.available-clickable {
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .calendar-day.available-clickable:hover {
        transform: translateY(-2px);
        box-shadow: inset 0 0 0 2px #4CAF50;
    }

    .calendar-day.unavailable-clickable {
        cursor: not-allowed;
    }

    .calendar-day-number {
        font-weight: 700;
        margin-bottom: 14px;
        color: #2E7D32;
        font-size: 15px;
    }

    .calendar-day.other-month .calendar-day-number {
        color: #A5D6A7;
    }

    .calendar-events {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
    }

    .calendar-event {
        padding: 8px 12px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 650;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        background: rgba(255, 255, 255, 0.8);
        letter-spacing: 0.2px;
    }

    .calendar-event.approved {
        background: rgba(76, 175, 80, 0.4);
        color: #1B5E20;
        border-left: 3px solid #2E7D32;
    }

    .calendar-event.pending {
        background: rgba(255, 167, 38, 0.4);
        color: #E65100;
        border-left: 3px solid #FFA726;
    }

    .calendar-event.cancelled {
        background: rgba(229, 57, 53, 0.15);
        color: #C62828;
        border-left: 3px solid #E53935;
    }

    .calendar-event.range-start {
        border-radius: 5px 2px 2px 5px;
    }

    .calendar-event.range-middle {
        border-radius: 2px;
        border-left-width: 0;
        text-align: center;
        letter-spacing: 2px;
        color: inherit;
        opacity: 0.9;
    }

    .calendar-event.range-end {
        border-radius: 2px 5px 5px 2px;
    }

    .calendar-event.range-single {
        border-radius: 5px;
    }

    .calendar-error-alert {
        display: none;
        margin: 0 0 16px 0;
        padding: 12px 14px;
        border-left: 4px solid #e53935;
        background: #ffebee;
        color: #b71c1c;
        border-radius: 6px;
        font-weight: 600;
    }

    .reservation-tooltip {
        position: fixed;
        z-index: 5000;
        width: 300px;
        max-width: calc(100vw - 24px);
        padding: 12px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        background: #fff;
        border: 1px solid #e0e0e0;
        pointer-events: none;
        display: none;
        font-size: 12px;
        line-height: 1.4;
    }

    .reservation-tooltip .tt-title {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .reservation-tooltip .tt-item {
        border-top: 1px solid #f0f0f0;
        padding-top: 8px;
        margin-top: 8px;
    }

    .reservation-tooltip .tt-status {
        display: inline-block;
        border-radius: 12px;
        padding: 2px 8px;
        color: #fff;
        font-weight: 700;
        font-size: 11px;
        margin-bottom: 6px;
    }

    .reservation-tooltip .tt-status.approved { background: #2e7d32; }
    .reservation-tooltip .tt-status.pending { background: #ef6c00; }
    .reservation-tooltip .tt-status.cancelled,
    .reservation-tooltip .tt-status.rejected { background: #c62828; }

    .calendar-event.free {
        background: rgba(76, 175, 80, 0.25);
        color: #2E7D32;
        border-left: 3px solid #4CAF50;
    }

    /* Responsive — Tablet */
    @media (max-width: 1024px) {
        .content-wrapper {
            padding: 25px 15px;
        }

        .page-header {
            padding: 25px 28px;
            margin-bottom: 35px;
        }

        .calendar-container {
            padding: 35px;
        }

        .calendar-day {
            min-height: 130px;
            padding: 16px 14px;
        }

        .calendar-header {
            margin-bottom: 35px;
            gap: 25px;
        }

        .calendar-month {
            font-size: 28px;
            min-width: 200px;
        }

        .legend {
            gap: 20px;
        }
    }

    /* Responsive - Month Picker */
    @media (max-width: 768px) {
        .month-year-picker-modal {
            padding: 24px;
            max-width: 95%;
        }

        .month-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .month-btn {
            padding: 10px;
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .month-year-picker-modal {
            padding: 20px;
        }

        .month-year-picker-header {
            font-size: 16px;
            margin-bottom: 18px;
        }

        .month-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .picker-label {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .picker-btn {
            padding: 10px 16px;
            font-size: 12px;
        }

    /* Responsive — Tablet/Mobile */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 20px 12px;
        }

        .page-header {
            flex-direction: column;
            gap: 20px;
            padding: 22px 24px;
            margin-bottom: 30px;
            align-items: stretch;
        }

        .page-title {
            font-size: 21px;
            margin-bottom: 5px;
        }

        .header-actions {
            flex-direction: column;
            width: 100%;
            gap: 12px;
        }

        .vehicle-dropdown-wrapper,
        .today-btn {
            width: 100%;
            justify-content: center;
        }

        .calendar-container {
            padding: 25px;
        }

        .calendar-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 30px;
        }

        .calendar-header-left {
            width: 100%;
            gap: 15px;
        }

        .calendar-month {
            font-size: 24px;
            min-width: 150px;
        }

        .legend {
            width: 100%;
            justify-content: flex-start;
            gap: 16px;
        }

        .calendar-day {
            min-height: 120px;
            padding: 14px 12px;
        }

        .calendar-day-header {
            padding: 14px 12px;
            font-size: 12px;
        }

        .calendar-day-number {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .calendar-event {
            font-size: 11px;
            padding: 6px 10px;
        }
    }

    /* Responsive — Mobile */
    @media (max-width: 480px) {
        .content-wrapper {
            padding: 16px 10px;
        }

        .page-header {
            flex-direction: column;
            padding: 18px 16px;
            gap: 16px;
            align-items: stretch;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 18px;
            gap: 10px;
        }

        .page-title i {
            font-size: 24px;
        }

        .header-actions {
            flex-direction: column;
            width: 100%;
            gap: 10px;
        }

        .vehicle-dropdown-wrapper {
            width: 100%;
            padding: 10px 12px;
        }

        .today-btn {
            width: 100%;
            padding: 10px 16px;
        }

        .calendar-container {
            padding: 18px;
        }

        .calendar-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }

        .calendar-header-left {
            width: 100%;
            justify-content: space-between;
            gap: 10px;
        }

        .calendar-month {
            font-size: 20px;
            min-width: 120px;
            text-align: left;
        }

        .calendar-nav {
            gap: 8px;
        }

        .calendar-nav-btn {
            width: 38px;
            height: 38px;
            font-size: 16px;
        }

        .legend {
            width: 100%;
            justify-content: flex-start;
            gap: 12px;
            font-size: 12px;
        }

        .legend-item {
            font-size: 12px;
            gap: 8px;
        }

        .calendar-grid {
            gap: 1px;
        }

        .calendar-day {
            min-height: 100px;
            padding: 12px 10px;
        }

        .calendar-day-header {
            padding: 10px 8px;
            font-size: 11px;
        }

        .calendar-day-number {
            font-size: 13px;
            margin-bottom: 8px;
        }

        .calendar-events {
            gap: 6px;
        }

        .calendar-event {
            font-size: 10px;
            padding: 5px 8px;
        }
    }
</style>

<!-- Content Wrapper - Unified -->
<div class="content-wrapper">
    <div id="calendarErrorAlert" class="calendar-error-alert">{{ $calendarOptions['reservation_messages']['day_reserved'] ?? 'This day is already reserved' }}</div>

    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-calendar-alt"></i> Calendrier des affectations</h1>
        <div class="header-actions">
            <div class="vehicle-dropdown-wrapper">
                <i class="fas fa-car"></i>
                <select class="vehicle-dropdown" id="vehicleSelect">
                    <option value="all" @if($selectedCar === 'all') selected @endif>{{ $calendarOptions['all_vehicles_label'] }}</option>
                    @foreach($vehicles as $key => $vehicle)
                        <option value="{{ $key }}" @if($selectedCar === $key) selected @endif>
                            {{ $vehicle['name'] }} ({{ $vehicle['plate'] }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button class="today-btn"><i class="fas fa-check-circle"></i> Aujourd'hui</button>
        </div>
    </div>

    <!-- Calendar -->
    <div class="calendar-container">
        <div class="calendar-header">
            <div class="calendar-header-left">
                <div class="calendar-nav">
                    <button class="calendar-nav-btn"><i class="fas fa-chevron-left"></i></button>
                </div>
                <h2 class="calendar-month"><i class="fas fa-calendar-days"></i> {{ $currentMonth->monthName }} {{ $currentMonth->year }}</h2>
                <div class="calendar-nav">
                    <button class="calendar-nav-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            
            <!-- Legend -->
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color legend-approved"></div>
                    <span>{{ $calendarOptions['legend']['approved'] }}</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-pending"></div>
                    <span>{{ $calendarOptions['legend']['pending'] }}</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-cancelled"></div>
                    <span>{{ $calendarOptions['legend']['cancelled'] }}</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: white; border: 2px solid #ddd;"></div>
                    <span>{{ $calendarOptions['legend']['available'] }}</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-today"></div>
                    <span>{{ $calendarOptions['legend']['today'] }}</span>
                </div>
            </div>
        </div>

        <div class="calendar-grid">
            <!-- Day Headers -->
            <div class="calendar-day-header">DIM</div>
            <div class="calendar-day-header">LUN</div>
            <div class="calendar-day-header">MAR</div>
            <div class="calendar-day-header">MER</div>
            <div class="calendar-day-header">JEU</div>
            <div class="calendar-day-header">VEN</div>
            <div class="calendar-day-header">SAM</div>

            <!-- Calendar Days -->
            @php
                $currentDay = $firstDay->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                $lastDisplayDay = $lastDay->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
            @endphp

            @while($currentDay <= $lastDisplayDay)
                @php
                    $isCurrentMonth = $currentDay->month == $currentMonth->month;
                    $dateKey = $currentDay->format('Y-m-d');
                    $dayStatus = $statusByDate[$dateKey] ?? ['key' => 'available', 'label' => ($calendarOptions['day_status_labels']['available'] ?? 'Disponible'), 'blocked' => false];
                    
                    $dayClass = 'calendar-day';
                    $dayClass .= ' ' . ($dayStatus['key'] ?? 'available');
                    $dayClass .= ($dayStatus['blocked'] ?? false) ? ' unavailable-clickable' : ' available-clickable';
                    
                    if ($currentDay->format('Y-m-d') == $today->format('Y-m-d')) {
                        $dayClass .= ' today';
                    }
                    
                    if (!$isCurrentMonth) {
                        $dayClass .= ' other-month';
                    }
                @endphp
                <div class="{{ $dayClass }}"
                     data-date="{{ $dateKey }}"
                     data-status="{{ $dayStatus['key'] }}"
                     data-blocked="{{ ($dayStatus['blocked'] ?? false) ? '1' : '0' }}"
                     data-car-id="{{ $selectedCar !== 'all' && isset($vehicles[$selectedCar]['car_id']) ? $vehicles[$selectedCar]['car_id'] : '' }}"
                     data-reservations='@json($dayStatus["reservations"] ?? [])'>
                    <div class="calendar-day-number">{{ $currentDay->day }}</div>
                    <div class="calendar-events">
                        @forelse($dayStatus['reservations'] ?? [] as $reservation)
                            <div
                                class="calendar-event {{ $reservation['status'] }} range-{{ $reservation['range_position'] ?? 'single' }}"
                                title="{{ ($reservation['employee'] ?? '') . ' — ' . ($reservation['vehicle'] ?? '') . ' (' . ($reservation['period'] ?? '') . ')' }}"
                            >
                                {{ $reservation['display_label'] ?? $reservation['status_label'] }}
                            </div>
                        @empty
                            <div class="calendar-event free">{{ $dayStatus['label'] }}</div>
                        @endforelse
                    </div>
                </div>
                @php
                    $currentDay->addDay();
                @endphp
            @endwhile
        </div>
    </div>
<!-- Close content-wrapper -->
</div>

<!-- Month/Year Picker Modal -->
<div class="month-year-picker-overlay" id="monthYearPickerOverlay">
    <div class="month-year-picker-modal">
        <div class="month-year-picker-header">
            <i class="fas fa-calendar-check"></i>
            Sélectionner une date
        </div>

        <!-- Month Selection -->
        <div class="picker-section">
            <label class="picker-label">Mois</label>
            <div class="month-grid" id="monthGrid">
                <!-- Months will be generated by JavaScript -->
            </div>
        </div>

        <!-- Year Selection -->
        <div class="picker-section">
            <label class="picker-label">Année</label>
            <div class="year-input-wrapper">
                <button class="year-btn" id="yearDecrement"><i class="fas fa-minus"></i></button>
                <input type="number" class="year-input" id="yearInput" min="2026" value="2026">
                <button class="year-btn" id="yearIncrement"><i class="fas fa-plus"></i></button>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="picker-actions">
            <button class="picker-btn picker-btn-cancel" id="pickerCancel">Annuler</button>
            <button class="picker-btn picker-btn-apply" id="pickerApply">Appliquer</button>
        </div>
    </div>
</div>

<div id="reservationTooltip" class="reservation-tooltip"></div>

<script>
    const calendarUiOptions = @json($calendarOptions);
    const dayReservedMessage = calendarUiOptions?.reservation_messages?.day_reserved || 'This day is already reserved';

    // Calendar Navigation
    const navButtons = document.querySelectorAll('.calendar-nav-btn');
    const todayBtn = document.querySelector('.today-btn');
    const vehicleDropdown = document.querySelector('#vehicleSelect');
    const calendarMonth = document.querySelector('.calendar-month');

    // Get current month from calendar (format: "Avril 2026")
    let monthText = calendarMonth.textContent.trim();
    let monthYearParts = monthText.split(' ');
    let currentYear = parseInt(monthYearParts[1]);
    let monthName = monthYearParts[0];
    
    // Month mapping
    const monthMap = {
        'January': 1, 'February': 2, 'March': 3, 'April': 4, 'May': 5, 'June': 6,
        'July': 7, 'August': 8, 'September': 9, 'October': 10, 'November': 11, 'December': 12,
        // French months
        'Janvier': 1, 'Février': 2, 'Mars': 3, 'Avril': 4, 'Mai': 5, 'Juin': 6,
        'Juillet': 7, 'Août': 8, 'Septembre': 9, 'Octobre': 10, 'Novembre': 11, 'Décembre': 12
    };
    let currentMonth = monthMap[monthName] || new Date().getMonth() + 1;

    // Get current selected car and month/year from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentCar = urlParams.get('car') || 'all';
    const currentPageMonth = urlParams.get('month') || currentMonth;
    const currentPageYear = urlParams.get('year') || currentYear;

    // Helper function to build URL with all parameters
    function buildCalendarUrl(month, year, car = currentCar) {
        const baseUrl = '{{ route("calendrier") }}';
        const params = new URLSearchParams();
        params.set('month', month);
        params.set('year', year);
        if (car !== 'all') {
            params.set('car', car);
        }
        return `${baseUrl}?${params.toString()}`;
    }

    // Today button
    if (todayBtn) {
        todayBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Reload page with current month and selected car
            window.location.href = buildCalendarUrl(new Date().getMonth() + 1, new Date().getFullYear());
        });
    }

    // Navigation buttons
    navButtons.forEach((btn, index) => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Determine if this is previous or next button based on position
            const parentNav = btn.closest('.calendar-nav');
            const navGroup = btn.closest('.calendar-header-left').querySelectorAll('.calendar-nav-btn');
            const isNext = Array.from(navGroup).indexOf(btn) === 1; // Second button is next
            
            let targetMonth = parseInt(currentPageMonth);
            let targetYear = parseInt(currentPageYear);
            
            if (isNext) {
                targetMonth++;
                if (targetMonth > 12) {
                    targetMonth = 1;
                    targetYear++;
                }
            } else {
                targetMonth--;
                if (targetMonth < 1) {
                    targetMonth = 12;
                    targetYear--;
                }
            }
            
            // Navigate with month, year, and car parameters
            window.location.href = buildCalendarUrl(targetMonth, targetYear);
        });
    });

    // Vehicle dropdown
    if (vehicleDropdown) {
        vehicleDropdown.addEventListener('change', function(e) {
            const selectedCar = this.value;
            // Navigate to the same month/year but with different vehicle filter
            window.location.href = buildCalendarUrl(currentPageMonth, currentPageYear, selectedCar);
        });
    }

    // Scroll to today if it exists
    const todayElement = document.querySelector('.calendar-day.today');
    if (todayElement) {
        setTimeout(() => {
            todayElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    // Reservation day click behavior (available => redirect, reserved => alert)
    const calendarDays = document.querySelectorAll('.calendar-day[data-date]');
    const tooltip = document.getElementById('reservationTooltip');

    function buildTooltipHtml(reservations) {
        if (!reservations || !reservations.length) {
            return '';
        }

        const rows = reservations.map((reservation, index) => {
            return `
                <div class="${index === 0 ? '' : 'tt-item'}">
                    <div class="tt-status ${reservation.status || ''}">${reservation.status_label || (calendarUiOptions?.status_labels_en?.pending || 'Pending')}</div>
                    <div><strong>${calendarUiOptions?.tooltip_fields?.employee || 'Employee'}:</strong> ${reservation.employee || 'N/A'}</div>
                    <div><strong>${calendarUiOptions?.tooltip_fields?.vehicle || 'Vehicle'}:</strong> ${reservation.vehicle || 'N/A'}</div>
                    <div><strong>${calendarUiOptions?.tooltip_fields?.period || 'Période'}:</strong> ${reservation.period || reservation.date || 'N/A'}</div>
                    <div><strong>${calendarUiOptions?.tooltip_fields?.date || 'Jour'}:</strong> ${reservation.date || 'N/A'}</div>
                    <div><strong>${calendarUiOptions?.tooltip_fields?.destination || 'Destination'}:</strong> ${reservation.destination || 'N/A'}</div>
                    <div><strong>${calendarUiOptions?.tooltip_fields?.start || 'Start'}:</strong> ${reservation.start_time || '-'}</div>
                    <div><strong>${calendarUiOptions?.tooltip_fields?.end || 'End'}:</strong> ${reservation.end_time || '-'}</div>
                </div>
            `;
        }).join('');

        return `<div class="tt-title">${calendarUiOptions?.tooltip_title || 'Reservation details'}</div>${rows}`;
    }

    function moveTooltip(event) {
        if (!tooltip || tooltip.style.display !== 'block') {
            return;
        }

        const offset = 14;
        const tooltipWidth = tooltip.offsetWidth || 300;
        const tooltipHeight = tooltip.offsetHeight || 180;
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        let left = event.clientX + offset;
        let top = event.clientY + offset;

        if (left + tooltipWidth > viewportWidth - 8) {
            left = event.clientX - tooltipWidth - offset;
        }
        if (top + tooltipHeight > viewportHeight - 8) {
            top = event.clientY - tooltipHeight - offset;
        }

        tooltip.style.left = `${Math.max(8, left)}px`;
        tooltip.style.top = `${Math.max(8, top)}px`;
    }

    function hideTooltip() {
        if (!tooltip) {
            return;
        }
        tooltip.style.display = 'none';
        tooltip.innerHTML = '';
    }

    calendarDays.forEach(day => {
        day.addEventListener('mouseenter', function (event) {
            if (this.classList.contains('other-month')) {
                hideTooltip();
                return;
            }

            const reservationsRaw = this.dataset.reservations || '[]';
            let reservations = [];
            try {
                reservations = JSON.parse(reservationsRaw);
            } catch (e) {
                reservations = [];
            }

            if (!reservations.length) {
                hideTooltip();
                return;
            }

            if (tooltip) {
                tooltip.innerHTML = buildTooltipHtml(reservations);
                tooltip.style.display = 'block';
                moveTooltip(event);
            }
        });

        day.addEventListener('mousemove', moveTooltip);
        day.addEventListener('mouseleave', hideTooltip);

        day.addEventListener('click', function () {
            const blocked = this.dataset.blocked === '1';
            const date = this.dataset.date;
            const carId = this.dataset.carId;

            if (this.classList.contains('other-month')) {
                return;
            }

            if (blocked) {
                const alertBox = document.getElementById('calendarErrorAlert');
                if (alertBox) {
                    alertBox.textContent = dayReservedMessage;
                    alertBox.style.display = 'block';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
                return;
            }

            let url = '{{ route("mes-demandes.create") }}' + '?start_date=' + encodeURIComponent(date);
            if (carId) {
                url += '&car_id=' + encodeURIComponent(carId);
            }
            window.location.href = url;
        });
    });

    /* ========== MONTH/YEAR PICKER FUNCTIONALITY ========== */
    const monthYearPickerOverlay = document.getElementById('monthYearPickerOverlay');
    const pickerCancel = document.getElementById('pickerCancel');
    const pickerApply = document.getElementById('pickerApply');
    const monthGrid = document.getElementById('monthGrid');
    const yearInput = document.getElementById('yearInput');
    const yearDecrement = document.getElementById('yearDecrement');
    const yearIncrement = document.getElementById('yearIncrement');

    // Month names in French
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    const monthNamesShort = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];

    let selectedMonth = parseInt(currentPageMonth);
    let selectedYear = parseInt(currentPageYear);

    // Initialize month grid
    function initializeMonthGrid() {
        monthGrid.innerHTML = '';
        monthNamesShort.forEach((monthShort, index) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'month-btn';
            if (index + 1 === selectedMonth) {
                btn.classList.add('active');
            }
            btn.textContent = monthShort;
            btn.addEventListener('click', function() {
                document.querySelectorAll('.month-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                selectedMonth = index + 1;
            });
            monthGrid.appendChild(btn);
        });
    }

    // Open month/year picker
    const calendarMonthElement = document.querySelector('.calendar-month');
    if (calendarMonthElement) {
        calendarMonthElement.addEventListener('click', function(e) {
            e.preventDefault();
            selectedMonth = parseInt(currentPageMonth);
            selectedYear = parseInt(currentPageYear);
            yearInput.value = selectedYear;
            initializeMonthGrid();
            monthYearPickerOverlay.classList.add('active');
        });
    }

    // Year controls
    yearDecrement.addEventListener('click', function() {
        selectedYear = Math.max(2026, selectedYear - 1);
        yearInput.value = selectedYear;
    });

    yearIncrement.addEventListener('click', function() {
        // No upper bound: allow navigation into the far future
        selectedYear = selectedYear + 1;
        yearInput.value = selectedYear;
    });

    yearInput.addEventListener('change', function() {
        const newYear = parseInt(this.value) || selectedYear;
        // Enforce only a minimum year of 2026; no maximum
        selectedYear = Math.max(2026, newYear);
        this.value = selectedYear;
    });

    // Cancel button
    pickerCancel.addEventListener('click', function() {
        monthYearPickerOverlay.classList.remove('active');
    });

    // Apply button
    pickerApply.addEventListener('click', function() {
        monthYearPickerOverlay.classList.remove('active');
        window.location.href = buildCalendarUrl(selectedMonth, selectedYear);
    });

    // Close modal when clicking outside
    monthYearPickerOverlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if (monthYearPickerOverlay.classList.contains('active')) {
            if (e.key === 'Escape') {
                monthYearPickerOverlay.classList.remove('active');
            }
        }
    });
</script>
@endsection
