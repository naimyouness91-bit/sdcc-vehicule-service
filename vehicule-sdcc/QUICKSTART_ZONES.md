# ⚡ Zone-Based Reservation - Quick Start Guide

## 🚀 10-Second Summary

The **Planification system now controls vehicle access in Reservations**. Employees can only reserve vehicles assigned to their zone.

- ✅ **Automatic** - No manual configuration needed
- ✅ **Secure** - Three-layer validation
- ✅ **Scalable** - Add zones as needed
- ✅ **User-friendly** - Clear visual indicators

---

## 🎯 For End Users (Employees)

### Creating a Reservation
```
1. Click "Nouvelle Demande"
2. See your zone: "📍 Zone: Logistique Générale"
3. Enter reservation date
4. Dropdown shows ONLY your zone's vehicles
5. Select vehicle → Submit
✅ Done!
```

### If No Zone Assigned
```
⚠️ Warning: "Vous n'avez pas de zone de planification assignée"
→ Contact admin for zone assignment
→ Cannot create reservation until zone assigned
```

### Understanding Zones
- **Zone** = Group of employees + group of vehicles
- **Your zone** = Determines which vehicles you can reserve
- **Admin assigns** zones (you don't need to)
- **Same fleet** = All employees in zone can see same vehicles

---

## 👨‍💼 For Administrators

### Managing Zones

#### View All Zones
```
Admin Dashboard → Planification → View Zones
```

#### Assign Employee to Zone
```bash
# Via Admin Panel (preferred)
Dashboard → Utilisateurs → Edit User → Select Zone
```

#### Assign Vehicle to Zone
```bash
# Via Database (for now)
INSERT INTO planning_zone_car (planning_zone_id, car_id, created_at, updated_at)
VALUES (1, 1, NOW(), NOW());
```

#### Update Zone Name
```bash
UPDATE planning_zones SET name = 'New Name' WHERE id = 1;
```

### Common Tasks

#### Create New Zone
```sql
INSERT INTO planning_zones (name, description, status, created_at, updated_at)
VALUES ('Zone Name', 'Description', 'active', NOW(), NOW());
```

#### Add Multiple Employees to Zone
```sql
INSERT INTO planning_zone_user (planning_zone_id, user_id, created_at, updated_at)
VALUES 
(1, 5, NOW(), NOW()),
(1, 6, NOW(), NOW()),
(1, 7, NOW(), NOW());
```

#### Add Vehicle to Zone
```sql
INSERT INTO planning_zone_car (planning_zone_id, car_id, created_at, updated_at)
VALUES (1, 1, NOW(), NOW());
```

#### View Zone Members
```bash
SELECT u.id, u.name, u.email, z.name as zone
FROM users u
JOIN planning_zones z ON u.planning_zone_id = z.id
WHERE z.id = 1;
```

#### View Zone Vehicles
```bash
SELECT c.id, c.name, c.matricule, z.name as zone
FROM cars c
JOIN planning_zone_car pc ON c.id = pc.car_id
JOIN planning_zones z ON pc.planning_zone_id = z.id
WHERE z.id = 1;
```

---

## 🔧 For Developers

### Testing Zone Access

#### Test 1: Employee Can Access Zone Vehicle
```bash
# Login as alice@sdcc.ma
# Select date → dropdown shows vehicles
# Can submit reservation
✅ PASS
```

#### Test 2: Cannot Access Non-Zone Vehicle
```bash
# Edit HTML form car_id to different vehicle
# Submit
# Backend rejects: "Ce véhicule n'est pas disponible pour votre zone"
✅ PASS (Security working)
```

#### Test 3: Admin Unrestricted
```bash
# Login as admin@sdcc.ma
# No zone displayed
# All vehicles available
✅ PASS
```

### Code Examples

#### Check if User Can Access Vehicle
```php
$user = Auth::user();
$car = Car::find(1);

if ($user->canAccessVehicle($car)) {
    // User can access this vehicle
} else {
    // Deny access
}
```

#### Get User's Available Vehicles
```php
$user = Auth::user();
$vehicles = $user->getAvailableVehicles(); // Collection of cars
```

#### Get Zone Vehicles
```php
$zone = PlanningZone::find(1);
$vehicles = $zone->cars; // All vehicles in zone
$employees = $zone->users; // All employees in zone
```

#### Add Vehicle to Zone
```php
$zone = PlanningZone::find(1);
$zone->cars()->attach($carId);
```

#### Assign Employee to Zone
```php
$zone = PlanningZone::find(1);
$zone->users()->attach($userId);

// Also update direct reference
$user->update(['planning_zone_id' => $zone->id]);
```

---

## 📊 Database Schema Reference

### Users Table (Modified)
```sql
id              INTEGER PRIMARY KEY
name            VARCHAR
email           VARCHAR UNIQUE
password        VARCHAR
service         VARCHAR
planning_zone_id INTEGER FK (nullable)  ← NEW
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### Planning Zones Table
```sql
id          INTEGER PRIMARY KEY
name        VARCHAR
description TEXT
status      VARCHAR
created_at  TIMESTAMP
updated_at  TIMESTAMP
```

### Planning Zone User (Pivot)
```sql
id                  INTEGER PRIMARY KEY
planning_zone_id    INTEGER FK
user_id             INTEGER FK
created_at          TIMESTAMP
updated_at          TIMESTAMP
UNIQUE(planning_zone_id, user_id)
```

### Planning Zone Car (Pivot)
```sql
id                  INTEGER PRIMARY KEY
planning_zone_id    INTEGER FK
car_id              INTEGER FK
created_at          TIMESTAMP
updated_at          TIMESTAMP
UNIQUE(planning_zone_id, car_id)
```

---

## 🔑 API Endpoints

### Available Vehicles by Date
```http
GET /cars/available-by-date?date=2026-04-16
Accept: application/json

Response:
{
  "date": "2026-04-16",
  "day_type": "weekday",
  "cars": [
    {"id": 1, "name": "Renault Clio", "matricule": "23130T6", ...}
  ],
  "has_zone": true,
  "warning": null
}
```

### If No Zone Assigned
```json
{
  "date": "2026-04-16",
  "day_type": "weekday",
  "cars": [],
  "has_zone": false,
  "warning": "Vous n'avez pas de zone de planification..."
}
```

---

## ⚙️ Configuration

### Environment Variables
No new environment variables required - uses existing database.

### Migrations to Run
```bash
php artisan migrate:fresh --seed
# This runs all migrations including zone setup
# Seeders create 3 default zones with employee assignments
```

### Reset Everything
```bash
php artisan migrate:fresh --seed
# Drops all tables and reseeds with fresh test data
# All zones and assignments recreated
```

---

## 🆘 Troubleshooting

### Problem: Employee sees no vehicles
**Solution:**
1. Check user has planning_zone_id assigned
   ```sql
   SELECT planning_zone_id FROM users WHERE email = 'alice@sdcc.ma';
   ```
2. Check zone has vehicles assigned
   ```sql
   SELECT COUNT(*) FROM planning_zone_car WHERE planning_zone_id = 1;
   ```
3. Check vehicle status is 'disponible'
   ```sql
   SELECT * FROM cars WHERE id = 1;
   ```

### Problem: Admin can't see vehicle dropdown
**Solution:**
- This is normal! Admin has no zone (gets all vehicles)
- Check if vehicles exist in database
- No zone warning shouldn't appear for admin

### Problem: Can manually select unauthorized vehicle
**Solution:**
- Backend validation should reject
- Check CarController available() has zone filtering
- Check MesDemandesController store() has validation
- Review application logs

### Problem: Zone not displaying in user card
**Solution:**
1. Check user has planning_zone_id set
2. Check view file has zone display code
3. Review browser console for JavaScript errors
4. Clear browser cache (Ctrl+Shift+Delete)

---

## 📚 Related Documentation

- [Full Integration Guide](INTEGRATION_DOCUMENTATION.md)
- [Before/After Comparison](BEFORE_AFTER_INTEGRATION.md)
- [Database Schema](INTEGRATION_DOCUMENTATION.md#-system-architecture)
- [Security Details](INTEGRATION_DOCUMENTATION.md#-security-implementation)
- [Testing Procedures](INTEGRATION_DOCUMENTATION.md#-testing-workflow)

---

## 🎯 Next Steps

### For Administrators
1. ✅ Verify migration ran successfully
2. ✅ Check test data seeded (alice & bob have zones)
3. ✅ Test with employee account
4. ✅ Create additional zones as needed
5. ✅ Assign employees to zones

### For Developers
1. ✅ Review [INTEGRATION_DOCUMENTATION.md](INTEGRATION_DOCUMENTATION.md) for full details
2. ✅ Test security scenarios
3. ✅ Check backend validation is working
4. ✅ Verify API returns correct zone filtering
5. ✅ Run test suite

### For Users
1. ✅ Wait for admin to assign your zone
2. ✅ Try creating new reservation
3. ✅ Verify you see only your zone's vehicles
4. ✅ Report any issues to admin

---

## 🎓 Key Concepts

| Term | Meaning |
|------|---------|
| **Zone** | Group of employees + group of vehicles assigned by admin |
| **Planning Zone** | Database entity managing zone assignments |
| **Zone Assignment** | Employee → Zone relationship (1 zone per employee) |
| **Vehicle Assignment** | Zone → Vehicle relationship (many vehicles per zone) |
| **Filtering** | System automatically hides non-zone vehicles |
| **Validation** | Backend checks user has permission before creating reservation |

---

## 📞 Support

For issues or questions:
1. Check troubleshooting section above
2. Review logs: `storage/logs/laravel.log`
3. Contact: Admin or developer
4. Emergency: Check database directly for assignments

---

## ✨ Quick Commands

```bash
# Run migrations only
php artisan migrate

# Reset and seed with fresh data
php artisan migrate:fresh --seed

# View users and zones
php artisan tinker
>>> User::with('planningZone')->get();
>>> PlanningZone::with('users', 'cars')->get();

# Clear application cache
php artisan cache:clear

# View recent errors
tail -f storage/logs/laravel.log
```

---

## 🎉 Success Criteria

You'll know it's working when:
- ✅ Employees see zone name in user card
- ✅ Vehicle dropdown filtered by zone
- ✅ Cannot manually select unauthorized vehicles
- ✅ Admin sees all vehicles (no filtering)
- ✅ Backend validation rejects unauthorized attempts
- ✅ No console errors in browser

---

**Ready to use!** 🚀

Start with the [Full Integration Guide](INTEGRATION_DOCUMENTATION.md) for comprehensive documentation.
