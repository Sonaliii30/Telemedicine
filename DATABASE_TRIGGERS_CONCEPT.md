# Database Triggers - Complete Concept Guide

## What is a Trigger?

**Simple Definition:**
A trigger is an **automatic action** that fires (executes) when something happens in your database.

**Think of it like:** A motion sensor that triggers an alarm automatically — you don't have to manually press a button.

---

## Real-World Analogy

### Without Trigger (Manual Process):
```
Doctor creates prescription
  ↓
Admin must manually write down in log: "Doctor X created prescription"
  ↓
Admin manually enters: date, time, old values, new values
  ↓
Error-prone, time-consuming, easy to forget
```

### With Trigger (Automatic Process):
```
Doctor creates prescription
  ↓
Trigger fires AUTOMATICALLY
  ↓
Trigger records: date, time, doctor name, old/new values
  ↓
Perfect record, no manual work
```

---

## Basic Trigger Syntax

```sql
CREATE TRIGGER trigger_name
[BEFORE | AFTER] [INSERT | UPDATE | DELETE] ON table_name
FOR EACH ROW
BEGIN
    -- Code to execute (actions)
    INSERT INTO another_table VALUES (...);
END;
```

### Breaking it down:

| Part | Meaning | Example |
|------|---------|---------|
| `trigger_name` | Name of the trigger | `trg_prestb_after_insert` |
| `BEFORE/AFTER` | When to fire (before or after the event) | `AFTER` |
| `INSERT/UPDATE/DELETE` | Which action triggers it | `INSERT` |
| `ON table_name` | Which table to monitor | `ON prestb` |
| `FOR EACH ROW` | Execute once per row affected | (fixed) |
| `BEGIN...END` | The action code | SQL statements |

---

## Types of Triggers (3 Main Types)

### 1. **AFTER INSERT Trigger**

**When it fires:** After a new row is inserted

```sql
CREATE TRIGGER trg_prestb_after_insert
AFTER INSERT ON prestb
FOR EACH ROW
BEGIN
    -- This code runs AFTER new prescription is added
    INSERT INTO audit_log (table_name, action, record_id, changed_by, new_data)
    VALUES ('prestb', 'INSERT', NEW.ID, @current_user, 'doctor=...; disease=...;');
END;
```

**Use Case:** Log that a prescription was created

**Example Flow:**
```
Doctor runs: INSERT INTO prestb VALUES (...)
  ↓
Prescription inserted into prestb table
  ↓
TRIGGER FIRES
  ↓
Automatically runs: INSERT INTO audit_log VALUES (...)
  ↓
Audit entry created: "Dr. Ganesh created prescription #14"
```

---

### 2. **AFTER UPDATE Trigger**

**When it fires:** After a row is updated

```sql
CREATE TRIGGER trg_prestb_after_update
AFTER UPDATE ON prestb
FOR EACH ROW
BEGIN
    -- This code runs AFTER prescription is modified
    INSERT INTO audit_log (table_name, action, record_id, changed_by, old_data, new_data)
    VALUES ('prestb', 'UPDATE', NEW.ID, @current_user, OLD_VALUES, NEW_VALUES);
END;
```

**Use Case:** Log what changed in a prescription

**Example Flow:**
```
Doctor runs: UPDATE prestb SET disease='Cough' WHERE ID=14
  ↓
Prescription updated (disease changed from Fever to Cough)
  ↓
TRIGGER FIRES
  ↓
Automatically records:
  OLD: disease=Fever
  NEW: disease=Cough
  ↓
Audit entry: "Dr. Ganesh changed prescription #14: Fever → Cough"
```

---

### 3. **AFTER DELETE Trigger**

**When it fires:** After a row is deleted

```sql
CREATE TRIGGER trg_prestb_after_delete
AFTER DELETE ON prestb
FOR EACH ROW
BEGIN
    -- This code runs AFTER prescription is deleted
    INSERT INTO audit_log (table_name, action, record_id, changed_by, old_data)
    VALUES ('prestb', 'DELETE', OLD.ID, @current_user, OLD_VALUES);
END;
```

**Use Case:** Log what prescription was deleted (for recovery)

**Example Flow:**
```
Admin runs: DELETE FROM prestb WHERE ID=14
  ↓
Prescription deleted from prestb table
  ↓
TRIGGER FIRES
  ↓
Automatically records deleted values in audit_log
  ↓
Audit entry: "admin deleted prescription #14 (disease=Cough, patient=John Doe, ...)"
```

---

## Key Words in Triggers

### **NEW vs OLD**

In a trigger, `NEW` and `OLD` are special variables:

- **NEW** = new values (after change)
- **OLD** = old values (before change)

```sql
UPDATE prestb SET disease='Cough' WHERE ID=14;
-- After this runs, the trigger can access:
-- OLD.disease = 'Fever'  (what it was before)
-- NEW.disease = 'Cough'  (what it is now)
```

### **@current_user** (Session Variable)

```sql
-- Set from PHP:
SET @current_user = 'Dr. Ganesh';

-- Trigger can read it:
INSERT INTO audit_log (changed_by) VALUES (@current_user);
-- Result: changed_by = 'Dr. Ganesh'
```

---

## How Triggers Work in Your Audit System

### The Flow:

```
1. Doctor/Admin performs action (INSERT/UPDATE/DELETE prescription)
   ↓
2. Database modifies the prestb table
   ↓
3. Trigger AUTOMATICALLY fires (no manual code needed)
   ↓
4. Trigger code reads NEW and OLD values
   ↓
5. Trigger inserts record into audit_log
   ↓
6. Audit trail complete!
```

### Visual Timeline:

```
TIME → 

10:30 AM: Dr. Ganesh creates prescription
           ↓
         [INSERT INTO prestb]
           ↓
         TRIGGER FIRES (automatic)
           ↓
         [INSERT INTO audit_log] ← automatic
           ↓
         Audit entry recorded!

10:35 AM: Dr. Ganesh edits prescription
           ↓
         [UPDATE prestb]
           ↓
         TRIGGER FIRES (automatic)
           ↓
         [INSERT INTO audit_log] ← automatic
           ↓
         Old vs New values recorded!

11:00 AM: Admin deletes prescription
           ↓
         [DELETE FROM prestb]
           ↓
         TRIGGER FIRES (automatic)
           ↓
         [INSERT INTO audit_log] ← automatic
           ↓
         Deleted values recorded!
```

---

## Your Specific Trigger Code Explained

### From Your `db/audit_triggers.sql`:

```sql
-- AFTER INSERT trigger
CREATE TRIGGER trg_prestb_after_insert
AFTER INSERT ON prestb          -- After prescription is created
FOR EACH ROW
BEGIN
    INSERT INTO audit_log(...)
    VALUES(
        'prestb',               -- Table name
        'INSERT',               -- Action type
        NEW.ID,                 -- New prescription ID
        @current_user,          -- Current user from PHP
        CONCAT(...)             -- New values as string
    );
END
```

**What it does:**
1. Waits for INSERT into prestb
2. When INSERT happens, automatically runs
3. Records in audit_log: who did it, when, what was inserted
4. No manual code needed — happens automatically

---

## Why Use Triggers? (Benefits)

### ✅ **1. Automatic & No Manual Code**
```
WITHOUT trigger:
    Doctor inserts prescription
    Programmer must remember to write audit log code
    Easy to forget, causes inconsistency
    
WITH trigger:
    Doctor inserts prescription
    Trigger fires automatically
    Always records, consistent
```

### ✅ **2. Data Integrity**
- Every change is recorded
- Can't accidentally skip logging
- Triggers run EVERY TIME without exception

### ✅ **3. Audit Trail / Security**
- Know exactly who changed what
- Prove compliance with regulations (HIPAA)
- Detect unauthorized changes

### ✅ **4. Recovery**
- If prescription is deleted by mistake
- Check audit_log to see what was deleted
- Recreate the data manually if needed

### ✅ **5. Centralized Logic**
- Audit logic in trigger (one place)
- All prescription changes go through same trigger
- Easier to maintain

---

## Triggers vs Application Code

### Option 1: Log in Application Code (Without Trigger)

```php
// In PHP code (prescribe.php)
if (INSERT prescription succeeds) {
    // Log it manually
    INSERT INTO audit_log...
}

if (UPDATE prescription succeeds) {
    // Log it manually
    INSERT INTO audit_log...
}

if (DELETE prescription succeeds) {
    // Log it manually
    INSERT INTO audit_log...
}
```

**Problems:**
- ❌ Programmer must remember to add logging everywhere
- ❌ If someone inserts directly via SQL, logging is skipped
- ❌ Code duplication
- ❌ Easy to forget

### Option 2: Use Trigger (Better)

```sql
-- In database
CREATE TRIGGER trg_prestb_after_insert AFTER INSERT...
CREATE TRIGGER trg_prestb_after_update AFTER UPDATE...
CREATE TRIGGER trg_prestb_after_delete AFTER DELETE...
```

**Benefits:**
- ✅ Automatic for ANY insert/update/delete (including direct SQL)
- ✅ One place (database) to manage
- ✅ Can't be bypassed
- ✅ Centralized logic

---

## Performance Impact

### Overhead:
- Triggers add **small delay** per write operation
- Acceptable for most applications
- Your telemedicine system: negligible impact

### Trade-off:
```
Speed Loss:       ~1-5ms per INSERT/UPDATE/DELETE
Benefit Gained:   Complete audit trail, security, compliance
Worth it?         YES! Security > Speed for medical data
```

---

## Triggers in Your Project

### Current Triggers:
```
1. trg_prestb_after_insert  → Logs new prescriptions
2. trg_prestb_after_update  → Logs modified prescriptions
3. trg_prestb_after_delete  → Logs deleted prescriptions
```

### Table Being Monitored:
- `prestb` (prescriptions)

### What Gets Logged:
- Who changed it (`@current_user`)
- When (`CURRENT_TIMESTAMP`)
- What changed (OLD vs NEW values)
- Which record (record_id)

### Where Logs Go:
- `audit_log` table

---

## Advanced Trigger Concepts

### **BEFORE vs AFTER**

```sql
-- BEFORE INSERT: Runs BEFORE the insert happens
CREATE TRIGGER check_before_insert
BEFORE INSERT ON prestb
BEGIN
    -- Can validate data or prevent insert
    IF NEW.disease = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Disease required';
    END IF;
END

-- AFTER INSERT: Runs AFTER the insert happens
CREATE TRIGGER log_after_insert
AFTER INSERT ON prestb
BEGIN
    -- Record that insert happened
    INSERT INTO audit_log...
END
```

### **Conditional Triggers**

```sql
-- Only log if disease changed
IF OLD.disease != NEW.disease THEN
    INSERT INTO audit_log VALUES (...)
END IF
```

---

## Summary Table

| Aspect | Details |
|--------|---------|
| **What** | Automatic action that fires on DB change |
| **When** | BEFORE or AFTER INSERT/UPDATE/DELETE |
| **Where** | In the database (SQL) |
| **Why** | Audit trail, security, data integrity |
| **How** | Trigger definition in `db/audit_triggers.sql` |
| **Benefit** | Automatic logging, can't be bypassed |
| **Cost** | Small performance overhead (~1-5ms) |
| **In Your Project** | 3 triggers on `prestb` → audit_log |

---

## Real Example: What Actually Happens

### Step 1: Doctor Creates Prescription
```php
// PHP code (prescribe.php)
mysqli_query($con, "INSERT INTO prestb VALUES (...)");
```

### Step 2: Database Inserts Row
```
prestb table:
ID | doctor | patient | disease | prescription
14 | Ganesh | 1       | Fever   | Take rest
```

### Step 3: Trigger Fires (Automatic)
```
TRIGGER: trg_prestb_after_insert
ACCESS: NEW.ID=14, NEW.doctor='Ganesh', etc.
READ: @current_user='Dr. Ganesh'
```

### Step 4: Trigger Inserts Audit Record
```
audit_log table:
audit_id | table_name | action | record_id | changed_by | new_data
1        | prestb     | INSERT | 14        | Dr. Ganesh | doctor=Ganesh; patient=1; disease=Fever; prescription=Take rest
```

### Step 5: Admin Views Audit Log
```
http://localhost/Telemedicine/admin-audit.php
Shows: "Dr. Ganesh created prescription #14 at 2025-01-15 10:30:45"
```

---

## Bottom Line

**Trigger = Automatic Database Watchdog**
- Watches for changes (INSERT/UPDATE/DELETE)
- Automatically logs them
- Can't be bypassed
- Essential for security and compliance
- Small performance cost for big security benefit

**In Your Project:**
- 3 triggers automatically record prescription changes
- No manual code needed
- Complete audit trail guaranteed
- Compliance-ready for medical regulations

---

## Key Takeaways

1. ✅ **Automatic** — Fires without manual code
2. ✅ **Reliable** — Can't be bypassed or forgotten
3. ✅ **Audit Trail** — Complete record of changes
4. ✅ **Compliance** — Meets regulatory requirements
5. ✅ **Recovery** — Can restore deleted data
6. ✅ **Security** — Track unauthorized changes
7. ✅ **Centralized** — Database-level logic, not app-level

**Think:** "A trigger is like a security guard that watches your database and writes down everything that changes." 🔐📋
