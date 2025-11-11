# 🏥 Telemedicine Hospital Management System - Introduction

---

## 📌 What is This Project?

**Global Hospitals Telemedicine System** is an **online hospital management platform** that allows patients to book appointments with doctors, receive prescriptions, and enables doctors and admins to manage the entire workflow from anywhere.

Think of it as **Practo** or **HealthOne** - an online doctor appointment booking system.

---

## 🎯 Main Purpose

### **For Patients:**
- ✅ Register online
- ✅ Browse doctors
- ✅ Book appointments
- ✅ Receive prescriptions
- ✅ View appointment history

### **For Doctors:**
- ✅ View scheduled appointments
- ✅ Accept or reject appointments
- ✅ Write digital prescriptions
- ✅ Manage patient records

### **For Admins/Receptionists:**
- ✅ Manage doctors (add/remove)
- ✅ View all patients
- ✅ Monitor appointments
- ✅ View prescriptions
- ✅ Track audit logs (WHO changed WHAT)

---

## 💻 What We Used (Tech Stack)

### **Frontend (User Interface)**
| Technology | Purpose |
|------------|---------|
| **HTML** | Page structure |
| **CSS** | Styling & layout |
| **Bootstrap 4** | Responsive UI framework |
| **jQuery** | Interactive features (buttons, forms, animations) |
| **Font Awesome** | Icons (calendar, users, phone, etc.) |

**Result:** Modern, mobile-friendly web interface

---

### **Backend (Server Logic)**
| Technology | Purpose |
|------------|---------|
| **PHP 7.2+** | Server-side programming language |
| **Apache** | Web server (part of XAMPP) |
| **Composer** | PHP package manager |
| **TCPDF** | Generate prescription PDFs |

**Result:** Server processes logins, appointments, prescriptions

---

### **Database (Data Storage)**
| Technology | Purpose |
|------------|---------|
| **MySQL 10.1.31** | Database management system |
| **InnoDB** | Engine with transaction support |
| **SQL Triggers** | Automatic audit logging |
| **phpMyAdmin** | Database admin interface |

**Result:** Stores all patient, doctor, appointment, and audit data

---

### **Development Tools**
| Tool | Purpose |
|------|---------|
| **XAMPP** | Local development server (Apache + MySQL + PHP) |
| **VS Code** | Code editor |
| **Git** | Version control |
| **Composer** | Dependency management |

**Result:** Complete development environment on Windows machine

---

## 🏗️ Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    USER (Browser)                           │
│                                                             │
│  Patient/Doctor/Admin accesses: http://localhost/           │
│  Telemedicine/index.php                                     │
└────────────────────┬────────────────────────────────────────┘
                     │ HTTP Request
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              WEB SERVER (Apache)                            │
│                                                             │
│  Receives request, routes to PHP files                      │
│  Location: C:\xampp\apache\...                              │
└────────────────┬──────────────────────────────────────────┘
                 │ Executes PHP
                 ▼
┌─────────────────────────────────────────────────────────────┐
│           PHP APPLICATION (Backend Logic)                   │
│                                                             │
│  • index.php - Login page                                   │
│  • func.php - Patient authentication                        │
│  • admin-panel.php - Patient dashboard                      │
│  • doctor-panel.php - Doctor dashboard                      │
│  • prescribe.php - Write prescription                       │
│  • admin-panel1.php - Admin dashboard                       │
│                                                             │
│  Location: C:\xampp\htdocs\Telemedicine\                    │
└────────────────┬──────────────────────────────────────────┘
                 │ Queries database
                 ▼
┌─────────────────────────────────────────────────────────────┐
│             MYSQL DATABASE (Data Storage)                   │
│                                                             │
│  Tables:                                                    │
│  • patreg - Patient registration                            │
│  • doctb - Doctor information                               │
│  • appointmenttb - Appointments                             │
│  • prestb - Prescriptions                                   │
│  • audit_log - Change history (NEW)                         │
│                                                             │
│  Location: C:\xampp\mysql\data\myhmsdb\                     │
└────────────────────────────────────────────────────────────┘
                 │
                 ▼ TRIGGERS automatically log changes
         ┌──────────────────┐
         │   audit_log      │
         │ Stores: WHO/     │
         │ WHAT/WHEN        │
         └──────────────────┘
                 │
                 ▼ Admin views via
         admin-audit.php (new feature)
```

---

## 📁 Project Files Summary

### **Total Files: ~80**

**Categories:**

| Category | Count | Examples |
|----------|-------|----------|
| PHP Pages | 20 | index.php, admin-panel.php, doctor-panel.php |
| CSS Stylesheets | 15+ | style.css, bootstrap.min.css, color themes |
| JavaScript | 10+ | custom.js, jQuery plugins |
| Documentation | 8 | README.md, PROJECT_FILE_OVERVIEW.md, AUDIT_GUIDE.md |
| Database | 2 | myhmsdb.sql, audit_triggers.sql |
| Vendor (Libraries) | 20+ | Bootstrap, jQuery, Font Awesome, TCPDF |
| Images | Multiple folders | Logos, backgrounds, user photos |

---

## 🔄 How It Works (Simple Example)

### **Scenario: Patient Books Appointment**

```
1. Patient visits http://localhost/Telemedicine/index.php
   ↓
2. Patient enters email & password in "Patient" tab
   ↓
3. Clicks "Login" → Submits to func.php
   ↓
4. func.php checks database (patreg table)
   ✅ If valid → Sets session variables
   ❌ If invalid → Shows error
   ↓
5. Patient redirected to admin-panel.php (dashboard)
   ↓
6. Patient sees form: "Book Appointment"
   - Select Doctor (dropdown)
   - Choose Date & Time
   ↓
7. Validates:
   - Is date/time in future? ✅
   - Is doctor available? ✅
   ↓
8. Inserts into appointmenttb
   ↓
9. Success! "Appointment booked"
```

---

### **Scenario: Doctor Writes Prescription**

```
1. Doctor logs in → doctor-panel.php
   ↓
2. Doctor sees list of appointments
   ↓
3. Doctor clicks "Prescribe" on appointment
   ↓
4. Redirected to prescribe.php with patient details
   ↓
5. Doctor fills form:
   - Disease: "Fever"
   - Allergy: "Penicillin"
   - Prescription: "Take paracetamol 500mg twice daily"
   ↓
6. Clicks "Submit Prescription"
   ↓
7. PHP inserts into prestb (prescriptions table)
   ↓
8. ⚡ TRIGGER FIRES AUTOMATICALLY ⚡
   ↓
9. Trigger logs to audit_log:
   - WHO: Dr. Ganesh
   - WHAT: Created prescription #14
   - WHEN: 2025-01-15 10:30:45
   - New data: doctor, patient, disease, allergy, prescription
   ↓
10. Success! "Prescribed successfully!"
```

---

### **Scenario: Admin Views Audit Log**

```
1. Receptionist logs in → admin-panel1.php
   ↓
2. Clicks "Audit Log" in sidebar menu
   ↓
3. Redirected to admin-audit.php
   ↓
4. Sees table showing:
   ┌─────────────────────────────────────────┐
   │ # │ Table │ Action │ By        │ When    │
   ├───┼───────┼────────┼───────────┼─────────┤
   │ 1 │ prestb│ INSERT │ Dr.Ganesh │ 10:30   │
   │ 2 │ prestb│ UPDATE │ Dr.Ganesh │ 10:35   │
   │ 3 │ prestb│ DELETE │ Admin     │ 10:40   │
   └─────────────────────────────────────────┘
   ↓
5. Clicks "1" to see old vs new values
   ↓
6. Can prove who changed what when (Compliance!)
```

---

## 🎨 User Interfaces

### **1. Login Page (index.php)**
```
┌─────────────────────────────────────────┐
│  GLOBAL HOSPITALS                       │
├─────────────────────────────────────────┤
│  [Patient] [Doctor] [Receptionist]      │
│                                         │
│  Email:    [___________________]        │
│  Password: [___________________]        │
│  [   Login   ]                          │
└─────────────────────────────────────────┘
```

**Features:**
- 3 tabs for different user types
- Email & password fields
- Form validation (real-time)

---

### **2. Patient Dashboard (admin-panel.php)**
```
┌────────────────────────────────────────────┐
│ Hi, John! [Logout]                        │
├────────────────────────────────────────────┤
│ Book Appointment                           │
│ Select Doctor: [Dr. Ganesh ▼]            │
│ Choose Date: [2025-01-20]                 │
│ Choose Time: [3:00 PM]                    │
│ [Book Appointment]                        │
│                                            │
│ My Appointments                            │
│ ┌────────────────────────────────────────┐│
│ │ Doctor   │ Date  │ Time  │ Status     ││
│ ├──────────┼───────┼───────┼────────────┤│
│ │ Dr. Ganesh│ Jan20│ 3PM  │ Confirmed  ││
│ │ Dr. Priya │ Jan25│ 4PM  │ Pending    ││
│ └────────────────────────────────────────┘│
└────────────────────────────────────────────┘
```

---

### **3. Doctor Dashboard (doctor-panel.php)**
```
┌────────────────────────────────────────────┐
│ Dr. Ganesh's Panel [Logout]               │
├────────────────────────────────────────────┤
│ My Appointments Today                      │
│ ┌────────────────────────────────────────┐│
│ │ Patient  │ Time │ [Accept] [Prescribe]││
│ ├──────────┼──────┼────────┼────────────┤│
│ │ John Doe │ 3PM  │ ✅     │ 💊        ││
│ │ Jane Doe │ 4PM  │ ❌     │ 💊        ││
│ └────────────────────────────────────────┘│
└────────────────────────────────────────────┘
```

---

### **4. Admin Dashboard (admin-panel1.php)**
```
┌────────────────────────────────────────────┐
│ ADMIN PANEL [Logout]                      │
├─────────────┬──────────────────────────────┤
│ MENU        │ CONTENT AREA                │
│ ─────────── │                             │
│ Dashboard   │ Overview stats              │
│ Doctor List │ All doctors (add/remove)    │
│ Patient List│ All patients               │
│ Appointments│ Manage appointments         │
│ Prescriptions
│ Queries     │ Search records              │
│ Audit Log   │ ⭐ NEW: View changes       │
│ Settings    │                             │
└─────────────┴──────────────────────────────┘
```

---

## 🆕 What's New (Audit Log Feature)

### **Added Components:**

1. **`db/audit_triggers.sql`** - 3 SQL triggers that automatically log:
   - Prescription INSERT (new prescription created)
   - Prescription UPDATE (prescription changed)
   - Prescription DELETE (prescription deleted)

2. **`admin-audit.php`** - Web interface to view audit logs:
   - Shows all changes in a searchable table
   - Color-coded badges (Green=INSERT, Yellow=UPDATE, Red=DELETE)
   - View who changed it, what changed, when

3. **`admin-panel1.php` (Updated)** - Added "Audit Log" menu link

4. **Documentation:**
   - `AUDIT_GUIDE.md` - How to use audit log
   - `WHAT_IS_AUDIT_LOG.md` - Why it matters
   - `DATABASE_TRIGGERS_CONCEPT.md` - How triggers work

### **Benefits:**
✅ **Compliance** - Prove regulatory compliance (HIPAA, medical laws)  
✅ **Security** - Track unauthorized changes  
✅ **Recovery** - See what was deleted (can restore)  
✅ **Accountability** - Know who did what  

---

## 📊 Key Statistics

| Metric | Value |
|--------|-------|
| Total PHP Files | 20+ |
| Database Tables | 7+ |
| CSS Files | 15+ |
| JavaScript Files | 10+ |
| Documentation Pages | 8 |
| Users (roles) | 3 (Patient, Doctor, Admin) |
| Main Tables | patreg, doctb, appointmenttb, prestb, audit_log |

---

## 🚀 How to Run It

### **Simple Steps:**

1. **Download XAMPP** (Apache, MySQL, PHP bundled)
2. **Start XAMPP Control Panel:**
   - Click "Start" on Apache
   - Click "Start" on MySQL
3. **Import Database:**
   - Open `http://localhost/phpmyadmin`
   - Create database `myhmsdb`
   - Import `myhmsdb.sql`
4. **Activate Audit Logging:**
   - Import `db/audit_triggers.sql` (creates triggers)
5. **Open Application:**
   - Visit `http://localhost/Telemedicine/index.php`
6. **Login & Test:**
   - Patient: Use any email from patreg table
   - Doctor: Use any email from doctb table
   - Admin: Use any email from admin table

---

## 🔒 Security Level

### **Current:**
- ⚠️ Basic (suitable for learning/demo)

### **Vulnerabilities:**
- ❌ Passwords stored in plaintext
- ❌ No HTTPS
- ❌ SQL injection risk
- ❌ No CSRF protection

### **Production Fixes Needed:**
1. Hash passwords with bcrypt
2. Use prepared statements
3. Add HTTPS
4. Implement CSRF tokens
5. Add input validation
6. Use parameterized queries

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `README.md` | General project info |
| `PROJECT_FILE_OVERVIEW.md` | Detailed file-by-file explanation |
| `DATABASE_TRIGGERS_CONCEPT.md` | How triggers work (educational) |
| `AUDIT_GUIDE.md` | Step-by-step audit log usage |
| `WHAT_IS_AUDIT_LOG.md` | Why audit logs matter |
| `TEAM_DISTRIBUTION.md` | Team member roles |
| `MEMBER_1_ASSIGNMENT.md` | Individual assignments (1-5) |

---

## ✨ Summary

| Aspect | Details |
|--------|---------|
| **Name** | Global Hospitals Telemedicine System |
| **Type** | Hospital Management System (HMS) |
| **Purpose** | Online appointment booking & prescription tracking |
| **Users** | Patients, Doctors, Admins |
| **Frontend** | HTML, CSS, Bootstrap, jQuery |
| **Backend** | PHP 7.2+ |
| **Database** | MySQL/MariaDB with InnoDB |
| **Server** | Apache (XAMPP) |
| **New Feature** | Audit logging (tracks all changes) |
| **Status** | Fully functional, ready for learning |

---

## 🎓 Use Cases

✅ **Learning:** Understand hospital systems, PHP, MySQL, web development  
✅ **Portfolio:** Show employers your project  
✅ **Demo:** Present to clients/stakeholders  
✅ **Customize:** Add more features for real hospitals  
✅ **Scale:** Deploy to production with security upgrades  

---

**Ready to explore? Start at `index.php` and try booking an appointment!** 🏥📱
