# 📊 24-Hour Advance Reservation Rule - Visual Architecture

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    EMPLOYEE RESERVATION SYSTEM                              │
└─────────────────────────────────────────────────────────────────────────────┘

                         ┌──────────────────┐
                         │  Employee Opens  │
                         │  Reservation Form│
                         └────────┬─────────┘
                                  │
                    ╔═════════════╩═════════════╗
                    ║                           ║
            ┌───────▼────────┐         ┌───────▼────────┐
            │   IS EMPLOYEE  │         │    IS ADMIN    │
            │   ❌ (YES)     │         │    ✅ (YES)    │
            └───────┬────────┘         └───────┬────────┘
                    │                         │
        ┌───────────▼──────────┐    ┌────────▼─────────┐
        │ 24-Hour Restriction  │    │  No Restriction  │
        │ APPLIES              │    │  NO LIMIT        │
        │ ──────────────────── │    │  ────────────    │
        │ • Date < 24h: ❌     │    │ • Any date: ✅   │
        │ • Date ≥ 24h: ✅     │    │                  │
        └───────────┬──────────┘    └────────┬─────────┘
                    │                        │
        ┌───────────▼────────────────────────▼──────────┐
        │     USER SELECTS DATE FROM CALENDAR           │
        └────┬──────────────────────────────────────────┘
             │
    ╔════════╩════════════════════════════════════╗
    ║  FRONTEND: JavaScript Validation            ║
    ║  ════════════════════════════════════════   ║
    ║  1. Check selected date                     ║
    ║  2. Calculate hours until reservation       ║
    ║  3. If hours < 24:                          ║
    ║     → Show RED TOAST ERROR                  ║
    ║     → Clear date field                      ║
    ║     → Block submission                      ║
    ║  4. If hours ≥ 24:                          ║
    ║     → Allow form to proceed                 ║
    ╚════════╤════════════════════════════════════╝
             │
    VALID? ──┼── NO ──┐
             │        │
             │        └──→ 🔴 Toast Error (5.5s)
             │           "Délai insuffisant"
             │           Close form, retry
             │
            YES
             │
    ┌────────▼──────────────┐
    │ USER FILLS ALL FIELDS │
    │ • Vehicle             │
    │ • Destination         │
    │ • Reason              │
    │ • Times               │
    └────────┬──────────────┘
             │
    ┌────────▼──────────────────────┐
    │ USER CLICKS "SOUMETTRE"       │
    │ (SUBMIT RESERVATION REQUEST)  │
    └────────┬──────────────────────┘
             │
    ╔════════╩═══════════════════════════════════╗
    ║  FRONTEND: Form Submit Validation          ║
    ║  ═════════════════════════════════════════ ║
    ║  • Double-check hours < 24 again           ║
    ║  • Prevent bypasses                        ║
    ║  • If invalid: Show error, block submit    ║
    ║  • If valid: Continue                      ║
    ╚════════╤═══════════════════════════════════╝
             │
    VALID? ──┼── NO ──┐
             │        │
             │        └──→ 🔴 Toast Error
             │           "Réservation rejetée"
             │
            YES
             │
    ┌────────▼────────────────────────┐
    │  SEND HTTP POST REQUEST         │
    │  /mes-demandes                  │
    │  ─────────────────────────────  │
    │  • start_date                   │
    │  • car_id                       │
    │  • destination                  │
    │  • All form fields              │
    └────────┬────────────────────────┘
             │
    ╔════════╩════════════════════════════════════════╗
    ║  BACKEND: MesDemandesController@store()        ║
    ║  ═════════════════════════════════════════════ ║
    ║  1. Validate all fields (Laravel rules)       ║
    ║  2. Check if user is employee:                ║
    ║     IF YES:                                   ║
    ║       → Calculate: now vs start_date          ║
    ║       → Calculate: hours until reservation    ║
    ║       → IF hours < 24:                        ║
    ║          → Return validation error ❌         ║
    ║       → IF hours ≥ 24:                        ║
    ║          → Continue                          ║
    ║     IF NO (is admin):                         ║
    ║       → Skip 24-hour check                    ║
    ║       → Continue                             ║
    ║  3. Check other validations:                  ║
    ║     → Vehicle available?                     ║
    ║     → Zone access?                           ║
    ║     → No conflicts?                          ║
    ║  4. If all pass: Create reservation ✅        ║
    ║  5. If any fail: Return error ❌              ║
    ╚════════╤════════════════════════════════════════╝
             │
    VALID? ──┼── NO ──┐
             │        │
             │        └──→ 🔴 Error Response
             │           Show error message
             │           Stay on form
             │
            YES
             │
    ┌────────▼──────────────────────┐
    │  CREATE RESERVATION IN DB     │
    │  ────────────────────────────  │
    │  • INSERT INTO demandes       │
    │  • Send notifications         │
    │  • Log submission             │
    └────────┬──────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │  SEND SUCCESS RESPONSE        │
    │  ────────────────────────────  │
    │  • 201 Created                │
    │  • Redirect to list           │
    │  • Flash message              │
    └────────┬──────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │  FRONTEND: Show Success       │
    │  ────────────────────────────  │
    │  🟢 Green Toast (4.5s)        │
    │  "Votre demande a été..."     │
    │  ────────────────────────────  │
    │  Redirect to list page        │
    └────────┬──────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │  SHOW RESERVATION IN LIST     │
    │  Status: "En Attente"         │
    │  (Pending admin approval)     │
    └────────────────────────────────┘
```

---

## Date Calculation Timeline

```
TODAY (Current Time)
    │
    ├─ 00:00 AM ────────────────────────────────────────
    │
    ├─ 12:00 PM ◄─── Current Time (e.g., Monday 12:00)
    │
    └─ 11:59 PM ────────────────────────────────────────

TOMORROW (Next Day)
    │
    ├─ 00:00 AM ◄─── 12 hours away ❌
    │
    ├─ 12:00 PM ◄─── 24 hours away ✅ (exactly 24h)
    │
    └─ 11:59 PM ◄─── 35.999 hours away ✅

DAY AFTER TOMORROW
    │
    ├─ 00:00 AM ◄─── 36 hours away ✅
    │
    └─ Any time  ◄─── Always ✅

┌─────────────────────────────────────────────────────┐
│ RULE: hoursUntilReservation < 24 = ❌ BLOCKED      │
│       hoursUntilReservation ≥ 24 = ✅ ALLOWED      │
└─────────────────────────────────────────────────────┘
```

---

## Date Input Behavior

```
┌─────────────────────────────────────────────────────────┐
│                    DATE PICKER CALENDAR                 │
├─────────────────────────────────────────────────────────┤
│  Mon  Tue  Wed  Thu  Fri  Sat  Sun                      │
│   1   2   3   4   5   6   7                             │
│   8   9   10  11  12  13  14                            │
│  15  16  17  18  19  20  21                             │
│  22  23  24  25  26  27  28                             │
│  29  30                                                 │
└─────────────────────────────────────────────────────────┘

EMPLOYEE VIEW (Current Date: April 15 @ 10:00 AM):

┌─────────────────────────────────────────────────────────┐
│                    DATE PICKER CALENDAR                 │
├─────────────────────────────────────────────────────────┤
│  Mon  Tue  Wed  Thu  Fri  Sat  Sun                      │
│ [1]  [2]  [3]  [4]  [5]  [6]  [7]                      │
│ [8]  [9]  [10] [11] [12] [13] [14]                     │
│ [15] 🔴16 🔴17  18   19   20   21  ◄ Today (15) marked  │
│  22   23   24   25   26   27   28  ◄ Disabled dates     │
│  29   30                                                │
└─────────────────────────────────────────────────────────┘

Legend:
  🔴 = Disabled (cannot select) ❌
       → Today (< 24h away)
       → Tomorrow (< 24h away)
  [Numbers] = Enabled (can select) ✅
             → Day after tomorrow and beyond

ADMIN VIEW (Current Date: April 15 @ 10:00 AM):

┌─────────────────────────────────────────────────────────┐
│                    DATE PICKER CALENDAR                 │
├─────────────────────────────────────────────────────────┤
│  Mon  Tue  Wed  Thu  Fri  Sat  Sun                      │
│ [1]  [2]  [3]  [4]  [5]  [6]  [7]                      │
│ [8]  [9]  [10] [11] [12] [13] [14]                     │
│ [15] [16] [17]  18   19   20   21  ◄ All dates enabled  │
│  22   23   24   25   26   27   28                       │
│  29   30                                                │
└─────────────────────────────────────────────────────────┘

Legend:
  [Numbers] = Enabled (can select) ✅
             → All dates available (no restrictions)
```

---

## Validation Stack

```
┌─────────────────────────────────────────────────────┐
│          FRONTEND VALIDATION LAYERS                 │
├─────────────────────────────────────────────────────┤
│ 1. HTML5 Date Input                                 │
│    └─ min="2024-04-19" (24h from now)              │
│       • Browser prevents date selection before min  │
│       • Built-in protection                         │
│                                                     │
│ 2. JavaScript on Change Event                       │
│    └─ When user changes date field                  │
│       • Calculate hours until reservation           │
│       • Show Toast error if < 24h                   │
│       • Clear invalid date                          │
│                                                     │
│ 3. JavaScript on Form Submit                        │
│    └─ When user clicks "Soumettre"                  │
│       • Final validation before sending              │
│       • Prevents bypass attempts                    │
│       • Shows error if needed                       │
└─────────────────────────────────────────────────────┘

                         ↓
              Submit HTTP POST Request
                         ↓

┌─────────────────────────────────────────────────────┐
│           BACKEND VALIDATION LAYER                  │
├─────────────────────────────────────────────────────┤
│ 1. Laravel Form Validation                          │
│    └─ Basic field checks (required, types, etc)    │
│                                                     │
│ 2. 24-Hour Check (Employee only)                    │
│    └─ Carbon::now()->diffInHours($start_date)      │
│       • Independent calculation                     │
│       • Server-side truth                           │
│       • Rejects if hours < 24                       │
│                                                     │
│ 3. Other Validations                                │
│    └─ Vehicle exists                                │
│    └─ Vehicle available                             │
│    └─ User has zone access                          │
│    └─ No conflicts with other reservations          │
│                                                     │
│ 4. Database Insert                                  │
│    └─ All validations passed                        │
│    └─ Create reservation record                     │
└─────────────────────────────────────────────────────┘

SECURITY PRINCIPLE:
"Trust nothing from the client. Server validates all."
```

---

## Error Flow Diagram

```
User tries invalid date (< 24h away):

┌─────────────────────────────────────────────────┐
│ INPUT: start_date = today                       │
└────────────┬────────────────────────────────────┘
             │
    ┌────────▼────────┐
    │ Frontend Checks │
    │ ──────────────  │
    │ Hours: -0.5h    │
    │ Valid? NO ❌    │
    └────────┬────────┘
             │
    ┌────────▼──────────────────────┐
    │ 🔴 Show Toast Error           │
    │ ────────────────────────────── │
    │ Title: "Délai insuffisant"    │
    │ Msg: "...n'offre que -0.5h"   │
    │ Duration: 5.5 seconds          │
    └────────┬──────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ Clear date field              │
    │ Focus on date input           │
    │ Block form submission         │
    └────────┬──────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ User must select valid date   │
    └────────────────────────────────┘


If user bypasses frontend (DevTools):

┌─────────────────────────────────────────────────┐
│ INPUT: start_date = today (via API call)        │
└────────────┬────────────────────────────────────┘
             │
    ┌────────▼────────┐
    │ Frontend Checks │
    │ (already gone)  │ ← Bypassed
    └────────┬────────┘
             │
    ┌────────▼─────────────────────┐
    │ POST /mes-demandes            │
    │ (Server receives request)     │
    └────────┬─────────────────────┘
             │
    ┌────────▼──────────────┐
    │ Backend Validation    │
    │ ──────────────────── │
    │ Check: isEmployee()   │
    │ Calculate hours       │
    │ Hours: -0.5h          │
    │ < 24? YES ❌          │
    └────────┬──────────────┘
             │
    ┌────────▼──────────────────────┐
    │ 🔴 Return Error Response      │
    │ ────────────────────────────── │
    │ Status: 422                    │
    │ Field: start_date              │
    │ Error: "...24 heures..."       │
    └────────┬──────────────────────┘
             │
    ┌────────▼──────────────────────┐
    │ Frontend receives error        │
    │ Shows validation error banner  │
    │ Form doesn't submit            │
    │ Reservation NOT created        │
    └────────────────────────────────┘
```

---

## Status Legend

```
✅ = Allowed / Working / Success / Pass
❌ = Blocked / Not working / Error / Fail
🟢 = Green / Success / Positive action
🔴 = Red / Error / Negative action
⏰ = Time-based / Duration indicator
📅 = Date-related
👤 = User/Role based
🔒 = Security / Validation
⚠️  = Warning / Notice
ℹ️  = Information / Banner
```

---

**Created**: April 21, 2026  
**Purpose**: Visual representation of 24-hour reservation rule  
**Audience**: Developers, QA, Support team
