# Where to See Audit Log on Your Telemedicine Website

## Quick Answer
**URL:** http://localhost/Telemedicine/admin-audit.php

---

## Step-by-Step: Where & How to View

### Step 1: Start XAMPP
- Open XAMPP Control Panel
- Click **Start** on Apache and MySQL

### Step 2: Open Admin Audit Page in Browser
**Type this in your browser address bar:**
```
http://localhost/Telemedicine/admin-audit.php
```

### Step 3: What You'll See
A table like this:

```
┌─────┬───────┬────────┬───────────┬──────────────┬─────────────────────┬────────────┬────────────┐
│ #   │ Table │ Action │ Record ID │ Changed By   │ When                │ Old Data   │ New Data   │
├─────┼───────┼────────┼───────────┼──────────────┼─────────────────────┼────────────┼────────────┤
│ 1   │ prestb│ INSERT │ 14        │ doctor_name  │ 2025-01-15 10:30:45 │ (empty)    │ doctor=... │
│ 2   │ prestb│ UPDATE │ 12        │ doctor_name  │ 2025-01-15 10:15:22 │ disease=.. │ disease=.. │
│ 3   │ prestb│ DELETE │ 10        │ admin        │ 2025-01-15 09:45:00 │ doctor=... │ (empty)    │
└─────┴───────┴────────┴───────────┴──────────────┴─────────────────────┴────────────┴────────────┘
```

- **#** = Audit entry ID (auto-incremented)
- **Table** = which table was changed (e.g., `prestb` = prescriptions)
- **Action** = INSERT (new row), UPDATE (modified), or DELETE (removed)
- **Record ID** = which prescription/row was affected
- **Changed By** = username of person who made the change
- **When** = exact date & time
- **Old Data** = values BEFORE the change
- **New Data** = values AFTER the change

---

## Integration Option: Add Link in Admin Panel

To make it easier for admins to find, add a link in your existing admin panel.

### Where to add it:
Edit your main admin file (e.g., `admin-panel.php` or `admin-panel1.php`):

Find the admin menu section (look for links like "Logout", "Dashboard", etc.) and add:
```html
<a class="list-group-item list-group-item-action" href="admin-audit.php">
  <i class="fa fa-eye" aria-hidden="true"></i> View Audit Log
</a>
```

This will show as a menu item in the admin panel.

---

## Real-World Scenario: Trace Who Changed a Prescription

**Example:** A prescription was modified at 10:30 AM. Who did it? What changed?

1. Go to http://localhost/Telemedicine/admin-audit.php
2. Look for the row with:
   - Action = `UPDATE`
   - Table = `prestb`
   - Record ID = prescription you're looking for
3. View:
   - **Changed By** = doctor/admin name
   - **When** = exact timestamp
   - **Old Data** = what prescription said before (e.g., "disease=Flu")
   - **New Data** = what it says now (e.g., "disease=Cough")

---

## Before You See Data: 3 Critical Setup Steps

### 1. Run the Trigger SQL (One-Time)
```sql
-- In phpMyAdmin SQL tab, paste and Execute:
-- OR use PowerShell:
-- mysql -u root -p myhmsdb < "C:\xampp\htdocs\Telemedicine\db\audit_triggers.sql"
```
This creates the `audit_log` table and triggers.

### 2. Make Sure `@current_user` is Set
**In phpMyAdmin (for testing):**
```sql
SET @current_user = 'testuser';
```

**In your PHP code (production):**
Add this ONCE when a user logs in or before they save/update data:
```php
<?php
require_once 'include/config.php';
session_start();

// Set current user on the DB connection
$username = $_SESSION['username']; // or logged-in user
mysqli_query($con, "SET @current_user = '" . mysqli_real_escape_string($con, $username) . "'");

// Now when they insert/update/delete prescriptions,
// the trigger will record their username in audit_log
?>
```

### 3. Create/Modify Data
Insert, update, or delete a prescription in your app. The triggers automatically log it.

---

## Where Triggers Are Active

**Current triggers monitor these actions on the `prestb` (prescriptions) table:**

✅ **WHEN YOU INSERT** a new prescription → logged to `audit_log`
✅ **WHEN YOU UPDATE** a prescription → old and new values logged
✅ **WHEN YOU DELETE** a prescription → logged with old data

---

## Test Right Now (without your app)

### Quick Test in phpMyAdmin:

1. Go to http://localhost/phpmyadmin → SQL tab
2. Run:
```sql
SET @current_user = 'testdoctor';
INSERT INTO prestb (doctor, pid, ID, fname, lname, appdate, apptime, disease, allergy, prescription)
VALUES ('Dr. Test', 1, 999, 'John', 'Doe', '2025-01-20', '14:00:00', 'Fever', 'Aspirin', 'Rest 2 days');
```

3. Then open: http://localhost/Telemedicine/admin-audit.php
4. **You should see a new row** with Action=INSERT, Changed By=testdoctor

---

## File Structure (for reference)

```
Telemedicine/
├── admin-audit.php              ← View audit logs HERE
├── db/
│   └── audit_triggers.sql       ← Run this ONCE to enable triggers
├── include/
│   └── config.php               ← Your DB connection
└── prescribe.php                ← Where you modify prescriptions
```

---

## Summary

| Component | Location | Purpose |
|-----------|----------|---------|
| Audit table | `myhmsdb.audit_log` | Stores all changes |
| Audit view page | `admin-audit.php` | See the logs in browser |
| Triggers | DB (created by `audit_triggers.sql`) | Auto-log changes |
| Session variable | PHP code | Captures who made the change |

**To see audit logs on your website:**
1. ✅ Run `db/audit_triggers.sql`
2. ✅ Set `@current_user` in PHP
3. ✅ Make changes to prescriptions
4. ✅ Open http://localhost/Telemedicine/admin-audit.php

Done! 🎉
