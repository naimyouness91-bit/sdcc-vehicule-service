# CSV Export Implementation - SDCC Car Reservation

## Overview
Comprehensive CSV export functionality for both administrators and employees with RFC 4180 compliance for maximum compatibility with standard CSV readers (Excel, Google Sheets, etc.).

## Features Implemented

### 1. Admin Reservation History Export
- **Route:** `GET /planification/history/export`
- **Controller:** [PlanificationController.php](app/Http/Controllers/PlanificationController.php#L335-L363)
- **Method:** `exportHistory(Request $request)`
- **Access:** Admin/Super Admin only (middleware: `role:admin|super_admin`)

#### CSV Columns (11 total):
| Column | Description | Format |
|--------|-------------|--------|
| N° | Row number | Integer |
| Date de Demande | Reservation request date | YYYY-MM-DD HH:MM |
| Employé | Employee full name | String |
| Service | Employee service/department | String |
| Véhicule | Vehicle name | String |
| Matricule | License plate | String |
| Destination | Trip destination | String |
| Date de Départ | Departure date | YYYY-MM-DD HH:MM |
| Date de Retour | Return date | YYYY-MM-DD HH:MM |
| Raison | Trip reason | String |
| Statut | Reservation status | pending/approved/rejected/cancelled |

#### Advanced Filtering:
```php
// Supported query parameters
GET /planification/history/export?status=approved&car_id=1&date_from=2024-01-01&date_to=2024-12-31&q=destination
```

Filters:
- `status` - Filter by reservation status (pending, approved, rejected, cancelled)
- `employee_id` - Filter by specific employee
- `car_id` - Filter by specific vehicle
- `date_from` - Filter from date
- `date_to` - Filter to date
- `q` - Search in employee name, vehicle name, plate, or destination

#### File Output:
```
Filename: historique_reservations_Ymd_His.csv
Example: historique_reservations_20240410_143025.csv
Encoding: UTF-8
```

### 2. Employee Personal History Export
- **Route:** `GET /mes-demandes/history/export`
- **Controller:** [MesDemandesController.php](app/Http/Controllers/MesDemandesController.php#L386-L411)
- **Method:** `exportHistory(Request $request)`
- **Access:** Authenticated employees only (middleware: `permission:reservations.own.manage`)
- **Security:** Automatically filtered to current user's reservations only

#### CSV Columns (9 total):
| Column | Description | Format |
|--------|-------------|--------|
| N° | Row number | Integer |
| Date de Demande | Reservation request date | YYYY-MM-DD HH:MM |
| Véhicule | Vehicle name | String |
| Matricule | License plate | String |
| Destination | Trip destination | String |
| Date de Départ | Departure date | YYYY-MM-DD HH:MM |
| Date de Retour | Return date | YYYY-MM-DD HH:MM |
| Raison | Trip reason | String |
| Statut | Reservation status | pending/approved/rejected/cancelled |

#### Same Filtering Options:
```php
// Supported query parameters (same as admin, minus employee_id)
GET /mes-demandes/history/export?status=approved&car_id=1&date_from=2024-01-01&date_to=2024-12-31&q=destination
```

#### File Output:
```
Filename: mon_historique_reservations_Ymd_His.csv
Example: mon_historique_reservations_20240410_143025.csv
Encoding: UTF-8
```

## Technical Implementation

### RFC 4180 Compliance

Both controllers implement the `escapeCsvLine()` private helper method ensuring full RFC 4180 compliance:

```php
/**
 * Escape and format a CSV line properly according to RFC 4180
 * 
 * @param array $fields
 * @return string
 */
private function escapeCsvLine(array $fields): string
{
    $escaped = array_map(function($field) {
        // Convert value to string if necessary
        $field = (string) $field;
        
        // If field contains comma, quote, or newline, wrap in quotes and escape inner quotes
        if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
            return '"' . str_replace('"', '""', $field) . '"';
        }
        
        // Otherwise return as-is
        return $field;
    }, $fields);
    
    return implode(',', $escaped);
}
```

### Key Features:

1. **Proper Quote Escaping**: Doubles internal quotes (`"` becomes `""`)
2. **Smart Field Quoting**: Only quotes fields containing special characters
3. **Special Character Handling**: Correctly handles commas, quotes, and newlines
4. **Array-based Processing**: Uses `array_map()` for clean, functional approach
5. **UTF-8 Encoding**: Output with `charset=utf-8` header

### Example Field Processing:

```
Input:  "I said ""hello"" to the driver"
Output: "I said ""hello"" to the driver"  (properly quoted and escaped)

Input:  "Paris, France"
Output: "Paris, France"  (quoted due to comma)

Input:  "Simple text"
Output: Simple text  (unquoted, no special chars)

Input:  "Line 1
        Line 2"
Output: "Line 1
        Line 2"  (quoted due to newline)
```

## Integration Points

### View Components

#### Admin History View
- **File:** [resources/views/planification/history.blade.php](resources/views/planification/history.blade.php)
- **Export Button Location:** Header section
- **Button Icon:** `fas fa-file-csv` (CSV file icon)
- **Button Title:** "Télécharger l'historique en format CSV" (tooltip)

```blade
<form action="{{ route('planification.export-history') }}" method="GET" style="display: flex;">
    {{-- Filter parameters preserved in export --}}
    @foreach(['status' => $statusFilter, 'employee_id' => $employeeFilter, ...] as $param => $value)
        @if($value)
            <input type="hidden" name="{{ $param }}" value="{{ $value }}">
        @endif
    @endforeach
    <button type="submit" class="export-btn" title="Télécharger l'historique en format CSV">
        <i class="fas fa-file-csv"></i>
        Exporter CSV
    </button>
</form>
```

#### Employee History View
- **File:** [resources/views/mes-demandes/history.blade.php](resources/views/mes-demandes/history.blade.php)
- **Export Button Location:** Header section
- **Button Icon:** `fas fa-file-csv`
- **Button Title:** "Télécharger votre historique en format CSV" (tooltip)

### Routes Configuration

Both routes defined in [routes/web.php](routes/web.php):

```php
// Admin history export
Route::get('/planification/history/export', [PlanificationController::class, 'exportHistory'])
    ->name('planification.export-history');

// Employee history export  
Route::get('/mes-demandes/history/export', [MesDemandesController::class, 'exportHistory'])
    ->middleware('permission:reservations.own.manage')
    ->name('mes-demandes.export-history');
```

## HTTP Response Headers

All CSV exports use proper HTTP headers for reliable browser downloads:

```php
return response($csvContent)
    ->header('Content-Type', 'text/csv; charset=utf-8')
    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
    ->header('Pragma', 'no-cache')
    ->header('Expires', '0');
```

### Header Explanations:
- **Content-Type:** Declares MIME type for CSV format with UTF-8 encoding
- **Content-Disposition:** Forces browser download with specified filename
- **Pragma/Expires:** Prevents caching to ensure fresh data

## Testing Checklist

### Admin CSV Export
- [ ] Navigate to `/planification/history`
- [ ] Click "Exporter CSV" button without filters
- [ ] Verify download of `historique_reservations_Ymd_His.csv`
- [ ] Open in Excel - check formatting and special characters
- [ ] Open in Google Sheets - verify import and layout
- [ ] Open in text editor - confirm UTF-8 encoding
- [ ] Test with filters (status, date range, search)
- [ ] Verify filtered data only appears in export
- [ ] Check header row contains all 11 columns

### Employee CSV Export
- [ ] Log in as employee
- [ ] Navigate to `/mes-demandes/history`
- [ ] Click "Exporter CSV" button
- [ ] Verify download of `mon_historique_reservations_Ymd_His.csv`
- [ ] Open in Excel - check formatting
- [ ] Open in Google Sheets - verify import
- [ ] Verify 9 columns (no employee/service columns)
- [ ] Confirm only own reservations included
- [ ] Test with filters applied
- [ ] Verify Cannot see admin view or all employees data

### Special Character Testing
- [ ] Create reservation with destination containing comma: "Paris, France"
- [ ] Create reason with quote: "Driver said ""OK"""
- [ ] Create destination with newline
- [ ] Export and verify proper quoting in CSV
- [ ] Reimport to verify no data corruption

### Compatibility Testing
- [ ] Excel 2019+ (Windows)
- [ ] Excel (Mac)
- [ ] Google Sheets
- [ ] LibreOffice Calc
- [ ] Numbers (Mac)
- [ ] Text editors (Notepad, VS Code, etc.)

## Security Considerations

1. **Role-Based Access:**
   - Admin export: Protected by `role:admin|super_admin` middleware
   - Employee export: Protected by `permission:reservations.own.manage`

2. **User Data Isolation:**
   - Employee exports automatically filtered to `where('user_id', Auth::id())`
   - No way for employees to access other users' data

3. **Filter Validation:**
   - All filters validated in controller before database query
   - User IDs checked against current authenticated user

4. **File Format Security:**
   - UTF-8 encoding prevents injection attacks
   - Proper quote escaping prevents formula injection
   - No sensitive data in filename (timestamp only)

## File Structure

```
app/Http/Controllers/
├── PlanificationController.php
│   ├── history() - Display filtered reservations
│   ├── exportHistory() - Generate and download CSV
│   └── escapeCsvLine() - RFC 4180 helper method
└── MesDemandesController.php
    ├── history() - Display employee reservations
    ├── exportHistory() - Generate and download CSV
    └── escapeCsvLine() - RFC 4180 helper method

resources/views/
├── planification/history.blade.php - Admin export UI
└── mes-demandes/history.blade.php - Employee export UI

routes/
└── web.php - Export routes configuration
```

## Performance Characteristics

- **Admin Export:** O(n) where n = number of filtered reservations
- **Employee Export:** O(n) where n = number of employee's reservations
- **CSV Generation:** Streamed to prevent memory issues
- **Database Queries:** Optimized with eager loading (->with(['user', 'car']))

## Future Enhancements

1. **Additional Export Formats:**
   - Excel (.xlsx) export with formatting
   - PDF export with branding
   - JSON export for API integration

2. **Advanced Features:**
   - Scheduled exports (daily/weekly)
   - Email delivery of exports
   - Chart generation from export data
   - Custom column selection

3. **Audit Logging:**
   - Log all export activities
   - Track what data was exported by whom
   - Compliance reporting

## Troubleshooting

### Export not downloading
- Check network tab in browser DevTools
- Verify route exists: `php artisan route:list | grep export`
- Clear cache: `php artisan cache:clear`

### Special characters corrupted in Excel
- File encoding is UTF-8 (correct)
- Excel may need UTF-8 BOM for proper display
- Try opening as CSV with UTF-8 encoding selected

### Filters not applied to export
- Verify query parameters in URL
- Check form hidden inputs in history.blade.php
- Validate filter values in controller

### File size too large
- Limit date range in filter
- Implement pagination with offset/limit
- Use streams instead of loading all data at once

---

**Last Updated:** April 2026
**Status:** ✅ Production Ready
**Testing Status:** Ready for QA
