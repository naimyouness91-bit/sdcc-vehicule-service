# ⏰ 24-Hour Advance Reservation Rule - Quick Reference

## What Changed?

Employees **must now book vehicle reservations at least 24 hours in advance**. Admins can book anytime.

## User Experience

### For Employees ❌ (Cannot book same day/next day)
```
Today: Monday 10:00 AM
❌ Cannot book: Monday (today)
❌ Cannot book: Tuesday (23h away)
✅ Can book: Wednesday (48h away)
✅ Can book: Any date 24+ hours away
```

### For Admins ✅ (No restrictions)
```
Today: Monday 10:00 AM
✅ Can book: Monday (today)
✅ Can book: Tuesday (next day)
✅ Can book: Any date anytime
```

## How It Works

### Frontend Protection
1. Date input automatically disables dates < 24h away
2. Shows info banner: "Les réservations doivent être faites au minimum 24 heures à l'avance"
3. Selecting invalid date shows error toast

### Backend Protection
1. Validates every submission on server
2. Prevents API bypass attempts
3. Returns clear error message if < 24 hours

### Error Message
```
🔴 Red Toast Popup
Title: "Délai insuffisant"
Message: "Les réservations doivent être faites au moins 24 heures à l'avance.
         Vous avez sélectionné une date qui n'offre que Xh de délai."
```

## Where to Find

### Files Changed
- ✏️ `app/Http/Controllers/MesDemandesController.php` - Backend validation
- ✏️ `resources/views/mes-demandes/create.blade.php` - Frontend validation + info banner

### Documentation
- 📖 `ADVANCE_RESERVATION_RULE.md` - Full implementation details
- 🧪 `TESTING_24HOUR_RULE.md` - Testing guide and checklists

## Testing

### Quick Test (Employee)
1. Open reservation form
2. Check date picker - today/tomorrow should be greyed out
3. Try to select tomorrow - should be disabled
4. Select a date 24+ hours away - should work
5. Submit - should succeed

### Quick Test (Admin)
1. Open reservation form
2. Check date picker - all dates selectable
3. Select today's date - should work
4. Submit - should succeed

## Configuration

### To Change from 24 to 48 Hours

**Backend** - `app/Http/Controllers/MesDemandesController.php` line 93:
```php
if ($hoursUntilReservation < 48) {  // Change 24 to 48
```

**Frontend** - `resources/views/mes-demandes/create.blade.php` line 743:
```javascript
const minimumDate = new Date(now.getTime() + 48 * 60 * 60 * 1000); // Change 24 to 48
```

## Bypass Prevention

### ❌ Cannot bypass by:
- Modifying HTML (browser)
- Using browser console
- Direct API call with old date
- Clearing local storage
- Using different browser

### ✅ Why?
- Server validates every request
- Logic on both frontend AND backend
- Both must allow the date to proceed

## Status

✅ **Production Ready**
- Frontend validation: Active
- Backend validation: Active  
- Error messages: Localized (French)
- Admin override: Enabled
- Toast notifications: Working

## Support

**For Employees**: "Je ne peux pas réserver pour demain?"
→ "Réservations must be made 24 hours in advance. Please select a date further in the future."

**For Admins**: "Pourquoi je peux réserver pour aujourd'hui mais pas les employés?"
→ "Admin users have override permissions. Employees must book 24+ hours in advance."

## Key Points

1. **Only employees are restricted** - Admins can book anytime
2. **Exactly 24 hours is allowed** - 24.0h = ✅, 23.9h = ❌
3. **Date picker prevents selection** - Users won't see invalid dates
4. **Clear error messages** - Shows hours remaining
5. **Can't bypass** - Server validates all submissions

---

**Status**: ✅ Active & Enforced  
**Applies To**: Employees only  
**Error Display**: Modern popup toast notifications
