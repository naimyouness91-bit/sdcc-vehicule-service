<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SDCC - Gestion des Véhicules de Service')</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print.css') }}" media="print">
    <style>
        :root {
            --brand-primary: #2e7d32;
            --brand-primary-light: #4caf50;
            --brand-primary-soft: #66bb6a;
            --brand-accent: #ffa726;
            --brand-accent-strong: #fb8c00;
            --brand-danger: #e53935;
            --bg-app: #f4f7f6;
            --bg-surface: #ffffff;
            --text-strong: #1f2937;
            --text-muted: #6b7280;
            --border-soft: #e5e7eb;
            --gradient-brand: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-accent) 100%);
            --gradient-sidebar: linear-gradient(180deg, #1f6f34 0%, var(--brand-primary) 32%, var(--brand-primary-soft) 65%, var(--brand-accent) 92%, var(--brand-accent-strong) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: var(--bg-app);
            overflow-x: hidden;
        }

        /* ==================== TOP NAVBAR ==================== */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: var(--bg-surface);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            z-index: 1000;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-soft);
            overflow: visible;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar-toggle-btn {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--brand-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .navbar-toggle-btn:hover {
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.12) 0%, rgba(255, 167, 38, 0.14) 100%);
            color: var(--brand-accent);
            transform: scale(1.1);
        }

        .navbar-toggle-btn.active {
            color: var(--brand-accent);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 18px;
            background: var(--gradient-brand);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
        }

        .navbar-logo {
            width: 52px;
            height: 52px;
            background: linear-gradient(145deg, #ffffff 0%, #f7fbf7 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: inherit;
            font-weight: 800;
            font-size: 12px;
            letter-spacing: 0.6px;
            border: 1px solid rgba(76, 175, 80, 0.18);
            box-shadow: 0 6px 16px rgba(46, 125, 50, 0.16);
            padding: 4px;
        }

        .navbar-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
        }

        .navbar-brand-text {
            display: flex;
            flex-direction: column;
            gap: 0;
            line-height: 1.2;
        }

        .navbar-brand-title {
            font-size: 16px;
            font-weight: 700;
            background: var(--gradient-brand);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.3px;
        }

        .navbar-brand-subtitle {
            font-size: 11px;
            font-weight: 600;
            background: linear-gradient(135deg, var(--brand-accent) 0%, var(--brand-accent-strong) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 1100;
        }

        .navbar-notifications {
            position: relative;
        }

        .notifications-btn {
            background: var(--bg-surface);
            border: 1px solid var(--border-soft);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--brand-primary);
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .notifications-btn:hover {
            color: var(--brand-accent-strong);
            transform: translateY(-1px);
        }

        .notifications-badge {
            position: absolute;
            top: -5px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: var(--brand-danger);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            display: none;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        .notifications-badge.show {
            display: flex;
        }

        .notifications-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 12px;
            width: min(380px, 88vw);
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.14);
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.25s ease;
            z-index: 1300;
        }

        .notifications-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .notifications-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            border-bottom: 1px solid #efefef;
        }

        .notifications-head-title {
            font-size: 14px;
            font-weight: 700;
            color: #222;
        }

        .notifications-mark-all {
            border: none;
            background: none;
            color: #2E7D32;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .notifications-list {
            max-height: 320px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 11px 14px;
            border-bottom: 1px solid #f3f3f3;
            cursor: pointer;
        }

        .notification-item.unread {
            background: #F4FBF4;
        }

        .notification-title {
            font-size: 13px;
            font-weight: 700;
            color: #222;
            margin-bottom: 3px;
        }

        .notification-message {
            font-size: 12px;
            color: #555;
            line-height: 1.35;
        }

        .notification-meta {
            margin-top: 6px;
            font-size: 11px;
            color: #999;
        }

        .notifications-empty {
            text-align: center;
            color: #777;
            font-size: 13px;
            padding: 20px 12px;
        }

        .navbar-search {
            display: none;
            background: #f5f7fa;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 14px;
            width: 200px;
            transition: all 0.3s ease;
        }

        .navbar-search:focus {
            outline: none;
            border-color: #2E7D32;
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.08);
        }

        .navbar-profile {
            position: relative;
            display: flex;
            align-items: center;
        }

        .profile-btn {
            background: linear-gradient(135deg, #2E7D32 0%, #FFA726 100%);
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.25);
            flex-shrink: 0;
        }

        .profile-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(46, 125, 50, 0.35);
        }

        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            min-width: 220px;
            margin-top: 12px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1200;
            overflow: hidden;
        }

        .profile-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 16px 16px 12px 16px;
            border-bottom: 1px solid #e8e8e8;
        }

        .dropdown-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #2E7D32 0%, #FFA726 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        .dropdown-user-name {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 14px;
        }

        .dropdown-user-role {
            font-size: 12px;
            color: #666;
            font-weight: 500;
            text-transform: capitalize;
        }

        .dropdown-menu {
            padding: 10px 0;
        }

        .dropdown-item {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.08) 0%, rgba(255, 167, 38, 0.08) 100%);
            color: #2E7D32;
        }

        .dropdown-item i {
            width: 16px;
            color: #FFA726;
        }

        .dropdown-divider {
            height: 1px;
            background: #e8e8e8;
            margin: 6px 0;
        }

        .dropdown-item.logout {
            color: #c62828;
        }

        .dropdown-item.logout:hover {
            background: #ffebee;
        }

        .dropdown-item.logout i {
            color: #c62828;
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar {
            position: fixed;
            left: 0;
            top: 70px;
            width: 260px;
            height: calc(100vh - 70px);
            background: var(--gradient-sidebar);
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            transform: translateX(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 999;
            box-shadow: 4px 0 12px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        /* Scrollable menu content - grows to fill available space */
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 25px 0;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 3px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .sidebar-brand-mobile {
            display: none;
            padding: 0 20px 20px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            margin-bottom: 20px;
            font-weight: 700;
            color: white;
            font-size: 16px;
        }

        .sidebar-section {
            padding: 15px 0;
        }

        .sidebar-section-title {
            padding: 10px 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 1px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
            font-size: 14px;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: rgba(255, 255, 255, 0.3);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.14);
            color: white;
            border-left-color: rgba(255, 255, 255, 0.95);
            padding-left: 22px;
        }

        .sidebar-link:hover::before {
            transform: scaleY(1);
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-left-color: rgba(255, 255, 255, 0.98);
            font-weight: 600;
            box-shadow: inset 4px 0 0 rgba(255, 255, 255, 0.3);
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar-link-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-link-badge {
            background: rgba(255, 255, 255, 0.3);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            color: white;
            min-width: 22px;
            text-align: center;
        }

        /* Sidebar group (collapsible submenu) */
        .sidebar-group {
            display: block;
        }

        .sidebar-group-toggle {
            width: 100%;
            background: transparent;
            border: 0;
            text-align: left;
        }

        .sidebar-group-toggle .sidebar-group-caret {
            width: 18px;
            text-align: center;
            opacity: 0.9;
            transition: transform 0.2s ease;
        }

        .sidebar-group.open .sidebar-group-toggle .sidebar-group-caret {
            transform: rotate(90deg);
        }

        .sidebar-submenu {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height 0.25s ease, opacity 0.2s ease;
            padding-left: 10px;
        }

        .sidebar-group.open .sidebar-submenu {
            max-height: 420px;
            opacity: 1;
        }

        .sidebar-sublink {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px 10px 30px;
            color: rgba(255, 255, 255, 0.82);
            text-decoration: none;
            transition: all 0.25s ease;
            border-left: 3px solid transparent;
            font-size: 13px;
            position: relative;
        }

        .sidebar-sublink:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: rgba(255, 255, 255, 0.9);
            padding-left: 32px;
        }

        .sidebar-sublink.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border-left-color: rgba(255, 255, 255, 0.9);
            font-weight: 700;
        }

        .sidebar-user-section {
            flex-shrink: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.25) 100%);
            padding: 16px 18px;
            border-top: 2px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
        }

        .sidebar-user-section:hover {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.35) 100%);
        }

        .sidebar-user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.85) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2E7D32;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 4px;
            padding-right: 8px;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 700;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            letter-spacing: 0.3px;
        }

        .sidebar-user-role {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: capitalize;
            font-weight: 500;
            letter-spacing: 0.2px;
            line-height: 1.2;
        }

        .sidebar-logout {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: rgba(255, 255, 255, 0.9);
            width: 38px;
            height: 38px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
            font-size: 15px;
            flex-shrink: 0;
        }

        .sidebar-logout:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.35);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .sidebar-logout:active {
            transform: translateY(0);
        }

        /* ==================== MAIN CONTENT ==================== */
        .main-content {
            margin-left: 260px;
            margin-top: 70px;
            padding: 30px;
            min-height: calc(100vh - 70px);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* ==================== CONTENT WRAPPER - UNIFIED CENTERING ==================== */
        .content-wrapper {
            width: 100%;
            max-width: none;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* ==================== OVERLAY ==================== */
        .sidebar-overlay {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 998;
            display: none;
            pointer-events: none;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* ==================== UNIFIED STYLE UTILITIES ==================== */
        /* Ensure consistent spacing and appearance across all pages */
        .page-header,
        .dashboard-header {
            width: 100%;
        }

        .stats-grid,
        .requests-grid,
        .vehicles-grid,
        .analytics-grid {
            width: 100%;
        }

        .section,
        .users-table,
        .calendar-container,
        .requests-section,
        .vehicle-card {
            width: 100%;
        }

        /* ==================== RESPONSIVE ==================== */
        /* ==================== RESPONSIVE DESIGN ==================== */
        
        @media (max-width: 1200px) {
            .content-wrapper {
                padding: 0 25px;
            }
        }

        @media (max-width: 1024px) {
            .content-wrapper {
                padding: 0 20px;
            }

            .navbar-right {
                gap: 10px;
            }

            .notifications-dropdown {
                width: min(350px, 90vw);
            }

            .profile-dropdown {
                min-width: 200px;
            }
        }

        @media (max-width: 768px) {
            .sidebar-overlay {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 20px 15px;
                margin-top: 70px;
            }

            .content-wrapper {
                padding: 0 15px;
            }

            .sidebar {
                width: 260px;
                box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            }

            .sidebar-menu {
                padding: 20px 0;
            }

            .sidebar.collapsed {
                box-shadow: none;
                transform: translateX(-100%);
            }

            .navbar-brand-text {
                display: flex;
                flex-direction: column;
                gap: 0;
            }

            .navbar-brand-title {
                font-size: 15px;
            }

            .navbar-brand-subtitle {
                font-size: 10px;
            }

            .navbar-search {
                display: none !important;
            }

            .notifications-dropdown {
                width: min(320px, 90vw);
                max-height: 300px;
            }

            .profile-dropdown {
                min-width: 220px;
                right: -10px;
            }

            .top-navbar {
                padding: 0 15px;
            }

            /* Touch-friendly button sizes on mobile */
            .notifications-btn,
            .profile-btn {
                min-width: 44px;
                min-height: 44px;
            }

            /* Responsive sidebar user section */
            .sidebar-user-section {
                padding: 14px 16px;
                gap: 10px;
            }

            .sidebar-user-avatar {
                width: 40px;
                height: 40px;
                font-size: 15px;
            }

            .sidebar-user-name {
                font-size: 12px;
            }

            .sidebar-user-role {
                font-size: 10px;
            }

            .sidebar-logout {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }

        @media (max-width: 640px) {
            .top-navbar {
                padding: 0 12px;
                height: 65px;
            }

            .navbar-left {
                gap: 15px;
            }

            .navbar-toggle-btn {
                font-size: 20px;
                padding: 8px;
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .navbar-logo {
                width: 45px;
                height: 45px;
                font-size: 11px;
            }

            .navbar-brand-text {
                display: none;
            }

            .navbar-right {
                gap: 8px;
            }

            .notifications-btn {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }

            .profile-btn {
                width: 44px;
                height: 44px;
                font-size: 16px;
            }

            .sidebar {
                width: 80vw;
                max-width: 260px;
                top: 65px;
                height: calc(100vh - 65px);
            }

            .main-content {
                margin-top: 65px;
            }

            .content-wrapper {
                padding: 0 12px;
            }

            .profile-dropdown {
                right: -15px;
                min-width: 220px;
            }

            .notifications-dropdown {
                right: -10px;
            }

            .sidebar-menu {
                padding: 15px 0;
            }
        }

        @media (max-width: 480px) {
            .top-navbar {
                height: 60px;
                padding: 0 10px;
            }

            .navbar-left {
                gap: 10px;
            }

            .navbar-toggle-btn {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .navbar-logo {
                width: 40px;
                height: 40px;
                font-size: 10px;
            }

            .navbar-right {
                gap: 6px;
            }

            .notifications-btn,
            .profile-btn {
                width: 40px;
                height: 40px;
                min-width: 40px;
                min-height: 40px;
                font-size: 16px;
            }

            .sidebar {
                top: 60px;
                height: calc(100vh - 60px);
                width: 85vw;
                max-width: 240px;
            }

            .sidebar-menu {
                padding: 12px 0;
            }

            .main-content {
                margin-top: 60px;
                padding: 12px 10px;
            }

            .content-wrapper {
                padding: 0 10px;
                gap: 12px;
            }

            /* Responsive sidebar text sizing */
            .sidebar-link {
                padding: 12px 16px;
                font-size: 13px;
                gap: 10px;
            }

            .sidebar-section-title {
                padding: 8px 16px;
                font-size: 10px;
            }

            .sidebar-user-section {
                padding: 12px 12px;
                gap: 8px;
            }

            .sidebar-user-avatar {
                width: 38px;
                height: 38px;
                font-size: 14px;
            }

            .sidebar-user-name {
                font-size: 11px;
            }

            .sidebar-user-role {
                font-size: 9px;
            }

            .sidebar-logout {
                width: 34px;
                height: 34px;
                font-size: 13px;
            }

            /* Responsive buttons */
            button, .btn, a.btn {
                min-height: 40px;
                min-width: 40px;
                padding: 10px 16px;
                font-size: 14px;
                border-radius: 6px;
            }

            /* Responsive form elements */
            input, textarea, select {
                min-height: 40px;
                font-size: 16px; /* Prevents iOS zoom on input focus */
                padding: 10px 12px;
            }

            /* Responsive text */
            h1 {
                font-size: 20px !important;
            }

            h2 {
                font-size: 18px !important;
            }

            h3 {
                font-size: 16px !important;
            }

            body {
                font-size: 14px;
            }

            .dropdown-header {
                padding: 12px;
            }

            .dropdown-item {
                padding: 10px 12px;
                font-size: 13px;
                gap: 10px;
            }

            .profile-dropdown {
                width: calc(100vw - 20px);
                right: auto;
                left: 10px;
            }

            .notifications-dropdown {
                width: calc(100vw - 20px);
                right: auto;
                left: 10px;
            }
        }

        /* Open state: expand sidebar from viewport top while navbar stays fixed */
        body.sidebar-open .sidebar {
            top: 0 !important;
            height: 100vh !important;
            padding-top: 82px !important;
        }

        /* Merge header + sidebar visually when menu is open */
        body.sidebar-open .top-navbar {
            box-shadow: none !important;
            background: #2E7D32 !important;
        }

        body.sidebar-open .navbar-toggle-btn,
        body.sidebar-open .navbar-brand {
            color: rgba(255, 255, 255, 0.95);
            -webkit-text-fill-color: rgba(255, 255, 255, 0.95);
        }

        @media (max-width: 480px) {
            body.sidebar-open .sidebar {
                padding-top: 72px !important;
            }
        }

        /* ==================== SCROLLBAR ==================== */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* ==================== ANIMATIONS ==================== */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .sidebar-link {
            animation: slideIn 0.3s ease forwards;
        }

        .sidebar-link:nth-child(1) { animation-delay: 0.05s; }
        .sidebar-link:nth-child(2) { animation-delay: 0.1s; }
        .sidebar-link:nth-child(3) { animation-delay: 0.15s; }
        .sidebar-link:nth-child(4) { animation-delay: 0.2s; }
        .sidebar-link:nth-child(5) { animation-delay: 0.25s; }

        /* ==================== UTILITY ==================== */
        .content-title {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #2E7D32 0%, #FFA726 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #666;
            margin-bottom: 20px;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumb-item.active {
            color: #2E7D32;
            font-weight: 600;
        }

        /* print: legacy behavior removed from global layout to preserve per-page print styles */
    </style>
    <!-- SweetAlert2 for notifications -->
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}">
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.min.js') }}"></script>
    
    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
    <!-- Flash Messages for Toast Notifications -->
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div data-toast-error="{{ $error }}"></div>
        @endforeach
    @endif
    @if (session('success'))
        <div data-toast-success="{{ session('success') }}"></div>
    @endif
    @if (session('error'))
        <div data-toast-error="{{ session('error') }}"></div>
    @endif
    @if (session('warning'))
        <div data-toast-warning="{{ session('warning') }}"></div>
    @endif
    @if (session('info'))
        <div data-toast-info="{{ session('info') }}"></div>
    @endif

    <!-- TOP NAVBAR -->
    <nav class="top-navbar no-print">
        <div class="navbar-left">
            <button class="navbar-toggle-btn" id="sidebarToggle" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                <div class="navbar-logo">
                    <img src="{{ asset('images/logo-sdcc-2.png') }}" alt="SDCC Logo">
                </div>
                <span class="navbar-brand-text">
                    <span class="navbar-brand-title">Réservation</span>
                    <span class="navbar-brand-subtitle">Véhicule de Service</span>
                </span>
            </a>
        </div>

        <div class="navbar-right">
            <div class="navbar-notifications">
                <button class="notifications-btn" id="notificationsBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notifications-badge" id="notificationsBadge">0</span>
                </button>
                <div class="notifications-dropdown" id="notificationsDropdown">
                    <div class="notifications-head">
                        <div class="notifications-head-title">Notifications</div>
                        <button type="button" class="notifications-mark-all" id="markAllNotificationsBtn">Tout lire</button>
                    </div>
                    <div class="notifications-list" id="notificationsList">
                        <div class="notifications-empty">Aucune notification.</div>
                    </div>
                </div>
            </div>

            <div class="navbar-profile">
                <button class="profile-btn" id="profileBtn" aria-label="Profile Menu">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </button>

                <div class="profile-dropdown" id="profileDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-user-info">
                            <div class="dropdown-user-avatar">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="dropdown-user-name">{{ Auth::user()->name ?? 'User' }}</div>
                                <div class="dropdown-user-role">{{ Auth::user()->primaryRole() }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown-menu">
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Mon Profil</span>
                        </a>
                        <a href="{{ route('settings.show') }}" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            <span>Paramètres</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Se Déconnecter</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR -->
    <aside class="sidebar no-print" id="sidebar">
        <!-- Scrollable Menu Content -->
        <div class="sidebar-menu">
            <div class="sidebar-brand-mobile">SDCC</div>

            <!-- Employee Menu (only for employees) -->
            @if(Auth::user() && Auth::user()->hasRole('employee'))
        <div class="sidebar-section">
            <div class="sidebar-section-title">Menu Principal</div>
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="sidebar-link @if(Route::currentRouteName() == 'dashboard') active @endif">
                    <i class="fas fa-chart-line"></i>
                    <span class="sidebar-link-text">Tableau de bord</span>
                </a>
                <a href="{{ route('mes-demandes.index') }}" class="sidebar-link @if(Route::currentRouteName() == 'mes-demandes.index' || Route::currentRouteName() == 'mes-demandes.show') active @endif">
                    <i class="fas fa-list-check"></i>
                    <span class="sidebar-link-text">Mes demandes</span>
                </a>
                <a href="{{ route('mes-demandes.create') }}" class="sidebar-link @if(Route::currentRouteName() == 'mes-demandes.create') active @endif">
                    <i class="fas fa-plus-circle"></i>
                    <span class="sidebar-link-text">Nouvelle demande</span>
                </a>
                <a href="{{ route('cars.index') }}" class="sidebar-link @if(Route::currentRouteName() == 'cars.index') active @endif">
                    <i class="fas fa-car"></i>
                    <span class="sidebar-link-text">Véhicules</span>
                </a>
                <a href="{{ route('calendrier') }}" class="sidebar-link @if(Route::currentRouteName() == 'calendrier') active @endif">
                    <i class="fas fa-calendar"></i>
                    <span class="sidebar-link-text">Calendrier</span>
                </a>
            </nav>
        </div>
        @endif

        <!-- Admin Menu -->
        @if(Auth::user() && Auth::user()->hasAnyRole(['admin', 'super_admin']))
        <div class="sidebar-section">
            <div class="sidebar-section-title">Administration</div>
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="sidebar-link @if(Route::currentRouteName() == 'dashboard') active @endif">
                    <i class="fas fa-chart-line"></i>
                    <span class="sidebar-link-text">Tableau de bord</span>
                </a>
                <div class="sidebar-group js-sidebar-group">
                    <button type="button"
                        class="sidebar-link sidebar-group-toggle js-sidebar-group-toggle"
                        data-no-close="1"
                        aria-expanded="false">
                        <i class="fas fa-database"></i>
                        <span class="sidebar-link-text">Gestion des données</span>
                        <i class="fas fa-chevron-right sidebar-group-caret"></i>
                    </button>
                    <div class="sidebar-submenu">
                        <a href="{{ route('admin.data.kilometrage') }}"
                           class="sidebar-sublink @if(Route::is('admin.data.kilometrage')) active @endif">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="sidebar-link-text">Kilométrage</span>
                        </a>
                        <a href="{{ route('admin.data.requests') }}"
                           class="sidebar-sublink @if(Route::is('admin.data.requests')) active @endif">
                            <i class="fas fa-file-alt"></i>
                            <span class="sidebar-link-text">Demandes</span>
                        </a>
                        <a href="{{ route('admin.data.reservations') }}"
                           class="sidebar-sublink @if(Route::is('admin.data.reservations')) active @endif">
                            <i class="fas fa-calendar-check"></i>
                            <span class="sidebar-link-text">Réservations</span>
                        </a>
                        <a href="{{ route('admin.data.notifications') }}"
                           class="sidebar-sublink @if(Route::is('admin.data.notifications')) active @endif">
                            <i class="fas fa-bell"></i>
                            <span class="sidebar-link-text">Notifications</span>
                        </a>
                        {{-- Users tab in admin data removed per request --}}
                    </div>
                </div>
                <a href="{{ route('planification') }}" class="sidebar-link @if(Route::currentRouteName() == 'planification') active @endif">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="sidebar-link-text">Planification</span>
                </a>
                <a href="{{ route('zones.index') }}" class="sidebar-link @if(Route::currentRouteName() == 'zones.index' || Route::currentRouteName() == 'zones.create' || Route::currentRouteName() == 'zones.edit') active @endif">
                    <i class="fas fa-map-location-dot"></i>
                    <span class="sidebar-link-text">Zones</span>
                </a>
                <a href="{{ route('calendrier') }}" class="sidebar-link @if(Route::currentRouteName() == 'calendrier') active @endif">
                    <i class="fas fa-calendar"></i>
                    <span class="sidebar-link-text">Calendrier</span>
                </a>
                <a href="{{ route('cars.index') }}" class="sidebar-link @if(Route::currentRouteName() == 'cars.index' || Route::currentRouteName() == 'cars.show' || Route::currentRouteName() == 'cars.create' || Route::currentRouteName() == 'cars.edit') active @endif">
                    <i class="fas fa-car"></i>
                    <span class="sidebar-link-text">Véhicules</span>
                </a>

                <!-- Gestion des Utilisateurs - Super Admin only -->
                @can('super_admin')
                <a href="{{ route('utilisateurs.index') }}" class="sidebar-link @if(Route::currentRouteName() == 'utilisateurs.index' || Route::currentRouteName() == 'utilisateurs.store' || Route::currentRouteName() == 'utilisateurs.update' || Route::currentRouteName() == 'utilisateurs.destroy') active @endif">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-link-text">Gestion des Utilisateurs</span>
                </a>
                @endcan
            </nav>
        </div>
        @endif
        </div>
        <!-- End Scrollable Menu Content -->

        <div class="sidebar-user-section">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->name ?? 'User' }}</div>
                <div class="sidebar-user-role">{{ Auth::user()->primaryRole() }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="sidebar-logout" title="Se déconnecter">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </aside>
    </div> 

    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- MAIN CONTENT -->
    <main class="main-content" id="mainContent">
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            const notificationsBtn = document.getElementById('notificationsBtn');
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            const notificationsBadge = document.getElementById('notificationsBadge');
            const notificationsList = document.getElementById('notificationsList');
            const markAllNotificationsBtn = document.getElementById('markAllNotificationsBtn');

            const renderNotifications = (items) => {
                if (!items.length) {
                    notificationsList.innerHTML = '<div class="notifications-empty">Aucune notification.</div>';
                    return;
                }

                notificationsList.innerHTML = items.map((item) => `
                    <div class="notification-item ${item.read_at ? '' : 'unread'}" data-id="${item.id}" data-url="${item.url}">
                        <div class="notification-title">${item.title}</div>
                        <div class="notification-message">${item.message}</div>
                        <div class="notification-meta">${item.created_at ?? ''}</div>
                    </div>
                `).join('');
            };

            const updateBadge = (count) => {
                notificationsBadge.textContent = count > 99 ? '99+' : String(count);
                notificationsBadge.classList.toggle('show', count > 0);
            };

            const loadNotifications = async () => {
                try {
                    const response = await fetch('{{ route('notifications.index') }}');
                    if (!response.ok) {
                        return;
                    }
                    const data = await response.json();
                    updateBadge(data.unread_count || 0);
                    renderNotifications(data.notifications || []);
                } catch (e) {
                    // Ignore fetch failure silently.
                }
            };

            // ── Sidebar state persistence via localStorage ──────────────────────────
            const SIDEBAR_KEY = 'sdcc_sidebar_collapsed';

            // Restore saved state on page load (desktop only)
            if (window.innerWidth > 768) {
                const savedState = localStorage.getItem(SIDEBAR_KEY);
                if (savedState === 'true') {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    sidebarToggle.classList.add('active');
                }
            } else {
                // Always start collapsed on mobile
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }

            // Sidebar toggle functionality
            sidebarToggle.addEventListener('click', function() {
                const willOpen = sidebar.classList.contains('collapsed');
                sidebar.classList.toggle('collapsed');
                sidebarOverlay.classList.toggle('active');
                mainContent.classList.toggle('expanded');
                sidebarToggle.classList.toggle('active');
                document.body.classList.toggle('sidebar-open', willOpen);

                // Persist state (desktop only)
                if (window.innerWidth > 768) {
                    localStorage.setItem(SIDEBAR_KEY, !willOpen ? 'true' : 'false');
                }

                // Always reveal sidebar content from the top when opening.
                if (willOpen) {
                    sidebar.scrollTop = 0;
                }
            });

            // Close sidebar when overlay is clicked
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.add('collapsed');
                sidebarOverlay.classList.remove('active');
                mainContent.classList.remove('expanded');
                sidebarToggle.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            });


            // Profile dropdown toggle
            profileBtn.addEventListener('click', function() {
                profileDropdown.classList.toggle('active');
                notificationsDropdown.classList.remove('active');
            });

            notificationsBtn.addEventListener('click', function() {
                notificationsDropdown.classList.toggle('active');
                profileDropdown.classList.remove('active');

                if (notificationsDropdown.classList.contains('active')) {
                    loadNotifications();
                }
            });

            notificationsList.addEventListener('click', async function(event) {
                const item = event.target.closest('.notification-item');
                if (!item) {
                    return;
                }

                const id = item.dataset.id;
                const url = item.dataset.url;

                try {
                    await fetch(`{{ url('/notifications') }}/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                } catch (e) {
                    // Continue navigation even if read update fails.
                }

                window.location.href = url;
            });

            markAllNotificationsBtn.addEventListener('click', async function() {
                try {
                    await fetch('{{ route('notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    await loadNotifications();
                } catch (e) {
                    // Ignore failures silently.
                }
            });

            // Close profile dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.navbar-profile')) {
                    profileDropdown.classList.remove('active');
                }
                if (!event.target.closest('.navbar-notifications')) {
                    notificationsDropdown.classList.remove('active');
                }
            });

            // Close sidebar when a link is clicked on mobile
            const sidebarLinks = document.querySelectorAll('.sidebar-link, .sidebar-sublink');
            const mobileView = window.innerWidth <= 768;

            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (this.dataset && this.dataset.noClose === '1') return;
                    if (mobileView) {
                        sidebar.classList.add('collapsed');
                        sidebarOverlay.classList.remove('active');
                        mainContent.classList.remove('expanded');
                        sidebarToggle.classList.remove('active');
                        document.body.classList.remove('sidebar-open');
                    }
                });
            });

            // Sidebar groups (collapsible submenu)
            document.querySelectorAll('.js-sidebar-group').forEach((group) => {
                const toggle = group.querySelector('.js-sidebar-group-toggle');
                if (!toggle) return;

                const setExpanded = (isOpen) => {
                    group.classList.toggle('open', isOpen);
                    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                };

                // Auto-open when a sublink is active
                const hasActive = !!group.querySelector('.sidebar-sublink.active');
                setExpanded(hasActive);

                toggle.addEventListener('click', function () {
                    setExpanded(!group.classList.contains('open'));
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('collapsed');
                    sidebarOverlay.classList.remove('active');
                    mainContent.classList.remove('expanded');
                    sidebarToggle.classList.remove('active');
                    document.body.classList.remove('sidebar-open');
                }
            });

            loadNotifications();

        });
    </script>

    <!-- ==================== ENHANCED POPUP NOTIFICATIONS ==================== -->
    <script>
        // Global toast notification function for SweetAlert2
        function showToast(type = 'info', message = '') {
            const icons = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icons[type] || 'info',
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        }

        // Global modal notification function for validation errors
        function showValidationErrors(errors) {
            const html = errors.map(msg => 
                `<p style="text-align: left; margin: 12px 0; font-size: 14px;">
                    <i class="fas fa-circle" style="color: #e53935; font-size: 6px; margin-right: 8px; vertical-align: middle;"></i>${msg}
                </p>`
            ).join('');

            Swal.fire({
                icon: 'error',
                title: 'Erreur de validation',
                html: html,
                confirmButtonText: 'Corriger',
                confirmButtonColor: '#e53935',
                allowOutsideClick: false,
                allowEscapeKey: true,
                customClass: {
                    container: 'validation-error-modal',
                    popup: 'validation-error-popup'
                }
            });
        }

        // Handle session messages on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast('success', "{{ session('success') }}");
            @endif

            @if(session('error'))
                showToast('error', "{{ session('error') }}");
            @endif

            @if(session('warning'))
                showToast('warning', "{{ session('warning') }}");
            @endif

            @if(session('info'))
                showToast('info', "{{ session('info') }}");
            @endif

            // Handle validation errors as modal (not toast)
            @if($errors->any())
                const errors = [
                    @foreach($errors->all() as $error)
                        "{{ $error }}",
                    @endforeach
                ];
                showValidationErrors(errors);
            @endif
        });

        // Optional: Add custom CSS for toast animations
        const style = document.createElement('style');
        style.textContent = `
            .swal2-toast {
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
                border-radius: 8px;
            }

            .swal2-popup.swal2-toast .swal2-title {
                font-size: 14px;
                font-weight: 500;
                margin: 0;
            }

            .swal2-popup.swal2-toast .swal2-icon {
                margin: 0 12px 0 0;
                width: 28px;
                min-width: 28px;
                height: 28px;
                line-height: 28px;
            }

            .swal2-timer-progress-bar {
                height: 3px;
                background: linear-gradient(90deg, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0.3) 100%);
            }

            /* Success toast styling */
            .swal2-toast.swal2-icon-success {
                background: #ecfdf5;
                border: 1px solid #d1fae5;
            }

            .swal2-toast.swal2-icon-success .swal2-title {
                color: #065f46;
            }

            .swal2-toast.swal2-icon-success .swal2-icon {
                border-color: #10b981;
                color: #10b981;
            }

            /* Error toast styling */
            .swal2-toast.swal2-icon-error {
                background: #fef2f2;
                border: 1px solid #fee2e2;
            }

            .swal2-toast.swal2-icon-error .swal2-title {
                color: #7f1d1d;
            }

            .swal2-toast.swal2-icon-error .swal2-icon {
                border: none !important;
                background: linear-gradient(135deg, #ef4444, #dc2626) !important;
                color: white !important;
                box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3) !important;
            }

            .swal2-toast.swal2-icon-error .swal2-icon [class^=swal2-x-mark-line] {
                background-color: white !important;
            }

            /* Warning toast styling */
            .swal2-toast.swal2-icon-warning {
                background: #fffbeb;
                border: 1px solid #fef3c7;
            }

            .swal2-toast.swal2-icon-warning .swal2-title {
                color: #78350f;
            }

            .swal2-toast.swal2-icon-warning .swal2-icon {
                border-color: #fbbf24;
                color: #fbbf24;
            }

            /* Info toast styling */
            .swal2-toast.swal2-icon-info {
                background: #eff6ff;
                border: 1px solid #dbeafe;
            }

            .swal2-toast.swal2-icon-info .swal2-title {
                color: #0c2340;
            }

            .swal2-toast.swal2-icon-info .swal2-icon {
                border-color: #3b82f6;
                color: #3b82f6;
            }

            /* ======================== CUSTOM TOAST NOTIFICATIONS ======================== */
            
            .toast-container {
                position: fixed;
                top: 100px;
                right: 25px;
                z-index: 10000;
                display: flex;
                flex-direction: column;
                gap: 12px;
                pointer-events: none;
            }

            .toast {
                display: flex;
                align-items: center;
                gap: 14px;
                background: white;
                border-radius: 10px;
                padding: 14px 18px;
                min-width: 320px;
                max-width: 450px;
                box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
                border-left: 5px solid currentColor;
                pointer-events: auto;
                opacity: 0;
                transform: translateX(100px);
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                font-size: 14px;
                backdrop-filter: blur(10px);
                animation: toastSlideIn 0.35s ease-out forwards;
            }

            .toast.show {
                opacity: 1;
                transform: translateX(0);
            }

            .toast.hide {
                opacity: 0;
                transform: translateX(100px);
                animation: none;
            }

            @keyframes toastSlideIn {
                from {
                    opacity: 0;
                    transform: translateX(100px) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translateX(0) scale(1);
                }
            }

            /* Toast Icon */
            .toast-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-size: 20px;
                width: 28px;
                height: 28px;
            }

            /* Toast Content */
            .toast-content {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 3px;
            }

            .toast-title {
                font-weight: 600;
                color: #1a2332;
                font-size: 14px;
                line-height: 1.3;
            }

            .toast-message {
                color: #555;
                font-size: 13px;
                line-height: 1.4;
                font-weight: 400;
            }

            /* Toast Close Button */
            .toast-close {
                flex-shrink: 0;
                background: none;
                border: none;
                color: #999;
                font-size: 16px;
                cursor: pointer;
                padding: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
                border-radius: 4px;
            }

            .toast-close:hover {
                background: rgba(0, 0, 0, 0.05);
                color: #333;
                transform: scale(1.15);
            }

            .toast-close:active {
                transform: scale(0.95);
            }

            /* Toast Progress Bar */
            .toast-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                height: 3px;
                border-radius: 0 0 10px 0;
                animation: progressBar linear;
            }

            @keyframes progressBar {
                from { width: 100%; }
                to { width: 0%; }
            }

            /* Toast Types - Success */
            .toast-success {
                border-left-color: #00d084;
                background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            }

            .toast-success .toast-title {
                color: #0c5e3e;
            }

            .toast-success .toast-message {
                color: #186e4a;
            }

            /* Toast Types - Error */
            .toast-error {
                border-left-color: #ff4757;
                background: linear-gradient(135deg, #fef2f2 0%, #fde8e8 100%);
            }

            .toast-error .toast-title {
                color: #7f1d1d;
            }

            .toast-error .toast-message {
                color: #a03230;
            }

            /* Toast Types - Warning */
            .toast-warning {
                border-left-color: #ffa500;
                background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            }

            .toast-warning .toast-title {
                color: #78350f;
            }

            .toast-warning .toast-message {
                color: #92400e;
            }

            /* Toast Types - Info */
            .toast-info {
                border-left-color: #0066ff;
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            }

            .toast-info .toast-title {
                color: #0c2340;
            }

            .toast-info .toast-message {
                color: #1e40af;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .toast-container {
                    top: 90px;
                    right: 15px;
                    left: 15px;
                }

                .toast {
                    min-width: unset;
                    max-width: unset;
                    font-size: 13px;
                }

                .toast-icon {
                    font-size: 18px;
                }
            }

            /* Validation error modal styling */
            .validation-error-modal .swal2-popup {
                background: white;
                border: 1px solid #fee2e2;
                box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
            }

            /* Custom error icon - Red circle with white X */
            .validation-error-modal .swal2-icon.swal2-error {
                border: none;
                background: linear-gradient(135deg, #ef4444, #dc2626);
                width: 80px;
                height: 80px;
                line-height: 80px;
                box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
            }

            .validation-error-modal .swal2-icon.swal2-error .swal2-x-mark {
                position: relative;
                flex-grow: 1;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .validation-error-modal .swal2-icon.swal2-error [class^=swal2-x-mark-line] {
                background-color: white !important;
                height: 4px;
                width: 40px;
                border-radius: 2px;
            }

            .swal2-toast.swal2-icon-error .swal2-icon.swal2-error {
                border: none;
                background: linear-gradient(135deg, #ef4444, #dc2626);
                box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
            }

            .swal2-toast.swal2-icon-error .swal2-icon.swal2-error [class^=swal2-x-mark-line] {
                background-color: white !important;
            }

            .validation-error-modal .swal2-title {
                color: #991b1b;
                font-size: 18px;
                font-weight: 600;
            }

            /* Toast animation improvements */
            .swal2-toast.swal2-show {
                animation: slideInRight 0.3s ease-out;
            }

            @keyframes slideInRight {
                from {
                    transform: translateX(100px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            .swal2-toast.swal2-hide {
                animation: slideOutRight 0.3s ease-in;
            }

            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100px);
                    opacity: 0;
                }
            }

            /* Global error icon styling - Red circle with white X */
            .swal2-icon.swal2-error {
                border: none !important;
                background: linear-gradient(135deg, #ef4444, #dc2626) !important;
                color: white !important;
                box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3) !important;
            }

            .swal2-icon.swal2-error [class^=swal2-x-mark-line] {
                background-color: white !important;
            }
        `;
        document.head.appendChild(style);
    </script>
    <div class="print-footer" aria-hidden="true">
        <span class="page-number"></span>
    </div>
</body>
</html>
