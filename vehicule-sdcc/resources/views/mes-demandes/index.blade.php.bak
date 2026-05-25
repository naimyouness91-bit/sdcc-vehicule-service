<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDCC - Mes Demandes</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/all.min.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #4CAF50 0%, #66BB6A 20%, #81C784 40%, #FFA726 70%, #FFA500 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-weight: bold;
            padding: 12px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            background: linear-gradient(90deg, #4CAF50 0%, #66BB6A 25%, #81C784 50%, #FFA726 75%, #FFA500 100%);
        }

        .sidebar-header:hover {
            background: linear-gradient(90deg, #45a049 0%, #5cb85c 25%, #6db96d 50%, #ff9014 75%, #ff8c00 100%);
        }

        .sidebar-header:active {
            background: linear-gradient(90deg, #4CAF50 0%, #66BB6A 25%, #FFA726 75%, #FFA500 100%);
        }

        .sidebar-logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            color: #FFA500;
        }

        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
        }

        .sidebar-title {
            color: white;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .sidebar-title span {
            display: block;
        }

        .role-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }

        .role-badge:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        .role-badge i {
            font-size: 13px;
        }

        .sidebar-user-section {
            background: linear-gradient(180deg, #FFA726 0%, #FFA500 100%);
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: auto;
        }

        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        .sidebar-user-service {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.9);
        }

        .sidebar-title-sub {
            display: none;
        }

        .admin-badge {
            display: none;
        }

        .user-section {
            display: none;
        }

        .user-avatar {
            display: none;
        }

        .user-info {
            display: none;
        }

        .user-name {
            display: none;
        }

        .user-role {
            display: none;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 0;
        }

        .sidebar-nav:first-of-type {
            margin-bottom: auto;
        }

        .sidebar-nav:last-of-type {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav:last-of-type .sidebar-nav-item {
            background: linear-gradient(90deg, #4CAF50 0%, #66BB6A 25%, #81C784 50%, #FFA726 75%, #FFA500 100%);
            color: white;
        }

        .sidebar-nav:last-of-type .sidebar-nav-item:hover {
            background: linear-gradient(90deg, #45a049 0%, #5cb85c 25%, #6db96d 50%, #ff9014 75%, #ff8c00 100%);
        }

        .sidebar-nav-item {
            padding: 12px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.95);
            text-decoration: none;
            font-size: 13px;
            border-left: 3px solid transparent;
        }

        .sidebar-nav-item i {
            width: 16px;
            text-align: center;
            font-size: 14px;
        }

        .sidebar-nav-item:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .sidebar-nav-item.active {
            background: linear-gradient(90deg, #4CAF50 0%, #66BB6A 25%, #81C784 50%, #FFA726 75%, #FFA500 100%);
            color: white;
            border-left-color: white;
        }

        .sidebar-nav-item.user-profile {
            display: none;
        }

        .user-avatar-mini {
            display: none;
        }

        .user-info-text {
            display: none;
        }

        .user-name-text {
            display: none;
        }

        .user-status-text {
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name-text {
            font-size: 13px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
        }

        .user-status-text {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
        }

        /* Breadcrumb */
        .breadcrumb {
            color: #999;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .breadcrumb a {
            color: #4CAF50;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }

        .new-request-btn {
            background: linear-gradient(90deg, #4CAF50 0%, #66BB6A 50%, #FFA726 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .new-request-btn:hover {
            background: linear-gradient(90deg, #66BB6A 0%, #81C784 50%, #FF8A65 100%);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
            transform: translateY(-2px);
        }

        /* Requests Table */
        .requests-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(90deg, #4CAF50 0%, #66BB6A 40%, #FFA726 100%);
        }

        th {
            text-align: left;
            padding: 12px;
            font-size: 12px;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 13px;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .request-link {
            color: #FFA726;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .request-link:hover {
            color: #FF8A65;
            text-decoration: underline;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3e0;
            color: #FFA726;
            border-left: 3px solid #FFA726;
        }

        .status-approved {
            background-color: #e8f5e9;
            color: #4CAF50;
            border-left: 3px solid #4CAF50;
        }

        .status-rejected {
            background-color: #ffebee;
            color: #c62828;
            border-left: 3px solid #c62828;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
            border-top: 4px solid;
        }

        .stat-card-total {
            border-top-color: #4CAF50;
        }

        .stat-card-pending {
            border-top-color: #FFA726;
        }

        .stat-card-approved {
            border-top-color: #4CAF50;
        }

        .stat-card-rejected {
            border-top-color: #FFB74D;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Filters Container */
        .filters-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 16px;
            border: none;
            background: #f5f5f5;
            color: #666;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .filter-tab:hover {
            background: #e8e8e8;
        }

        .filter-tab.active {
            background: linear-gradient(90deg, #4CAF50 0%, #66BB6A 50%, #FFA726 100%);
            color: white;
            border: none;
        }

        .search-filter {
            display: flex;
            gap: 10px;
            flex: 1;
            min-width: 300px;
        }

        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 13px;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .date-input {
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 13px;
            transition: all 0.3s;
        }

        .date-input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .result-count {
            font-size: 13px;
            color: #999;
            white-space: nowrap;
        }

        /* Request Cards Grid */
        .requests-grid {
            display: grid;
            gap: 15px;
        }

        .request-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid;
            transition: all 0.3s ease;
            position: relative;
        }

        .request-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .request-card.status-approved {
            border-left-color: #4CAF50;
        }

        .request-card.status-pending {
            border-left-color: #FFA726;
        }

        .request-card.status-rejected {
            border-left-color: #c62828;
        }

        .request-card.status-cancelled {
            border-left-color: #999;
        }

        .request-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .request-destination {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .request-meta {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: #999;
        }

        .request-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .request-card-content {
            margin-bottom: 15px;
        }

        .request-notes {
            color: #666;
            font-size: 13px;
            line-height: 1.5;
            padding: 12px;
            background: #f9f9f9;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .request-car-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 6px;
        }

        .request-car-info i {
            color: #4CAF50;
            width: 18px;
        }

        .request-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f5f5f5;
            padding-top: 15px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .request-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .request-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4CAF50 0%, #FFA726 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        .request-user-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .request-user-name {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .request-user-time {
            font-size: 11px;
            color: #999;
        }

        .request-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .action-btn-primary {
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
            color: white;
        }

        .action-btn-primary:hover {
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
            transform: translateY(-1px);
        }

        .action-btn-secondary {
            background: #f5f5f5;
            color: #1e3a5f;
        }

        .action-btn-secondary:hover {
            background: #e8e8e8;
        }

        .action-btn-danger {
            background: #ffebee;
            color: #c62828;
        }

        .action-btn-danger:hover {
            background: #ffcdd2;
        }

        .request-detail-link {
            color: #4CAF50;
            font-size: 13px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s;
        }

        .request-detail-link:hover {
            color: #66BB6A;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .filters-section {
                flex-direction: column;
            }

            .search-filter {
                flex-direction: column;
            }

            .request-card-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="{{ asset('images/logo-sdcc-2.png') }}" alt="SDCC Car Reservation">
            </div>
            <div class="sidebar-title">
                <span>RESERVATION</span><br><span>VÉHICULES DE SERVICE</span>
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="role-badge">
            <i class="fas fa-shield-alt"></i> {{ ucfirst(str_replace('_', ' ', Auth::user()->isSuperAdmin() ? Auth::user()->current_role : Auth::user()->role)) }}
        </a>

        <!-- Employee Navigation -->
        <div class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="sidebar-nav-item"><i class="fas fa-chart-line"></i> Tableau de bord</a>
            <a href="{{ route('mes-demandes.index') }}" class="sidebar-nav-item active"><i class="fas fa-file-invoice"></i> Mes demandes</a>
            <a href="{{ route('mes-demandes.create') }}" class="sidebar-nav-item"><i class="fas fa-plus"></i> Nouvelle demande</a>
            <a href="{{ route('cars.index') }}" class="sidebar-nav-item"><i class="fas fa-car"></i> Véhicules</a>
            <a href="{{ route('calendrier') }}" class="sidebar-nav-item"><i class="fas fa-calendar"></i> Calendrier</a>
        </div>

        <div class="sidebar-nav">
            <a href="{{ route('logout') }}" class="sidebar-nav-item" onclick="if(event.preventDefault) event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </div>

        <div class="sidebar-user-section">
            <div class="sidebar-user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                <div class="sidebar-user-service">{{ Auth::user()->service ?? 'Service' }}</div>
            </div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span> > </span>
            <span>Mes demandes</span>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Mes demandes</h1>
                <div style="font-size: 12px; color: #999; margin-top: 5px;">{{ count($demandes) }} demandes au total — Alice Martin</div>
            </div>
            <a href="{{ route('mes-demandes.create') }}" class="new-request-btn">
                <i class="fas fa-plus"></i> Nouvelle demande
            </a>
        </div>

        <!-- Requests List -->
        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card stat-card-total">
                <div class="stat-label">TOTAL</div>
                <div class="stat-number">{{ count($demandes) }}</div>
            </div>
            <div class="stat-card stat-card-pending">
                <div class="stat-label">EN ATTENTE</div>
                <div class="stat-number">{{ collect($demandes)->where('status', 'pending')->count() }}</div>
            </div>
            <div class="stat-card stat-card-approved">
                <div class="stat-label">APPROUVEES</div>
                <div class="stat-number">{{ collect($demandes)->where('status', 'approved')->count() }}</div>
            </div>
            <div class="stat-card stat-card-rejected">
                <div class="stat-label">REJETEES</div>
                <div class="stat-number">{{ collect($demandes)->where('status', 'rejected')->count() }}</div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filter-tabs">
                <button class="filter-tab active" onclick="filterRequests('all')">Toutes ({{ count($demandes) }})</button>
                <button class="filter-tab" onclick="filterRequests('pending')">En attente ({{ collect($demandes)->where('status', 'pending')->count() }})</button>
                <button class="filter-tab" onclick="filterRequests('approved')">Approuvees ({{ collect($demandes)->where('status', 'approved')->count() }})</button>
                <button class="filter-tab" onclick="filterRequests('rejected')">Rejetees ({{ collect($demandes)->where('status', 'rejected')->count() }})</button>
            </div>
            <div class="search-filter">
                <input type="text" class="search-input" placeholder="Rechercher une destination..." id="searchInput" onkeyup="searchRequests(this.value)">
                <select class="date-input" id="monthFilter" onchange="filterByMonth(this.value)">
                    <option value="">Avr 2026</option>
                    <option value="03">Mar 2026</option>
                    <option value="02">Fév 2026</option>
                    <option value="01">Jan 2026</option>
                </select>
                <span class="result-count" id="resultCount">8 résultats</span>
            </div>
        </div>

        <!-- Request Cards Grid -->
        <div class="requests-grid" id="requestsContainer">
            @if($demandes && count($demandes) > 0)
                @foreach($demandes as $demande)
                    <div class="request-card status-{{ $demande['status'] }}" data-status="{{ $demande['status'] }}">
                        <div class="request-card-header">
                            <div>
                                <div class="request-destination">{{ $demande['destination'] }}</div>
                                <div class="request-meta">
                                    <div class="request-meta-item">
                                        <i class="fas fa-calendar"></i> {{ date('d M', strtotime($demande['start_date'])) }} — {{ date('d M', strtotime($demande['end_date'])) }}
                                    </div>
                                    <div class="request-meta-item">
                                        <i class="fas fa-clock"></i> {{ intval(abs(strtotime($demande['end_date']) - strtotime($demande['start_date'])) / 86400) + 1 }} jours
                                    </div>
                                    <div class="request-meta-item">
                                        <i class="fas fa-road"></i> 240 km
                                    </div>
                                </div>
                            </div>
                            <span class="status-badge {{ $demande['status'] == 'pending' ? 'status-pending' : ($demande['status'] == 'approved' ? 'status-approved' : 'status-rejected') }}">
                                <i class="fas {{ $demande['status'] == 'approved' ? 'fa-check-circle' : ($demande['status'] == 'pending' ? 'fa-clock' : 'fa-times-circle') }}"></i>
                                {{ ucfirst($demande['status']) == 'Pending' ? 'En attente' : (ucfirst($demande['status']) == 'Approved' ? 'Approved' : 'Rejected') }}
                            </span>
                        </div>

                        <div class="request-card-content">
                            <div class="request-notes">
                                <strong>Motif:</strong> Réunion client projet et présentation offre commerciale
                            </div>
                            <div class="request-car-info">
                                <i class="fas fa-car"></i> <strong>Renault Kardian WW831966</strong>
                            </div>
                        </div>

                        <div class="request-card-footer">
                            <div class="request-user-info">
                                <div class="request-user-avatar">{{ substr($demande['employee'], 0, 1) }}</div>
                                <div class="request-user-details">
                                    <div class="request-user-name">{{ $demande['employee'] }}</div>
                                    <div class="request-user-time">Soumise il y a 2h</div>
                                </div>
                            </div>
                            <div class="request-actions">
                                @if($demande['status'] == 'pending')
                                    <button class="action-btn action-btn-primary">
                                        <i class="fas fa-check"></i> Approver
                                    </button>
                                    <button class="action-btn action-btn-danger">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                @endif
                                <a href="{{ route('mes-demandes.show', $demande['id']) }}" class="request-detail-link">
                                    Détail <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucune demande</h3>
                    <p>Vous n'avez pas encore créé de demande de véhicule.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function filterRequests(status) {
            const cards = document.querySelectorAll('.request-card');
            const tabs = document.querySelectorAll('.filter-tab');
            let count = 0;

            tabs.forEach(tab => tab.classList.remove('active'));
            event.target.classList.add('active');

            cards.forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = 'block';
                    count++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('resultCount').textContent = count + ' résultats';
        }

        function searchRequests(searchTerm) {
            const cards = document.querySelectorAll('.request-card');
            let count = 0;

            cards.forEach(card => {
                const destination = card.querySelector('.request-destination').textContent.toLowerCase();
                const employee = card.querySelector('.request-user-name').textContent.toLowerCase();

                if (destination.includes(searchTerm.toLowerCase()) || employee.includes(searchTerm.toLowerCase())) {
                    card.style.display = 'block';
                    count++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('resultCount').textContent = count + ' résultats';
        }

        function filterByMonth(month) {
            // Month filter logic can be implemented here
        }
    </script>
</body>
</html>
