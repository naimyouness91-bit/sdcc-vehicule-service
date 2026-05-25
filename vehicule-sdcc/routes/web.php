<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlanificationController;
use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\MesDemandesController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\UtilisateursController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminReservationsController;
use App\Http\Controllers\Admin\AdminDataManagementController;
use App\Http\Controllers\Admin\PdfReportController;
use App\Http\Controllers\Admin\PrintController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\DataEntryController;
use App\Http\Controllers\ExcelManagementController;
use App\Http\Controllers\ServicePublicController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Public Services Routes (no auth required)
Route::get('/services', [ServicePublicController::class, 'index'])->name('services.index');

// Provide a top-level /admin entry that redirects to data-management
Route::get('/admin', function () {
    return redirect()->route('admin.data-management');
});

Route::get('/clear-session', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    // Dashboard - accessible to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/settings', [SettingsController::class, 'show'])->name('settings.show');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    
    // Notification Routes - User Isolated
    Route::middleware('can:access-notifications')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });
    
    // Admin/Super Admin Routes
    Route::middleware('role:admin|super_admin')->group(function () {
        // Admin Data Management (Sidebar)
        Route::get('/admin/data-management', [AdminDataManagementController::class, 'index'])->name('admin.data-management');
        Route::get('/admin/tab/{tab}', [AdminDataManagementController::class, 'loadTab'])->name('admin.tab.load');

        // Admin Data Management (Pages)
        Route::prefix('admin/data')->name('admin.data.')->group(function () {
            Route::get('/', [AdminDataManagementController::class, 'index'])->name('index');
            // Legacy URLs — redirect to canonical pages (removed from Gestion des données menu)
            Route::get('/vehicles', function (\Illuminate\Http\Request $request) {
                return redirect()->route('cars.index', $request->query());
            })->name('vehicles');
            Route::get('/zones', function (\Illuminate\Http\Request $request) {
                return redirect()->route('zones.index', $request->query());
            })->name('zones');
            Route::get('/kilometrage', [AdminDataManagementController::class, 'kilometrage'])->name('kilometrage');
            Route::get('/kilometrage/vehicle/{car}', [AdminDataManagementController::class, 'kilometrageVehicle'])->name('kilometrage.vehicle');
            Route::post('/kilometrage/entries', [AdminDataManagementController::class, 'storeKilometrageEntry'])->name('kilometrage.entries.store');
            
            Route::get('/requests', [AdminDataManagementController::class, 'requests'])->name('requests');
            Route::get('/reservations', [AdminDataManagementController::class, 'reservations'])->name('reservations');
            Route::get('/planning-windows', [AdminDataManagementController::class, 'planningWindows'])->name('planning-windows');
            Route::get('/notifications', [AdminDataManagementController::class, 'notifications'])->name('notifications');
            // Users management removed: '/utilisateurs' endpoints disabled per request
        }); // Ferme le groupe admin/data
        
        // Zone Management
        Route::resource('zones', ZoneController::class)->except('show');
        Route::post('/zones/{zone}/assign-users', [ZoneController::class, 'assignUsers'])->name('zones.assign-users');
        Route::post('/zones/{zone}/assign-cars', [ZoneController::class, 'assignCars'])->name('zones.assign-cars');
        Route::get('/api/zones', [ZoneController::class, 'getZones'])->name('api.zones');
        // Data-entry JSON endpoint for zones (server-side pagination)
        Route::get('/data-entry/zones', [ZoneController::class, 'getZones'])->name('data-entry.zones.get');
        Route::get('/api/zones/{zone}/users', [ZoneController::class, 'getZoneUsers'])->name('api.zones.users');

        // Services Management (Admin)
        Route::prefix('admin/services')->name('admin.services.')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('index');
            Route::get('/create', [ServiceController::class, 'create'])->name('create');
            Route::post('/', [ServiceController::class, 'store'])->name('store');
            Route::get('/{service}', [ServiceController::class, 'show'])->name('show');
            Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
            Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
            Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
        });
        Route::get('/api/services/grouped', [ServiceController::class, 'grouped'])->name('api.services.grouped');

        Route::get('/planification', [PlanificationController::class, 'index'])->name('planification');
        // Kilometrage page and manual check
        Route::get('/kilometrage', [\App\Http\Controllers\KilometrageController::class, 'index'])->name('kilometrage.index');
        Route::post('/kilometrage/check', [\App\Http\Controllers\KilometrageController::class, 'check'])->name('kilometrage.check');
        Route::get('/planification/export', [PlanificationController::class, 'export'])->name('planification.export');
        Route::get('/planification/live-excel', [PlanificationController::class, 'liveExcel'])->name('planification.live-excel');
        Route::post('/planification/zones', [PlanificationController::class, 'storeZone'])->name('planification.zones.store');
        Route::put('/planification/zones/{zone}', [PlanificationController::class, 'updateZone'])->name('planification.zones.update');
        Route::delete('/planification/zones/{zone}', [PlanificationController::class, 'destroyZone'])->name('planification.zones.destroy');
        Route::post('/planification/zones/{zone}/assign', [PlanificationController::class, 'syncAssignments'])->name('planification.zones.assign');

        Route::post('/planification/windows', [PlanificationController::class, 'storeWindow'])->name('planification.windows.store');
        Route::post('/planification/windows/{window}/toggle', [PlanificationController::class, 'toggleWindow'])->name('planification.windows.toggle');
        Route::delete('/planification/windows/{window}', [PlanificationController::class, 'destroyWindow'])->name('planification.windows.destroy');

        Route::get('/planification/export/excel', [PlanificationController::class, 'exportPlanningExcel'])->name('planification.export.excel');
        Route::get('/planification/export/pdf', [PlanificationController::class, 'exportPlanningPdf'])->name('planification.export.pdf');
        Route::get('/planification/history', [PlanificationController::class, 'history'])->name('planification.history');
        Route::get('/planification/history/filter', [PlanificationController::class, 'filterHistory'])->name('planification.history.filter');
        Route::get('/planification/history/export', [PlanificationController::class, 'exportHistory'])->name('planification.export-history');
        Route::get('/planification/history/export/excel', [PlanificationController::class, 'exportHistoryExcel'])->name('planification.export-history-excel');
        Route::get('/planification/history/export/template', [PlanificationController::class, 'exportHistoryTemplate'])->name('planification.export-history-template');
        
        // Admin Reservations Management
        Route::get('/admin/reservations', [AdminReservationsController::class, 'index'])->name('admin.reservations.index');
        Route::get('/admin/reservations/create', [AdminReservationsController::class, 'create'])->name('admin.reservations.create');
        Route::post('/admin/reservations', [AdminReservationsController::class, 'store'])->name('admin.reservations.store');
        Route::get('/admin/reservations/export/excel', [AdminReservationsController::class, 'export'])->name('admin.reservations.export');
        Route::get('/admin/reservations/api/stats', [AdminReservationsController::class, 'getStats'])->name('admin.reservations.api.stats');
        Route::get('/admin/reservations/{id}', [AdminReservationsController::class, 'show'])->name('admin.reservations.show');
        Route::get('/admin/reservations/{id}/edit', [AdminReservationsController::class, 'edit'])->name('admin.reservations.edit');
        Route::put('/admin/reservations/{id}', [AdminReservationsController::class, 'update'])->name('admin.reservations.update');
        Route::delete('/admin/reservations/{id}', [AdminReservationsController::class, 'destroy'])->name('admin.reservations.destroy');
        Route::post('/admin/reservations/{id}/approve', [AdminReservationsController::class, 'approve'])->name('admin.reservations.approve');
        Route::post('/admin/reservations/{id}/cancel', [AdminReservationsController::class, 'cancel'])->name('admin.reservations.cancel');
        Route::post('/admin/reservations/{id}/status', [AdminReservationsController::class, 'updateStatus'])->name('admin.reservations.update-status');

        
        // Data Entry Management (Admin) - employees endpoints removed
        Route::post('/data-entry/vehicles', [DataEntryController::class, 'storeVehicle'])->name('data-entry.vehicles.store');
        Route::post('/data-entry/kilometrage', [DataEntryController::class, 'storeKilometrage'])->name('data-entry.kilometrage.store');
        Route::get('/data-entry/vehicles', [DataEntryController::class, 'getVehicles'])->name('data-entry.vehicles.get');
        Route::get('/data-entry/kilometrage', [DataEntryController::class, 'getKilometrage'])->name('data-entry.kilometrage.get');
        Route::get('/data-entry/requests', [DataEntryController::class, 'getRequests'])->name('data-entry.requests.get');
        Route::get('/data-entry/reservations', [DataEntryController::class, 'getReservations'])->name('data-entry.reservations.get');
        Route::delete('/data-entry/vehicles/{id}', [DataEntryController::class, 'deleteVehicle'])->name('data-entry.vehicles.delete');
        Route::put('/data-entry/vehicles/{id}', [DataEntryController::class, 'updateVehicle'])->name('data-entry.vehicles.update');
        
        // Excel Management Routes
        Route::prefix('excel')->name('excel.')->group(function () {
            Route::get('/download', [ExcelManagementController::class, 'download'])->name('download');
            Route::post('/upload', [ExcelManagementController::class, 'upload'])->name('upload');
            Route::post('/apply-changes', [ExcelManagementController::class, 'applyChanges'])->name('apply-changes');
            Route::get('/lock/status', [ExcelManagementController::class, 'getLockStatus'])->name('lock.status');
            Route::post('/lock/acquire', [ExcelManagementController::class, 'acquireLock'])->name('lock.acquire');
            Route::post('/lock/release', [ExcelManagementController::class, 'releaseLock'])->name('lock.release');
            Route::post('/lock/force-release', [ExcelManagementController::class, 'forceReleaseLock'])->name('lock.force-release');
            Route::get('/backups', [ExcelManagementController::class, 'listBackups'])->name('backups');
            Route::post('/backups/restore', [ExcelManagementController::class, 'restoreFromBackup'])->name('backups.restore');
            Route::get('/management', [ExcelManagementController::class, 'managementInterface'])->name('management');
        });
        
        // API Routes for Excel Management
        Route::prefix('api/excel')->name('api.excel.')->group(function () {
            Route::get('/info', [ExcelManagementController::class, 'getExcelInfo'])->name('info');
            Route::get('/stats', [ExcelManagementController::class, 'getDataStats'])->name('stats');
        });
        
        // PDF Report Routes (Admin/Super Admin only)
        Route::prefix('pdf')->name('pdf.')->group(function () {
            // Employee PDF report removed
            Route::get('/vehicles', [PdfReportController::class, 'downloadVehiclesReport'])->name('vehicles');
            Route::get('/reservations', [PdfReportController::class, 'downloadReservationsReport'])->name('reservations');
        });
        
        // API Routes for PDF Reports
        Route::prefix('api/pdf')->name('api.pdf.')->group(function () {
            Route::get('/stats', [PdfReportController::class, 'getPdfStats'])->name('stats');
            Route::get('/available', [PdfReportController::class, 'getAvailableReports'])->name('available');
        });
        
        // User management - Utilisateurs Routes (Admin and Super Admin)
        Route::middleware(['auth', 'role:admin|super_admin'])->group(function () {
            Route::get('/utilisateurs', [UtilisateursController::class, 'index'])->name('utilisateurs.index');
            Route::get('/utilisateurs/create', [UtilisateursController::class, 'create'])->name('utilisateurs.create');
            Route::post('/utilisateurs', [UtilisateursController::class, 'store'])->name('utilisateurs.store');
            Route::get('/utilisateurs/{user}/edit', [UtilisateursController::class, 'edit'])->name('utilisateurs.edit');
            Route::get('/utilisateurs/{user}/reset-password', [UtilisateursController::class, 'showResetPasswordForm'])->name('utilisateurs.reset-password-form');
            Route::put('/utilisateurs/{user}', [UtilisateursController::class, 'update'])->name('utilisateurs.update');
            Route::post('/utilisateurs/{user}/deactivate', [UtilisateursController::class, 'deactivate'])->name('utilisateurs.deactivate');
            Route::post('/utilisateurs/{user}/reactivate', [UtilisateursController::class, 'reactivate'])->name('utilisateurs.reactivate');
            Route::delete('/utilisateurs/{user}', [UtilisateursController::class, 'destroy'])->name('utilisateurs.destroy');
            // Rate limited to 5 requests per minute per user (sensitive endpoint)
            Route::post('/utilisateurs/{user}/reset-password', [UtilisateursController::class, 'resetPassword'])
                ->middleware('throttle:5,1')
                ->name('utilisateurs.reset-password');
        });
        
        Route::resource('cars', CarController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy'])
            ->middleware('permission:cars.manage');
        Route::patch('/cars/{id}/status', [CarController::class, 'updateStatus'])
            ->middleware('permission:cars.manage')
            ->name('cars.update-status');
        Route::patch('/cars/{id}/availability', [CarController::class, 'updateAvailability'])
            ->middleware('permission:cars.manage')
            ->name('cars.update-availability');
        Route::post('/mes-demandes/{id}/approve', [MesDemandesController::class, 'approve'])
            ->middleware('permission:reservations.manage')
            ->name('mes-demandes.approve');
        Route::post('/demandes/{id}/approve', [MesDemandesController::class, 'approve'])
            ->middleware('permission:reservations.manage')
            ->name('demandes.approve');
        Route::post('/mes-demandes/{id}/reject', [MesDemandesController::class, 'reject'])
            ->middleware('permission:reservations.manage')
            ->name('mes-demandes.reject');
    });
    
    // Employee Routes - full access to requests
    Route::middleware('role:employee|admin|super_admin')->group(function () {
        Route::get('/mes-demandes', [MesDemandesController::class, 'index'])->middleware('permission:reservations.own.manage')->name('mes-demandes.index');
        Route::get('/mes-demandes/history', [MesDemandesController::class, 'history'])->middleware('permission:reservations.own.manage')->name('mes-demandes.history');
        Route::get('/mes-demandes/history/filter', [MesDemandesController::class, 'filterHistory'])->middleware('permission:reservations.own.manage')->name('mes-demandes.history.filter');
        Route::get('/mes-demandes/history/export', [MesDemandesController::class, 'exportHistory'])->middleware('permission:reservations.own.manage')->name('mes-demandes.export-history');
        Route::get('/mes-demandes/create', [MesDemandesController::class, 'create'])->middleware('permission:reservations.own.manage')->name('mes-demandes.create');
        Route::post('/mes-demandes', [MesDemandesController::class, 'store'])->middleware('permission:reservations.own.manage')->name('mes-demandes.store');
        Route::post('/demandes', [MesDemandesController::class, 'store'])->middleware('permission:reservations.own.manage')->name('demandes.store');
        Route::get('/mes-demandes/{id}', [MesDemandesController::class, 'show'])->middleware('permission:reservations.own.manage')->name('mes-demandes.show');
        Route::post('/mes-demandes/{id}/cancel', [MesDemandesController::class, 'cancelOwn'])->middleware('permission:reservations.own.manage')->name('mes-demandes.cancel');
        Route::put('/mes-demandes/{id}', [MesDemandesController::class, 'update'])->middleware('permission:reservations.own.manage')->name('mes-demandes.update');
        Route::put('/demandes/{id}', [MesDemandesController::class, 'update'])->middleware('permission:reservations.own.manage')->name('demandes.update');
        Route::get('/cars/available', [CarController::class, 'available'])->name('cars.available');
        Route::get('/destinations/suggest', [MesDemandesController::class, 'suggestDestinations'])->middleware('permission:reservations.own.manage')->name('destinations.suggest');
    });
    
    // Shared read-only routes for all authenticated roles
    Route::middleware('role:employee|admin|super_admin')->group(function () {
        Route::get('/calendrier', [CalendrierController::class, 'index'])->name('calendrier');
        Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    });
});

// Authentication Routes
require __DIR__ . '/auth.php';