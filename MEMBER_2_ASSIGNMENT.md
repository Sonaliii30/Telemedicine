# Member 2: Authentication & User Management 🔐

## Quick Reference Card

```
Role:         Security & User Management Developer
Focus:        Login System, User Roles, Search Features
Priority:     CRITICAL - Security depends on your implementation
```

---

## 📋 Your Complete File List

### Authentication Files (CRITICAL)
```
include/checklogin.php     - Login verification logic
logout.php                 - Standard logout handler
logout1.php                - Alternative logout handler
```

### User Management - Doctor
```
doctor-panel.php           - Doctor dashboard and interface
doctorsearch.php           - Doctor search functionality
```

### User Management - Patient
```
patientsearch.php          - Patient search functionality
```

### Layout & Navigation
```
header.php                 - Header component
include/sidebar.php        - Sidebar navigation
```

---

## 🎯 Your Daily Tasks

### Day 1-2: Security Assessment & Setup
- [ ] Clone repository and set up local environment
- [ ] Read `TEAM_DISTRIBUTION.md`
- [ ] Review current authentication logic in `checklogin.php`
- [ ] Assess security vulnerabilities:
  - Password hashing (should use bcrypt/password_hash)
  - Session handling
  - CSRF protection
  - SQL injection prevention
- [ ] Create security documentation

### Day 3-5: Authentication Implementation
- [ ] Implement secure login system
- [ ] Create logout functionality
- [ ] Implement session management
- [ ] Implement role-based access control (RBAC)
- [ ] Add unit tests for authentication

### Ongoing: User Management Features
- [ ] Doctor search and filtering
- [ ] Patient search and filtering
- [ ] User profile management
- [ ] Permission verification

---

## 🔐 Authentication System Design

### Login Flow
```
1. User submits credentials → doctor-panel.php / index.php
2. checklogin.php validates credentials
3. Check against database user table
4. Verify password hash
5. Create session on success
6. Redirect to dashboard
7. On failure, show error message
```

### Logout Flow
```
1. User clicks logout
2. logout.php or logout1.php triggered
3. Destroy session
4. Clear session variables
5. Redirect to login page
```

### Session Management
```php
// Use secure session settings
session_start();
session_regenerate_id(true); // After login
// Set session timeout (15 minutes)
if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity'] > 900)) {
    session_destroy();
}
$_SESSION['last_activity'] = time();
```

---

## 🔑 Key Responsibilities

### 1. Login & Authentication
- Implement secure login validation
- Use password hashing (PHP's password_hash)
- Implement login attempts limit
- Log failed login attempts
- Create "Remember Me" functionality (optional)

### 2. Session Management
- Create secure session handling
- Implement session timeout (15 mins)
- Prevent session fixation attacks
- Regenerate session ID after login
- Clear sessions on logout

### 3. Role-Based Access Control (RBAC)
Define roles:
```
- admin      → Full system access
- doctor     → Doctor functions only
- patient    → Patient functions only
- staff      → Limited admin functions
- guest      → Read-only access (if applicable)
```

### 4. Doctor Management
- Search doctors by specialty, name, availability
- Display doctor profiles
- Show doctor schedules
- Display ratings/reviews

### 5. Patient Management
- Search patients by ID, name, contact
- Display patient profiles
- Show medical history
- Display appointments

### 6. Navigation & UI
- Create responsive header
- Implement sidebar navigation
- Show role-specific menu items
- Display user info (profile, logout)

---

## 🗂️ File Responsibilities

### `include/checklogin.php`
**Main Functions:**
- `validateLogin($email, $password)` - Validate credentials
- `getUserRole($userId)` - Get user role
- `checkPermission($role, $action)` - Check RBAC
- `hashPassword($password)` - Hash password
- `verifyPassword($password, $hash)` - Verify password

### `doctor-panel.php`
**Features:**
- Display doctor dashboard
- Show appointments
- Show prescriptions
- Show patient list
- Profile management

### `doctorsearch.php`
**Features:**
- Search by name
- Filter by specialty
- Filter by availability
- Filter by rating
- Display search results

### `patientsearch.php`
**Features:**
- Search by patient ID
- Search by name
- Filter by status
- Display patient details
- Show medical history

### `header.php`
**Components:**
- Logo and branding
- Navigation menu (responsive)
- User profile dropdown
- Logout button
- Search bar (optional)

### `include/sidebar.php`
**Components:**
- Menu items based on role
- Icons for menu items
- Active page highlighting
- Responsive collapse (mobile)

---

## 🔒 Security Checklist

### Password Security
- [ ] Use PHP's `password_hash()` for hashing
- [ ] Use `password_verify()` to check passwords
- [ ] Enforce strong password policies
- [ ] Implement password reset functionality
- [ ] Don't store plain text passwords

### Session Security
- [ ] Use HTTPS for all connections
- [ ] Set secure session cookies
- [ ] Implement session timeout
- [ ] Regenerate session ID after login
- [ ] Validate session on every request

### Input Validation
- [ ] Validate email format
- [ ] Sanitize all user inputs
- [ ] Use prepared statements (from Member 1)
- [ ] Implement CSRF tokens
- [ ] Validate on both client and server

### Access Control
- [ ] Check user role before granting access
- [ ] Log all unauthorized access attempts
- [ ] Implement rate limiting for login
- [ ] Don't show sensitive info to unauthorized users

---

## 👥 Collaboration Points

### With Member 1 (Backend)
- Get authentication functions
- Use user data retrieval functions
- Access role definitions

### With Member 3 (Frontend)
- Coordinate header styling
- Coordinate navigation appearance
- Share search UI components

### With Member 5 (Frontend Core)
- Coordinate with main layout
- Share error handling
- Use utility functions

---

## 💻 Code Examples

### Secure Login Implementation
```php
// checklogin.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Get user from database (using Member 1's function)
    $user = getUser($email); // From func.php
    
    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['last_activity'] = time();
        
        // Log successful login
        logActivity("Login", $user['id']);
        
        // Redirect based on role
        redirectBasedOnRole($user['role']);
    } else {
        // Log failed attempt
        logActivity("Failed Login", null, $email);
        $_SESSION['error'] = "Invalid credentials";
        header("Location: index.php?error=1");
    }
}
```

### Role-Based Access Control
```php
// Check if user has permission
function checkPermission($requiredRole) {
    session_start();
    
    if (!isset($_SESSION['user_role'])) {
        header("Location: index.php");
        exit;
    }
    
    $userRole = $_SESSION['user_role'];
    $roles = [
        'admin' => 3,
        'doctor' => 2,
        'patient' => 1,
    ];
    
    if ($roles[$userRole] < $roles[$requiredRole]) {
        die("Access Denied");
    }
}
```

---

## 🐛 Common Issues to Watch

❌ **Don't:**
- Store passwords in plain text
- Use MD5 for hashing
- Trust user input
- Hardcode role permissions
- Create sessions without timeout

✅ **Do:**
- Use password_hash and password_verify
- Sanitize all inputs
- Validate on server side
- Store roles in database
- Implement session timeout

---

## 📚 Resources

### Security Best Practices
- OWASP Authentication Cheat Sheet
- PHP Session Security: https://www.php.net/manual/en/session.security.php
- Password Hashing: https://www.php.net/manual/en/function.password-hash.php

### Database Schema (Ask Member 1)
- Users table structure
- Roles table structure
- Permissions table structure

---

## 🚀 Quick Start Commands

```bash
# Clone repository
git clone https://github.com/Sonaliii30/Telemedicine.git
cd Telemedicine

# Create your branch
git checkout develop
git checkout -b feature/member-2-authentication

# Test authentication
# Update include/config.php with your local database
php -S localhost:8000

# Commit your work
git add .
git commit -m "Implement authentication and user management"
git push origin feature/member-2-authentication
```

---

## ✅ Completion Checklist

- [ ] Environment setup complete
- [ ] Authentication system implemented
- [ ] Session management working
- [ ] RBAC implemented
- [ ] Doctor search working
- [ ] Patient search working
- [ ] Header and sidebar responsive
- [ ] Security audit completed
- [ ] Unit tests written
- [ ] Documentation complete

**You're the security guardian of the team! 🛡️**
