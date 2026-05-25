# 24-Hour Reservation Rule - Testing & Verification Guide

## Quick Start Verification

### ✅ Test 1: Date Input Constraint (Frontend)
1. Open the reservation form as an employee
2. Click on the date input field
3. **Expected**: Today's date and tomorrow are disabled (greyed out)
4. **Expected**: Dates starting from 24+ hours away are selectable

### ✅ Test 2: Error Toast (Frontend Validation)
1. Try to manually enter today's date in the field
2. Blur or change the field
3. **Expected**: Red error toast appears with message: "Délai insuffisant"
4. **Expected**: Shows current hours available (will be negative or small)
5. **Expected**: Date field is cleared after error

### ✅ Test 3: Form Submission (Backend Validation)  
1. Select a date 25+ hours away
2. Fill all form fields
3. Click "Soumettre la demande" (Submit Request)
4. **Expected**: Reservation created successfully
5. **Expected**: Green "Success" toast appears
6. **Expected**: Redirect to reservations list

### ✅ Test 4: Invalid Date Bypass Prevention (Backend)
1. Select a date 25+ hours away
2. Fill all fields
3. Inspect element and manually change date to today
4. Submit form using browser console or network tools
5. **Expected**: Server rejects with error validation message
6. **Expected**: Red error banner displays
7. **Expected**: Cannot bypass the 24-hour rule

### ✅ Test 5: Admin Exemption
1. Login as admin
2. Open reservation form
3. Try to select today's date
4. **Expected**: Date is selectable (no `min` constraint)
5. **Expected**: Can submit reservation for today
6. **Expected**: Reservation created successfully

## Detailed Test Cases

### Test Case A: Employee Booking 48 Hours in Advance ✅
**Precondition**: Current time is Monday 10:00 AM
**Steps**:
1. Navigate to "Nouvelle demande"
2. Calendar shows dates from Wednesday onwards as selectable
3. Select Wednesday date
4. Select available vehicle
5. Fill destination and reason
6. Click "Soumettre"
**Expected Result**: Success toast, reservation created for Wednesday

### Test Case B: Employee Booking 23 Hours in Advance ❌
**Precondition**: Current time is Monday 10:00 AM, trying to book for Tuesday 10:00 AM
**Steps**:
1. Navigate to "Nouvelle demande"
2. Calendar shows Tuesday as disabled
3. Try to click Tuesday (should not be selectable)
4. Change to Wednesday
**Expected Result**: Tuesday date is disabled (greyed out)

### Test Case C: Frontend Toast Error ❌
**Precondition**: Employee tries to submit with browser console
**Steps**:
1. Fill form with today's date (using DevTools)
2. Click submit
**Expected Result**: 
- Red error toast appears
- Shows message: "Les réservations doivent être faites..."
- Shows current hours (negative number)
- Form doesn't submit

### Test Case D: Backend Security Validation ❌
**Precondition**: Attacker tries to bypass frontend validation via API
**Steps**:
1. Use Postman or curl to POST to `/mes-demandes`
2. Include `start_date` as today
3. Include valid CSRF token and other fields
**Expected Result**:
- 422 Unprocessable Entity response
- Error message in JSON response
- Reservation not created

### Test Case E: Admin Override
**Precondition**: Login as admin user
**Steps**:
1. Navigate to admin reservation creation
2. Select today's date
3. Fill other fields
4. Submit
**Expected Result**: Reservation created immediately, no 24-hour restriction

### Test Case F: Info Banner Display
**Precondition**: Visit reservation form
**Steps**:
1. Look for green info box
2. Read content
**Expected Result**:
- Green banner visible
- Says: "Important: Les réservations doivent être faites au minimum 24 heures à l'avance"
- Shows icon and clear text

## Browser Testing Matrix

| Browser | Version | Desktop | Mobile | Status |
|---------|---------|---------|--------|--------|
| Chrome | 90+ | ✓ | ✓ | Supported |
| Firefox | 88+ | ✓ | ✓ | Supported |
| Safari | 14+ | ✓ | ✓ | Supported |
| Edge | 90+ | ✓ | N/A | Supported |
| IE | Any | ✗ | ✗ | Not supported |

## Error Message Verification

### Frontend Toast Text
```
Title: "Délai insuffisant"
Message: "Les réservations doivent être faites au moins 24 heures à l'avance. 
         Vous avez sélectionné une date qui n'offre que Xh de délai."
```

### Backend Error Text  
```
"Les réservations doivent être faites au moins 24 heures à l'avance. 
 Vous avez sélectionné une date qui ne dépasse pas ce délai (Xh). 
 Veuillez sélectionner une date ultérieure."
```

## Accessibility Testing

- [ ] Toast appears to screen readers
- [ ] Error message is announced
- [ ] Date input has accessible label
- [ ] Info banner uses proper heading hierarchy
- [ ] Keyboard navigation works (Tab through form)

## Performance Testing

- [ ] Date calculation takes < 10ms
- [ ] Toast appears < 300ms
- [ ] Form submission < 2s
- [ ] No page lag when selecting dates
- [ ] Mobile responsiveness smooth

## Edge Cases to Test

### Edge Case 1: Exactly 24 Hours
**Setup**: Current time 10:00 AM Monday
**Test**: Try to book for 10:00 AM Tuesday
**Expected**: ✅ Should be allowed (24h = exactly 24h)

### Edge Case 2: 23 Hours 59 Minutes  
**Setup**: Current time 10:00 AM Monday
**Test**: Try to book for 9:59 AM Tuesday
**Expected**: ❌ Should be rejected (< 24h)

### Edge Case 3: Midnight Boundary
**Setup**: Current time 11:50 PM Monday
**Test**: Try to book for Tuesday (any time)
**Expected**: May vary slightly due to timezone, but generally ❌ rejected

### Edge Case 4: Daylight Saving Time
**Setup**: Date crosses DST change
**Test**: Book around DST boundary
**Expected**: Carbon library handles DST automatically

### Edge Case 5: Form Prepopulation
**Setup**: Access form with pre-filled car_id from calendar
**Test**: Check if date validation still works with pre-filled data
**Expected**: ✅ Validation works regardless of prepopulation

## Log Analysis

### Check Backend Logs
```bash
# Laravel logs show validation rejection
tail -f storage/logs/laravel.log

# Look for entries like:
# "The start date field must be at least 24 hours in the future"
```

### Browser Console Check
```javascript
// Check for console errors
console.log("Toast system active:", typeof Toast !== 'undefined');
console.log("Toast methods:", Object.keys(Toast));

// Test Toast directly
Toast.error("Test error message");
```

## Data Verification

### Check Database
```sql
-- View recent reservations
SELECT id, user_id, start_date, created_at 
FROM demandes 
WHERE created_at >= NOW() - INTERVAL 1 DAY
ORDER BY created_at DESC;

-- Should not show reservations < 24 hours in future
```

## Post-Deployment Checklist

- [ ] Code deployed to production
- [ ] All tests passing
- [ ] No errors in log files
- [ ] Employees report seeing date restrictions
- [ ] Toast notifications display correctly
- [ ] Info banner visible on form
- [ ] Admins can still book without restrictions
- [ ] Error messages are clear and helpful

## Rollback Plan

If issues occur:

1. **Quick Disable Frontend**: Comment out validation in create.blade.php
2. **Quick Disable Backend**: Comment out validation in MesDemandesController
3. **Full Rollback**: Git revert to previous commit
4. **Database**: No schema changes, safe to rollback

## Support Documentation

- Users see: "Les réservations doivent être faites au minimum 24 heures à l'avance"
- Translation: "Reservations must be made at least 24 hours in advance"
- This applies to employees only, admins are exempt
- Dates are automatically disabled to prevent invalid selections

## Sign-Off

- [ ] Frontend validation tested
- [ ] Backend validation tested
- [ ] Toast notifications working
- [ ] Admin override confirmed
- [ ] Edge cases handled
- [ ] Error messages clear
- [ ] No console errors
- [ ] Ready for production

---

**Test Date**: [Insert date]  
**Tested By**: [Insert name]  
**Environment**: Production / Staging / Local  
**Status**: ✅ Ready / ⏳ In Progress / ❌ Issues Found
