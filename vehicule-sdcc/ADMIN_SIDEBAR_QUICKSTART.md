# Admin Sidebar - Quick Start Guide

## 🚀 Quick Access

### How to Access
1. **Login** as Admin or Super Admin
2. **Click** "Gestion des Données" in the sidebar (new option!)
3. **OR** Navigate directly to: `/admin/data-management`

### Sidebar Navigation
Once in the Admin Dashboard, you'll see:
- Left sidebar with dark background
- 9 data sections to choose from
- Click any section to load that data table
- All without page refresh!

---

## 📊 Available Data Sections

### 1. **Employés** (Employees)
- View all employees/users
- Filter by service (Commerciale, Technique, RH, etc.)
- Search by name, email, service
- Actions: View, Edit, Delete

### 2. **Véhicules** (Vehicles)
- View all vehicles in the fleet
- Filter by status (Disponible/Maintenance)
- Search by name, license plate, model
- Actions: View, Edit, Delete

### 3. **Kilométrage** (Mileage)
- Track mileage from all trips
- Filter by vehicle
- Search by employee, destination, vehicle
- Shows trip dates and kilometers

### 4. **Demandes** (Pending Requests)
- View all pending reservation requests
- Approve or reject requests
- Search by employee, vehicle, destination
- Quick-action buttons for approval

### 5. **Réservations** (Reservations)
- View all reservations (all statuses)
- Filter by status (Pending/Approved/Rejected/Cancelled)
- Complete reservation details
- Actions: View, Edit, Delete

### 6. **Zones** (Zones)
- View planning zones
- See assigned employees and vehicles count
- Filter by zone status
- Actions: View, Edit, Delete

### 7. **Fenêtres Planification** (Planning Windows)
- View planning time windows
- See date range and duration
- Filter by active/inactive status
- Actions: View, Edit, Delete

### 8. **Notifications**
- View all system notifications
- See unread status indicator
- Mark as read or delete
- Read/unread filtering

### 9. **Utilisateurs** (System Users)
- View all system users
- See roles and services assigned
- Filter by role
- Cannot delete own account (protected)

---

## 🔍 Table Features

### Search Box
- Type to search across all columns
- Case-insensitive matching
- Real-time filtering (as you type)
- Search clears automatically

### Filters
- **Status Filter**: Filter by approval/availability status
- **Service Filter**: Filter by department/service
- **Role Filter**: Filter by user role
- **Vehicle Filter**: Filter by specific vehicle
- Filters vary by section

### Action Buttons
Each table row has action buttons:
- **👁️ View**: See full details
- **✏️ Edit**: Modify the record
- **🗑️ Delete**: Remove the record (with confirmation)
- **✅ Approve**: For pending requests
- **❌ Reject**: For pending requests

### Export Options
At the top of each table:
- **📥 Export**: Download table as CSV file
- **🖨️ Print**: Open print dialog for the table

---

## 💡 Tips & Tricks

### Finding Data Quickly
1. Use **Search Box** for text-based search
2. Use **Filters** for categorical filtering
3. Combine search + filter for precise results

### Exporting Data
1. Click **"Exporter"** button at the top
2. File downloads as `export-[timestamp].csv`
3. Open in Excel or Google Sheets
4. All columns and visible rows included

### Printing Data
1. Click **"Imprimer"** button at the top
2. Choose your printer
3. Print preview opens with table formatting
4. Works on all browsers

### Mobile Usage
- On tablets: Sidebar may collapse into tabs
- On phones: Sidebar becomes horizontal menu
- All features work on mobile
- Table scrolls horizontally on small screens

### Navigation Between Sections
- Click sidebar links to switch sections
- No page reload needed
- Content loads in 1-2 seconds
- Smooth fade-in animation

---

## 📋 Table Columns by Section

### Employés
- Nom (Name)
- Email
- Service
- Rôle (Role)
- Zone de Planification (Planning Zone)
- Date d'Inscription (Registration Date)

### Véhicules
- Nom (Name)
- Immatriculation (License Plate)
- Modèle (Model)
- Année (Year)
- Kilométrage (Mileage)
- Statut (Status)
- Type de Disponibilité (Availability Type)

### Kilométrage
- Employé (Employee)
- Véhicule (Vehicle)
- Destination
- Date de Départ (Start Date)
- Date de Retour (Return Date)
- Kilométrage (Mileage)
- Raison (Reason)

### Demandes
- Employé (Employee)
- Destination
- Véhicule Demandé (Requested Vehicle)
- Date de Départ (Start Date)
- Date de Retour (Return Date)
- Raison (Reason)
- Statut (Status)

### Réservations
- Employé (Employee)
- Véhicule (Vehicle)
- Destination
- Départ (Departure)
- Retour (Return)
- Kilométrage (Mileage)
- Statut (Status)

### Zones
- Nom (Name)
- Description
- Statut (Status)
- Employés Assignés (Assigned Employees)
- Véhicules Assignés (Assigned Vehicles)
- Date de Création (Creation Date)

### Fenêtres Planification
- Nom (Name)
- Date de Début (Start Date)
- Date de Fin (End Date)
- Durée (Duration)
- Statut (Status)
- Date de Création (Creation Date)

### Notifications
- Indicator (unread dot)
- Message
- Type
- Date

### Utilisateurs
- Nom (Name)
- Email
- Rôle(s) (Roles)
- Service
- Zone
- Statut Inscription (Registration Date)
- Dernière Connexion (Last Login)

---

## ❌ Troubleshooting

### Table Not Loading?
- Check your internet connection
- Try refreshing the page
- Check browser console (F12) for errors
- Try a different browser

### Search Not Working?
- Make sure the table has data
- Clear the search box and try again
- Table may be empty (check filters)

### Export Not Working?
- Try a different browser
- Check if pop-up blocker is enabled
- Try exporting a smaller table first

### Mobile Layout Issues?
- Rotate device to landscape if needed
- Zoom out if text is too large
- Close other browser tabs
- Try a different mobile browser

### Sidebar Won't Open/Close?
- Refresh the page
- Clear browser cache
- Try a different browser
- Check screen size

---

## 🎯 Common Workflows

### Approving a Request
1. Click **"Demandes"** in sidebar
2. Find the pending request
3. Click **✅ Approve** button
4. Confirm the action
5. Status updates immediately

### Finding All Vehicles in Maintenance
1. Click **"Véhicules"** in sidebar
2. Select **"Maintenance"** from Status filter
3. Only maintenance vehicles appear
4. Click View for more details

### Checking Employee Trips
1. Click **"Kilométrage"** in sidebar
2. Search employee name in search box
3. See all their trips with mileage
4. Export for reporting

### Exporting All Reservations
1. Click **"Réservations"** in sidebar
2. No filters (show all)
3. Click **"Exporter"** button
4. Open CSV in Excel
5. All reservations with all details

### Assigning Zone to User (requires other modules)
1. Go to full Zone edit view
2. Use zone assignment features
3. Not available in this sidebar view yet

---

## 📱 Responsive Breakpoints

| Screen Size | Layout | Notes |
|---|---|---|
| 1024px+ | Sidebar + Content | Ideal for desktop |
| 768px-1023px | Narrow sidebar | Tablet view |
| 481px-767px | Horizontal tabs | Mobile landscape |
| 480px or less | Stacked layout | Mobile portrait |

---

## 🔐 Security Notes

- Only admins can access this dashboard
- Your account cannot be deleted
- All data is filtered by role/permission
- CSRF tokens protect all actions
- Changes are logged in system

---

## 🆘 Need Help?

### Within the Dashboard
- Hover over buttons for tooltips
- Table headers explain each column
- Status badges use consistent colors
- Empty states explain what's missing

### Still Stuck?
- Check the main documentation file
- Contact your system administrator
- Submit a support ticket

---

## ✅ Checklist Before Use

- [ ] You are logged in as Admin/Super Admin
- [ ] You can see "Gestion des Données" in sidebar
- [ ] Can click and navigate to different sections
- [ ] Tables load within 2 seconds
- [ ] Search and filters work
- [ ] Can export to CSV
- [ ] Print function works

---

## 🎉 You're All Set!

Start managing your data with the new Admin Sidebar system. Click any section in the sidebar to get started!

Happy managing! 🚀
