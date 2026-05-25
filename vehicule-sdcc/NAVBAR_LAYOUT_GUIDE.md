# Modern Top Navbar & Sidebar Layout - Implementation Guide

## Overview
A reusable, modern SaaS-style layout for the SDCC Car Reservation system featuring:
- **Fixed top navbar** with logo, hamburger toggle, and user profile menu
- **Collapsible sidebar** with smooth animations
- **Green & Orange color scheme** throughout
- **Responsive design** (desktop, tablet, mobile)
- **Global layout** for use across all pages

---

## Features

### Top Navbar
- ✅ Fixed at top (70px height)
- ✅ Hamburger menu toggle on left
- ✅ SDCC logo and brand name
- ✅ User profile dropdown on right
- ✅ Smooth transitions and hover effects
- ✅ Responsive (adapts to mobile)

### Sidebar
- ✅ Collapsible with smooth slide animation
- ✅ Navigation links with active states
- ✅ Admin section (only visible to admin users)
- ✅ User info section at bottom
- ✅ Smooth hover effects with animations
- ✅ Mobile overlay to close sidebar

### Profile Dropdown
- ✅ User name and role display
- ✅ Profile link
- ✅ Settings link
- ✅ Logout button
- ✅ Smooth open/close animation

---

## How to Use

### 1. Update Existing Pages

To use the new layout, simply extend it at the top of your Blade view:

```blade
@extends('layouts.app')

@section('title', 'Your Page Title - SDCC')

@section('content')
    <!-- Your content here -->
@endsection
```

### 2. Example Page Structure

```blade
@extends('layouts.app')

@section('title', 'Cars - SDCC')

@section('content')
    <div class="breadcrumb">
        <div class="breadcrumb-item">
            <i class="fas fa-home"></i>
            <a href="{{ route('dashboard') }}">Accueil</a>
        </div>
        <div class="breadcrumb-item">
            <i class="fas fa-chevron-right"></i>
        </div>
        <div class="breadcrumb-item active">Véhicules</div>
    </div>

    <h1 class="content-title">Gestion des Véhicules</h1>

    <div style="background: white; border-radius: 12px; padding: 30px;">
        <!-- Your page content -->
    </div>
@endsection
```

---

## Customization

### Add New Sidebar Links

Edit `/resources/views/layouts/app.blade.php` and add links in the sidebar sections:

```blade
<div class="sidebar-section">
    <div class="sidebar-section-title">Menu Principal</div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="sidebar-link">
            <i class="fas fa-chart-line"></i>
            <span class="sidebar-link-text">Tableau de Bord</span>
        </a>
        
        <!-- Add your new link here -->
        <a href="{{ route('your.route') }}" class="sidebar-link">
            <i class="fas fa-icon-name"></i>
            <span class="sidebar-link-text">Your Link</span>
        </a>
    </nav>
</div>
```

### Add Badges to Links

```blade
<a href="#" class="sidebar-link">
    <i class="fas fa-bell"></i>
    <span class="sidebar-link-text">Notifications</span>
    <span class="sidebar-link-badge">5</span>
</a>
```

### Active Route Detection

The layout automatically detects the current route and highlights it:

```blade
class="sidebar-link @if(Route::currentRouteName() == 'dashboard') active @endif"
```

---

## Layout Structure

```
┌─────────────────────────────────────┐
│  Top Navbar (Fixed, 70px)           │
├──────────────┬──────────────────────┤
│   Sidebar    │                      │
│ (Collapsible)│  Main Content        │
│              │                      │
│              │                      │
│              │                      │
│              │                      │
└──────────────┴──────────────────────┘
```

---

## Color Scheme

### Primary Colors
- **Green:** #2E7D32 (Dark Green)
- **Light Green:** #4CAF50, #66BB6A
- **Orange:** #FFA726, #FF6F00

### Typography
- **Font Family:** System Fonts (Segoe UI, Roboto, etc.)
- **Title:** 28px, Font-Weight: 800, Gradient (Green → Orange)
- **Links:** 14px, Font-Weight: 500

### Spacing
- **Navbar Height:** 70px
- **Sidebar Width:** 260px
- **Main Content Padding:** 30px (desktop), 20px (mobile)

---

## Responsive Behavior

### Desktop (> 768px)
- Sidebar always visible
- Main content adjusts with left margin
- Full navbar visible

### Tablet (481px - 768px)
- Sidebar hidden by default
- Hamburger menu shows
- Click to toggle sidebar
- Overlay appears when sidebar is open

### Mobile (< 480px)
- Narrower navbar (60px height)
- Narrower sidebar (75vw, max 260px)
- Smaller icons and text
- Touch-friendly buttons

---

## JavaScript Functions

### Sidebar Toggle
```javascript
// Automatically handled by the layout
// Just click the hamburger button to toggle
```

### Profile Dropdown
```javascript
// Automatically handled by the layout
// Click the profile icon to open/close
```

### Mobile Auto-Close
```javascript
// Sidebar automatically closes when:
// - A link is clicked on mobile
// - Overlay is clicked
// - Window is resized to desktop view
```

---

## CSS Classes

### Useful Classes for Custom Pages

```css
.content-title        /* Page title with gradient */
.breadcrumb           /* Breadcrumb navigation */
.breadcrumb-item      /* Individual breadcrumb item */
.breadcrumb-item.active /* Active breadcrumb */
```

### Example Card Style

```html
<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
    <!-- Card content -->
</div>
```

---

## Authentication

The layout uses Laravel's `Auth::user()` to display user information:

```blade
Auth::user()->first_name          /* User's first name */
Auth::user()->role                /* User's role (admin, user, etc.) */
route('logout')                   /* Logout route */
route('dashboard')                /* Dashboard route */
```

Ensure your User model has `first_name` and `role` attributes.

---

## Routes Required

The layout expects these routes to exist:

```php
route('dashboard')          /* Home/Dashboard */
route('cars.index')         /* Vehicles list */
route('reservations.index') /* Reservations list */
route('users.index')        /* Users (admin only) */
route('logout')            /* Logout form submission */
```

Add these to your `routes/web.php` if they don't exist.

---

## Browser Compatibility

- ✅ Chrome/Edge (Latest)
- ✅ Firefox (Latest)
- ✅ Safari (Latest)
- ✅ Mobile browsers

---

## Performance

- **CSS-in-HTML:** Minimal HTTP requests
- **Transitions:** GPU-accelerated for smooth animations
- **Responsive:** Mobile-first design
- **Accessibility:** ARIA labels included

---

## Notes

1. The sidebar is **NOT visible by default on mobile** - users must click the hamburger menu
2. The sidebar **automatically persists on desktop** view (> 768px)
3. The profile dropdown **closes when clicking outside**
4. All routes should be defined in your `routes/web.php`
5. The layout requires Font Awesome 6.4.0 for icons

---

## Next Steps

1. Replace your existing page templates with the new layout
2. Update routes to match the layout expectations
3. Customize colors/fonts if needed (modify CSS in layout file)
4. Test on mobile/tablet devices
5. Add additional sidebar links as needed

---

## Support

For issues or questions:
- Check the example page: `resources/views/example-page.blade.php`
- Review layout code: `resources/views/layouts/app.blade.php`
- Test with: `php artisan serve` at `http://localhost:8000`
