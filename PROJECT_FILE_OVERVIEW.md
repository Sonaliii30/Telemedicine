# Telemedicine Hospital Management System - Complete File Overview

---

## 🏥 Project Summary

**Name:** Global Hospitals Telemedicine System  
**Type:** Hospital Management System (HMS)  
**Technology:** PHP + MySQL + Bootstrap + jQuery  
**Purpose:** Online appointment booking, doctor-patient management, and prescription tracking  
**Users:** Patients, Doctors, Receptionists, Admins

---

## 📁 Directory Structure & File Functions

### **Core Configuration**

#### `include/config.php` ⚙️
**Purpose:** Database connection configuration  
**What it does:**
- Defines database credentials (localhost, root, myhmsdb)
- Creates mysqli connection object `$con`
- Used by every PHP file that needs database access

```php
// Typical usage in other files:
include('include/config.php');
$result = mysqli_query($con, $query);
```

---

#### `include/header.php` 📄
**Purpose:** Common header template  
**Current State:** Minimal header (mostly empty)  
**Note:** Not actively used in project; navbar is implemented inline in each page

---

#### `include/sidebar.php` 📄
**Purpose:** Sidebar template for admin panels  
**Current State:** Exists but may not be used (check admin-panel files for actual sidebar)

---

#### `include/footer.php` 📋
**Purpose:** Common footer template  
**Current State:** May be minimal or unused

---

#### `include/checklogin.php` 🔐
**Purpose:** Session validation  
**Functionality:** Ensures user is logged in before accessing certain pages  
**Typical usage:** Include at top of protected pages (doctor panel, admin panel)

---

#### `include/setting.php` ⚙️
**Purpose:** Global settings/configuration  
**Note:** Likely contains timezone settings, constants, or feature flags

---

### **Authentication & Login System**

#### `index.php` 🔓 **MAIN ENTRY POINT**
**Purpose:** Public landing page and login/registration interface  
**Contains 3 tabs:**
1. **Patient Tab** → Registration & Login form for patients
2. **Doctor Tab** → Login form for doctors  
3. **Receptionist Tab** → Login form for receptionists/admins

**Key Features:**
- Bootstrap responsive design
- Form validation with JavaScript (password match check, length validation)
- Redirects to `func.php` for processing
- Links to About & Contact pages

**Flow:**
```
User visits index.php
  ↓
Selects Patient/Doctor/Receptionist tab
  ↓
Fills registration/login form
  ↓
Submits to func.php via POST
  ↓
func.php processes & redirects to appropriate panel
```

---

#### `func.php` 🔑 **AUTHENTICATION HANDLER**
**Purpose:** Process patient & receptionist login and registration  
**Main Functions:**

1. **Patient Login:**
   - Retrieves email & password from form
   - Queries `patreg` table
   - Sets session variables: `$_SESSION['pid']`, `$_SESSION['username']`, `$_SESSION['fname']`, etc.
   - Redirects to `admin-panel.php` (patient dashboard)

2. **Doctor Registration (from admin panel):**
   - Accepts doctor name, password, email, fees
   - Inserts into `doctb` table

3. **Appointment Payment Update:**
   - Updates appointment status in `appointmenttb`

**Contains:** SQL INSERT/UPDATE queries (note: vulnerable to SQL injection - no prepared statements)

**Issues Found:**
- ❌ Direct SQL queries (SQL injection vulnerability)
- ❌ Passwords stored in plaintext (security risk)

---

#### `func1.php` 🔑 **DOCTOR AUTHENTICATION**
**Purpose:** Process doctor login  
**Main Function:**
- Retrieves doctor email & password
- Queries `doctb` table
- Sets session variables: `$_SESSION['dname']`, `$_SESSION['demail']`, etc.
- Redirects to `doctor-panel.php`

---

#### `func2.php` 🔑 **RECEPTIONIST/ADMIN AUTHENTICATION**
**Purpose:** Process admin/receptionist login  
**Main Function:**
- Similar to func1.php but for admin credentials
- Queries admin/receptionist table
- Sets session for admin panel access

---

#### `func3.php` 📊
**Purpose:** Utility functions for data processing  
**Likely contains:** Helper functions for calculations, data formatting, etc.

---

#### `newfunc.php` 🔧
**Purpose:** New/additional utility functions  
**Likely contains:** Functions created during development

---

### **Patient Interface**

#### `admin-panel.php` 👤 **PATIENT DASHBOARD**
**Purpose:** Patient main interface after login  
**Key Features:**
1. **Display Patient Info** (fetched from session)
2. **Book Appointments:**
   - Select doctor
   - Choose date and time
   - Validation: prevents past dates/times, checks doctor availability
3. **View Appointments:**
   - List of patient's appointments with doctors
4. **Manage Appointments:**
   - View status, cancel, reschedule options

**Main Workflow:**
```
Patient logs in (func.php sets session)
  ↓
Redirected to admin-panel.php
  ↓
Fetches patient details from session
  ↓
Displays form to book appointment
  ↓
Validates appointment date/time against current time & doctor availability
  ↓
Inserts into appointmenttb if valid
```

**Tables Accessed:**
- `patreg` (patient registration)
- `appointmenttb` (appointments)
- `doctb` (doctors list)

---

#### `admin-panel1.php` 👨‍💼 **RECEPTIONIST/ADMIN DASHBOARD**
**Purpose:** Admin interface for managing doctors, patients, appointments  
**Key Features:**
1. **Dashboard Tab** → Overview
2. **Doctor List** → View all doctors
3. **Patient List** → View all patients
4. **Appointments** → Manage all appointments
5. **Prescriptions** → View prescriptions
6. **Add Doctor** → Form to add new doctor to system
7. **Delete Doctor** → Form to remove doctor
8. **Queries** → Search appointments/prescriptions
9. **Audit Log** → View audit trail of all changes (NEW FEATURE)

**Processing:**
- `$_POST['docsub']` → Add doctor to `doctb`
- `$_POST['docsub1']` → Delete doctor from `doctb`

**UI:** Bootstrap 4 with left sidebar menu and tab-based content

---

### **Doctor Interface**

#### `doctor-panel.php` 👨‍⚕️ **DOCTOR DASHBOARD**
**Purpose:** Doctor interface for managing appointments and writing prescriptions  
**Key Features:**
1. **My Appointments** → List of appointments scheduled for this doctor
2. **Accept/Reject Appointments** → Update `doctorStatus` in `appointmenttb`
3. **Prescribe** → Write prescription for patient
4. **Search** → Find specific appointments

**Main Actions:**
- Cancel appointment (update `appointementtb` set `doctorStatus=0`)
- Redirect to `prescribe.php` for writing prescription
- Accept appointment

**Session Requirement:** `$_SESSION['dname']` (doctor name)

---

#### `prescribe.php` 💊 **PRESCRIPTION WRITING**
**Purpose:** Doctor writes prescription for a patient  
**Input (via GET or POST):**
- Patient ID (`pid`)
- Appointment ID (`ID`)
- Appointment date/time
- Patient name (`fname`, `lname`)

**Form Fields:**
- Disease (patient's condition)
- Allergy (patient allergies)
- Prescription (medicine & instructions)

**Processing:**
- Inserts into `prestb` (prescriptions table)
- Doctor name from `$_SESSION['dname']`
- **NOTE:** This insertion triggers the AUDIT TRIGGERS we created!

**Flow:**
```
Doctor clicks "Prescribe" button
  ↓
Prescribe.php opens with patient details (from GET)
  ↓
Doctor fills form
  ↓
Submits POST request
  ↓
Inserts into prestb (TRIGGERS FIRE → audit_log recorded)
  ↓
Alert: "Prescribed successfully!"
```

---

### **Search & Query Pages**

#### `search.php` 🔍 **DOCTOR SEARCH**
**Purpose:** Doctor searches for specific appointments  
**Input:** Contact number  
**Query:** Searches `appointmenttb` where contact matches and doctor is current user  
**Output:** Bootstrap table with matching appointments

---

#### `appsearch.php` 🔍 **APPOINTMENT SEARCH**
**Purpose:** Search appointments across system  
**Similar to search.php but broader scope**

---

#### `doctorsearch.php` 🔍 **DOCTOR SEARCH**
**Purpose:** Search doctors by name/specialty  
**Input:** Doctor name or specialty  
**Output:** List of matching doctors

---

#### `patientsearch.php` 🔍 **PATIENT SEARCH**
**Purpose:** Search patients in system  
**Input:** Patient name, email, or contact  
**Output:** Matching patient records

---

#### `messearch.php` 🔍 **MESSAGE/FEEDBACK SEARCH**
**Purpose:** Search messages or feedback  
**Implementation:** TBD based on project needs

---

### **Logout & Error Handling**

#### `logout.php` 🚪 **LOGOUT**
**Purpose:** Safely logout user  
**Function:**
- `session_start()` and `session_destroy()`
- Display confirmation message
- Link back to login page
- Used by all user types

---

#### `logout1.php` 🚪 **ALTERNATIVE LOGOUT**
**Purpose:** Additional logout handler  
**Note:** May be duplicate or variant for different user types

---

#### `error.php` ❌ **ERROR PAGE**
**Purpose:** Display error message (invalid login, connection failed, etc.)  
**Message:** "Invalid Email-Id or Password! Please try again."  
**Link:** "Try Again" button back to `index1.php`

---

#### `error1.php` ❌ **ALTERNATIVE ERROR**
**Purpose:** Different error handling page  
**Note:** May be used for different error scenarios

---

#### `error2.php` ❌ **ANOTHER ERROR PAGE**
**Purpose:** Additional error display  
**Note:** Possible for specific error types

---

#### `index1.php` 📝
**Purpose:** Alternative login page or redirect  
**Note:** Referenced in error pages and logout

---

### **Audit & Compliance Features (NEW)**

#### `admin-audit.php` 📋 **AUDIT LOG VIEWER**
**Purpose:** Admin views audit trail of all database changes  
**Features:**
- Displays all changes from `audit_log` table
- Shows: Table, Action (INSERT/UPDATE/DELETE), Record ID, Changed By, Timestamp
- Color-coded badges: Green (INSERT), Yellow (UPDATE), Red (DELETE)
- Filter by action or table name
- View old vs new values

**Access:** Receptionist/Admin dashboard → Menu → "Audit Log"  
**URL:** `http://localhost/Telemedicine/admin-audit.php`

**Database Query:**
```php
SELECT * FROM audit_log ORDER BY audit_id DESC
```

---

### **Database & Storage**

#### `myhmsdb.sql` 🗄️ **DATABASE SCHEMA**
**Purpose:** Complete database structure and tables  
**Tables Created:**
1. `patreg` - Patient registration
2. `doctb` - Doctors
3. `appointmenttb` - Appointments
4. `prestb` - Prescriptions
5. `admin` - Admin/Receptionist accounts
6. `feedback` - User feedback
7. Many others...

**Engine:** InnoDB (supports transactions)  
**Charset:** utf8mb4, latin1 (multilingual support)

**Storage:** Located in MySQL at `/xampp/mysql/data/myhmsdb/`

---

#### `db/audit_triggers.sql` 🔔 **AUDIT TRIGGERS**
**Purpose:** SQL triggers for automatic audit logging  
**Triggers Created:**
1. `trg_prestb_after_insert` - Logs new prescriptions
2. `trg_prestb_after_update` - Logs prescription updates
3. `trg_prestb_after_delete` - Logs deleted prescriptions

**Audit Table:** `audit_log` (stores: audit_id, table_name, action, record_id, changed_by, changed_at, old_data, new_data)

**How It Works:**
- When doctor inserts prescription in `prestb`
- Trigger automatically fires
- Logs WHO did it, WHAT changed, WHEN
- Record stored in `audit_log`

**Status:** Execute this file in phpMyAdmin to activate triggers

---

### **Frontend & Assets**

#### `index.php` / `contact.html` / `services.html` 📄
**Purpose:** Public-facing web pages  
- `index.php` - Main login page (PHP version with forms)
- `contact.html` - Contact page
- `services.html` - Services/About page

---

#### `contact.php` 📧
**Purpose:** Backend for contact form submissions  
**Likely processes:** Name, email, message → possibly sends email or stores in DB

---

#### `style1.css` / `style2.css` 🎨
**Purpose:** Custom CSS styling for pages  
- `style1.css` - Primary styling (login, public pages)
- `style2.css` - Additional styling (alternate theme or specific pages)

---

#### `contact.css` 🎨
**Purpose:** Specific styling for contact page

---

### **CSS Stylesheets (Various)**

#### `css/` directory
- `style.css` - Main stylesheet
- `bootstrap.min.css` - Bootstrap framework CSS
- `animate.css` - Animation library
- `owl.carousel.css` - Carousel plugin styling
- `nivo-lightbox.css` - Lightbox gallery styling

---

#### `bodybg/` directory
Contains background themes (bg1.css through bg10.css)

---

#### `color/` directory
Contains color theme files:
- `default.css` - Default color scheme
- `blue.css`, `green.css`, `red.css`, `pink.css`, etc. - Color variations

---

### **JavaScript & Plugins**

#### `js/` directory

**Core Libraries:**
- `jquery.min.js` - jQuery (DOM manipulation)
- `bootstrap.min.js` - Bootstrap JavaScript components
- `jquery-1.10.2.js` - Older jQuery version
- `jquery.appear.js` - Detect element appearance
- `jquery.easing.min.js` - Animation easing
- `jquery.scrollTo.js` - Smooth scrolling
- `wow.min.js` - Reveal animations on scroll

**Custom Scripts:**
- `custom.js` - Project-specific JavaScript

**Plugins:**
- `owl.carousel.min.js` - Image carousel
- `nivo-lightbox.min.js` - Image gallery lightbox
- `stellar.js` - Parallax effect

---

### **Fonts & Icons**

#### `font-awesome/` directory
- Contains Font Awesome icon library (CSS + font files)
- Used throughout project for icons (user, calendar, envelope, etc.)

---

#### `fonts/` directory
- Custom fonts for typography
- Google Fonts integration

---

### **Images & Media**

#### `images/` directory
- Logos, banners, icons

#### `img/` directory
- Organized subfolders:
  - `bodybg/` - Background images
  - `dummy/` - Placeholder images
  - `parallax/` - Parallax effect images
  - `photo/` - User/doctor photos
  - `slides/` - Carousel slides
  - `team/` - Team member photos
  - `testimonials/` - Testimonial images

---

### **Additional Libraries & Tools**

#### `vendor/` directory
Contains composer packages (managed by Composer)
- `bootstrap/` - Bootstrap framework
- `fontawesome/` - Font Awesome
- `datatables/` - Data table plugin
- `fullcalendar/` - Calendar widget
- `ckeditor/` - Rich text editor
- `Chart.js/` - Chart library
- And many others...

---

#### `TCPDF/` directory
**Purpose:** PDF generation library  
**Used for:** Generating prescription PDFs, appointment confirmations, etc.

**Key Files:**
- `tcpdf.php` - Main class
- `tcpdf_autoconfig.php` - Configuration
- `config/` - Settings
- `fonts/` - Font support for PDFs

---

#### `plugins/` directory
Third-party plugins:
- `cubeportfolio/` - Portfolio/gallery plugin

---

### **Documentation Files**

#### `README.md` 📖
**Purpose:** Project overview and setup guide  
**Contents:** Quick start, features, tech stack, installation

---

#### `composer.json` 📦
**Purpose:** PHP dependency management  
**Contains:** Required packages and versions  
**Usage:** Run `composer install` to install dependencies

---

#### `DATABASE_TRIGGERS_CONCEPT.md` 📚
**Purpose:** Educational guide on database triggers  
**Contents:** 
- What triggers are
- How they work
- Examples from your project
- Benefits (audit trail, security, compliance)

---

#### `AUDIT_GUIDE.md` 📋
**Purpose:** Step-by-step guide to using audit log  
**Contents:**
- How to access audit log
- What to look for
- Real-world scenarios
- Setup instructions

---

#### `WHAT_IS_AUDIT_LOG.md` 🔍
**Purpose:** Explain audit log concept simply  
**Contents:**
- Real-world examples
- Use cases (compliance, fraud detection, recovery)
- Benefits
- Current implementation

---

#### `TEAM_DISTRIBUTION.md` 👥
**Purpose:** Team member role assignments  
**Contains:** Who is responsible for what components

---

#### `MEMBER_1_ASSIGNMENT.md` through `MEMBER_5_ASSIGNMENT.md` 👤
**Purpose:** Individual team member task assignments  
**Each contains:** Specific responsibilities and deliverables

---

## 🔄 User Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                   VISITOR (Unauthenticated)                 │
└────────────────┬────────────────────────────────────────────┘
                 │ Visit localhost/Telemedicine/index.php
                 ▼
        ┌────────────────────┐
        │   index.php        │ ◄─── Choose: Patient/Doctor/Admin
        │ (Login page with   │
        │  3 tabs)           │
        └────────┬───────────┘
                 │ Submit form
        ┌────────▼──────────────────────────────────────────────┐
        │         func.php / func1.php / func2.php             │
        │  (Validate credentials & set session variables)       │
        └────────┬──────────────────────────────────────────────┘
                 │
        ┌────────┴─────────────────────────────────────┐
        │                                              │
    ┌───▼────────────┐                    ┌───────────▼──────┐
    │ PATIENT PATH   │                    │ DOCTOR PATH      │
    └────────────────┘                    └──────────────────┘
        │                                      │
    admin-panel.php                    doctor-panel.php
    (Patient Dashboard)                (Doctor Dashboard)
        │                                      │
    ┌───▼──────────────┐              ┌──────▼──────────────┐
    │ • Book Appt      │              │ • View My Appts     │
    │ • View Appts     │              │ • Accept/Reject     │
    │ • Check Status   │              │ • Write Prescription│
    │ • Logout         │              │ • Logout            │
    └───────────────────┘              └─────────────────────┘
                                              │
                                        prescribe.php
                                    (Write Prescription)
                                              │
                                        INSERT prestb
                                              │
                                        ┌─────▼──────────────┐
                                        │ TRIGGERS FIRE!     │
                                        │ audit_log recorded │
                                        │ WHO/WHAT/WHEN      │
                                        └────────────────────┘

    ┌────────────────────────────────────────────────────────┐
    │ ADMIN PATH: admin-panel1.php (Receptionist Dashboard)  │
    ├────────────────────────────────────────────────────────┤
    │ • View All Doctors                                     │
    │ • View All Patients                                    │
    │ • View All Appointments                                │
    │ • View Prescriptions                                   │
    │ • Add/Delete Doctors                                   │
    │ • Search Queries                                       │
    │ • VIEW AUDIT LOG ◄─── admin-audit.php                 │
    │ • Logout                                               │
    └────────────────────────────────────────────────────────┘
            │
        AUDIT LOG VIEWER
            │
    ┌───────▼─────────────────────────────────────┐
    │ admin-audit.php                             │
    │ ─────────────────────────────────────────── │
    │ Shows all changes from audit_log table:     │
    │ • Who changed it (Dr. Ganesh)               │
    │ • What changed (Prescription #14)           │
    │ • When (2025-01-15 10:30:45)                │
    │ • Type of change (INSERT/UPDATE/DELETE)    │
    │ • Old values vs new values                 │
    │                                             │
    │ Filter by: Action, Table, Date Range       │
    └─────────────────────────────────────────────┘


ALL PATHS LEAD TO: logout.php ◄─────── Logout & destroy session
```

---

## 🗄️ Database Table Overview

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `patreg` | Patient registration | pid, fname, lname, email, password, contact, gender |
| `doctb` | Doctor information | docid, username, password, email, spec, docFees |
| `appointmenttb` | Appointments | ID, pid, fname, doctor, appdate, apptime, userStatus, doctorStatus, payment |
| `prestb` | Prescriptions | ID, doctor, pid, fname, disease, allergy, prescription, **appdate, apptime** |
| `audit_log` | **Audit trail (NEW)** | **audit_id, table_name, action, record_id, changed_by, changed_at, old_data, new_data** |
| `admin` | Admin/Receptionist accounts | adminid, username, password, email |
| `feedback` | User feedback/messages | feedid, name, email, message |

---

## 🔐 Security Notes

### Current Issues:
- ❌ **SQL Injection Risk:** Direct SQL queries without prepared statements
- ❌ **Plaintext Passwords:** Stored unencrypted in database
- ❌ **No Input Validation:** User input not sanitized
- ❌ **Session Hijacking:** No HTTPS or CSRF tokens

### Recommendations:
1. Use prepared statements (`mysqli_prepare()` or PDO)
2. Hash passwords (bcrypt or password_hash())
3. Add input validation/sanitization
4. Implement HTTPS for production
5. Add CSRF tokens
6. Use parameterized queries

---

## 🚀 Quick Start

### To Run the Project:

1. **Start XAMPP:**
   ```powershell
   C:\xampp\xampp-control.exe
   ```

2. **Start Apache & MySQL** (click Start buttons in XAMPP Control Panel)

3. **Import Database:**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create database `myhmsdb`
   - Import `myhmsdb.sql`
   - Execute `db/audit_triggers.sql` to activate audit logging

4. **Access Application:**
   ```
   http://localhost/Telemedicine/index.php
   ```

5. **Login As:**
   - **Patient:** Use email from patreg table
   - **Doctor:** Use email from doctb table
   - **Admin:** Use credentials from admin table

### To View Audit Log:
1. Login as Receptionist/Admin
2. Go to admin-panel1.php
3. Click "Audit Log" in sidebar menu
4. View all prescription changes (INSERT/UPDATE/DELETE)

---

## 📊 File Statistics

| Category | Count |
|----------|-------|
| PHP Files | ~20 |
| Documentation Files | 8 |
| CSS Files | 15+ |
| JavaScript Files | 10+ |
| HTML Files | 3 |
| SQL Files | 2 |
| Image Directories | 7 |
| Vendor Packages | 20+ |

---

## ✅ Summary

This is a **comprehensive Hospital Management System** with:
- ✅ Patient self-service (appointment booking)
- ✅ Doctor workflow (prescriptions, appointments)
- ✅ Admin management (staff, appointments, audit trail)
- ✅ Audit logging for compliance & security
- ✅ Responsive Bootstrap UI
- ✅ Database-driven with triggers

**Next Steps:** Fix security vulnerabilities and add HTTPS support for production deployment.
