# Lab / Pathology Module - Overview

This document describes the newly added Lab (Pathology) module: schema, pages, workflows, and how to test.

## Files Added

- `db/lab_schema.sql` - Creates tables: `lab_tests`, `lab_orders`, `lab_order_items`, `lab_results_files`, `lab_technicians` and inserts sample tests.
- `db/lab_audit_triggers.sql` - Triggers to record changes on `lab_orders` and `lab_order_items` into `audit_log`.
- `doctor-order-lab.php` - Doctor-facing page to order tests for a patient (select tests, add instructions).
- `patient-lab-results.php` - Patient-facing page to view/download their lab results.
- `lab-panel.php` - Lab technician panel to view orders, enter results, upload files, and complete orders.
- `admin-lab.php` - Admin summary page with counts and link to lab panel.

## Workflow Summary

1. Doctor clicks "Order Lab" from appointment row (or uses `doctor-order-lab.php`) to select tests.
2. `lab_orders` and `lab_order_items` records are created. `@current_user` is set so `audit_log` triggers capture WHO made the change.
3. Technician opens `lab-panel.php`, fills results for each test, optionally uploads a PDF/image, and marks order complete.
4. Lab updates (item results and order status) are recorded and triggers log these actions to `audit_log`.
5. Patient can view completed results at `patient-lab-results.php` and download attached files.

## Quick Test Plan

1. Import `db/lab_schema.sql` into `myhmsdb` (phpMyAdmin).
2. Import `db/lab_audit_triggers.sql` (this expects `audit_log` table already exists).
3. Login as doctor, open an appointment, click "Order Lab", choose tests and create order.
4. Login (or open) `lab-panel.php`, open the latest order, enter results and upload a PDF.
5. Login as patient, open `patient-lab-results.php` and verify results and file download link.
6. Check `admin-audit.php` to see audit entries for the INSERT (order created) and UPDATE (results saved).

## Security Notes

- Technician accounts are minimal; create entries in `lab_technicians` and map logins or extend auth.
- Uploaded files are stored in `/uploads/lab_results/` - ensure this folder is protected in production and filenames are sanitized.
- All new DB writes set `@current_user` before execution so triggers record proper user.
- Use HTTPS in production and restrict access to lab pages by role.

## Next Improvements

- Add role-based authentication for technicians (login page, session handling).
- Add pagination/search on `lab-panel.php` and `admin-lab.php`.
- Add unit tests / SQL sanity checks and input validation.
- Move uploads outside webroot or serve via a protected download script.
