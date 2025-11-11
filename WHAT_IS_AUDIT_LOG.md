# What Does Audit Log Do? - Simple Explanation

## TL;DR (Too Long; Didn't Read)
**Audit log = A security camera for your database. It records WHO changed WHAT, WHEN, and HOW.**

---

## Real-World Scenario

Imagine you're running a hospital and a prescription gets deleted by mistake. Your question:
- ❌ **Who deleted it?**
- ❌ **When did they delete it?**
- ❌ **What was in the prescription?**
- ❌ **Can we recover it?**

**Without audit log:** You can't answer any of these. Data is gone. 😱

**With audit log:** You have a complete record of everything. 📋✅

---

## What Audit Log Records (For Each Change)

### 1. **WHO** — Changed By
- Username/name of the person who made the change
- Example: "Dr. Ganesh", "admin", "receptionist"

### 2. **WHAT** — Old Data & New Data
- **Old Data** = values BEFORE the change
- **New Data** = values AFTER the change
- Example:
  - Old: `disease=Flu`
  - New: `disease=Cough`

### 3. **WHEN** — Changed At
- Exact date and time of the change
- Example: `2025-01-15 10:30:45`

### 4. **ACTION** — INSERT, UPDATE, DELETE
- **INSERT** = new prescription created
- **UPDATE** = existing prescription modified
- **DELETE** = prescription removed

### 5. **WHICH RECORD** — Record ID
- Which prescription was affected
- Example: Prescription ID #14

---

## Example Audit Log Entries

```
#  | Action | Record | Changed By   | When              | Old Data       | New Data
───┼────────┼────────┼──────────────┼───────────────────┼────────────────┼─────────────
1  | INSERT | 14     | Dr. Ganesh   | 2025-01-15 10:30  | (empty)        | disease=Fever; prescription=Take rest
2  | UPDATE | 14     | Dr. Ganesh   | 2025-01-15 10:35  | disease=Fever  | disease=Cough
3  | DELETE | 14     | admin        | 2025-01-15 11:00  | disease=Cough  | (empty)
```

**What happened:**
1. Dr. Ganesh created a new prescription for patient with Fever
2. Dr. Ganesh later changed it to Cough
3. Admin deleted the prescription

---

## Real-World Use Cases

### 🔍 **Track Mistakes**
- "Someone deleted a prescription — I need to know who and restore it"
- **Solution:** Check audit log → find the DELETE entry → see who did it → restore the data

### 🔒 **Compliance & Auditing**
- Hospital regulations require: "Prove that only authorized people changed medical records"
- **Solution:** Show audit log as proof

### 🚨 **Detect Fraud/Unauthorized Changes**
- "A prescription for expensive medicine was added by unknown person"
- **Solution:** Check audit log → see who created it → investigate

### 📋 **Answer Questions**
- "What was the original prescription before the doctor edited it?"
- **Solution:** Check audit log → see old vs new data

### ⏮️ **Restore Deleted Data**
- "I accidentally deleted a patient's entire prescription"
- **Solution:** Check audit log → get the deleted data → recreate it manually

---

## How It Works (Behind the Scenes)

### Without Audit Log:
```
User Action → Database Change → Done
(No record kept)
```

### With Audit Log (Triggers):
```
User Action → Database Change → Trigger Fires Automatically → Record Stored in audit_log
                                        ↓
                           "Who changed what when?"
```

**Triggers** are like automated rules:
- **AFTER INSERT:** When a new prescription is created → automatically log it
- **AFTER UPDATE:** When a prescription is modified → automatically log old and new values
- **AFTER DELETE:** When a prescription is deleted → automatically log the deleted data

---

## Where to See It

### In Your Telemedicine Website:
1. Go to Admin Panel: `http://localhost/Telemedicine/admin-panel1.php`
2. Log in as admin
3. Click **"Audit Log"** menu item (on left sidebar)
4. See a table with all prescription changes

### In Database (phpMyAdmin):
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select your database `myhmsdb`
3. Click on table `audit_log`
4. Browse the data (same information as above)

---

## What Data Is Stored in Audit Log

| Column | What It Stores | Example |
|--------|---|---|
| `audit_id` | Unique ID for each log entry | 1, 2, 3, ... |
| `table_name` | Which table was changed | `prestb` (prescriptions) |
| `action` | INSERT, UPDATE, or DELETE | `UPDATE` |
| `record_id` | Which row/record ID was affected | `14` |
| `changed_by` | Username of person who changed it | `Dr. Ganesh` |
| `changed_at` | Date & time of change | `2025-01-15 10:30:45` |
| `old_data` | Values BEFORE the change | `disease=Fever` |
| `new_data` | Values AFTER the change | `disease=Cough` |

---

## Security & Compliance Benefits

✅ **Accountability** — Everyone's actions are recorded  
✅ **Traceability** — Know exactly what changed and when  
✅ **Compliance** — Meet HIPAA/medical data regulations  
✅ **Recovery** — Restore accidentally deleted data  
✅ **Fraud Detection** — Identify unauthorized changes  
✅ **Legal Proof** — Prove who did what (useful in disputes)  

---

## Performance Note

- Triggers add a small overhead per database write (INSERT/UPDATE/DELETE)
- The `audit_log` table grows over time (add retention policy to archive old entries)
- For your telemedicine system: minimal impact on speed
- Acceptable trade-off for security/compliance benefits

---

## Current Setup in Your Project

### ✅ What's in place:
- Database table: `audit_log` (stores all changes)
- Triggers: 3 triggers on `prestb` (prescriptions) table
  - `trg_prestb_after_insert` — logs new prescriptions
  - `trg_prestb_after_update` — logs modified prescriptions
  - `trg_prestb_after_delete` — logs deleted prescriptions
- Admin page: `admin-audit.php` (view the logs)
- Menu link: Added to admin panel for easy access

### 🔧 How to use:
1. When doctor/admin creates/edits/deletes a prescription
2. Trigger automatically fires
3. Change is recorded in `audit_log`
4. Admin can view history anytime in `admin-audit.php`

---

## Summary

| Aspect | Details |
|--------|---------|
| **Purpose** | Track all changes to prescriptions (who, what, when) |
| **Triggers** | Automatically log every INSERT/UPDATE/DELETE |
| **Storage** | `audit_log` table in database |
| **View** | `admin-audit.php` (admin panel) |
| **Use Cases** | Compliance, security, recovery, fraud detection |
| **Performance** | Minimal impact, highly recommended |

---

## Bottom Line

**Audit log = Digital record keeper**
- Never forgets who changed what
- Essential for medical/healthcare systems
- Helps comply with regulations (HIPAA)
- Saves you in case of disputes or accidents

**Think of it like:** A security camera for your database that records every change automatically. 📹➡️🗄️
