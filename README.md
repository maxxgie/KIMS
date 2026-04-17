# KIMS — King'ong'o Inmate Management System

KIMS is a specialized web-based management platform designed for correctional facilities to streamline inmate record-keeping, judicial tracking, vocational training logs, and facility logistics.

## Features

### 1. Inmate Lifecycle Management
- Registration: Enrollment of new inmates with auto-generation of unique KIMS IDs (e.g., KIMS-2024-1234).
- Mugshot Handling: Secure upload and storage of inmate profile photos.
- Discharge Audit: Automated identification of inmates due for release based on their Expected Date of Discharge (EDD).

### 2. Judicial & Sentence Tracking
- Court Logistics: Schedule hearings, track court locations, and store virtual meeting (Zoom) links.
- Sentence Management: Flexible sentence entry in Days, Months, or Years with automatic EDD recalculation.
- Legal Amendments: Audit-trailed updates for legal charges and manual sentence adjustments.
- Remission Logic: One-click application of standard 1/3 sentence remission.

### 3. Vocational Training (Kazi na Masomo)
- Activity Ledger: Log training hours across various units (Bakery, Carpentry, Tailoring, etc.).
- Eligibility Tracking: Automatically flags inmates for graduation once they surpass 100 hours of training.

### 4. Facility Housing
- Block Management: Real-time occupancy tracking across different cell blocks.
- Internal Transfers: Validated transfer system that prevents over-capacity assignments.

### 5. System Intelligence & Security
- Critical Alerts: Automated notifications for upcoming court dates (48h), scheduled releases (30 days), and Parole Eligibility (inmates who served >50% of their sentence).
- Role-Based Access Control (RBAC): Tiered access for Super Admins, Wardens, and Instructors. Account provisioning is restricted to Super Admins.
- Audit Logs: Dedicated reporting on charge revisions and sentence adjustments.

## Technology Stack

*   Backend: PHP 7.4+
*   Database: MySQL / MariaDB (Optimized for XAMPP on port 3307)
*   Frontend: HTML5, CSS3 (Modular stylesheets), JavaScript (Vanilla)
*   Security: Password hashing (BCRYPT), Prepared Statements, Session-based Authentication.

## Installation

1.  Clone the Repository:
    Place the project folder in `C:\xampp\htdocs\KIMS\`.

2.  Database Setup:
    *   Start MySQL via XAMPP (ensure port 3307 is active or update `db_connect.php`).
    *   Create a database named `kims`.
    - Import the provided SQL schema (ensure inmates, users, court_records, training_logs, cell_blocks, sentence_updates, and offence_updates tables are created).

3.  Configuration:
    Verify connection strings in `db_connect.php`:
    ```php
    $servername = "127.0.0.1";
    $username = "root";
    $password = ""; 
    $dbname = "kims";
    $port = 3307;
    ```

4.  Directory Permissions:
    Ensure the `uploads/` directory exists and is writable by the web server for mugshot storage.

## Security Configuration

- Default credentials should be updated immediately upon first login.
- The system uses password_verify() for secure authentication.
- Session timeouts are handled via standard PHP session management.

## Reporting

Navigate to the Reports section to generate:
- Legal Charge Revision Audits
- Sentence Adjustment Audits
- Crime Distribution Trends
- Vocational Enrollment Summaries

---
This system is intended for authorized use within correctional facilities only.

## Default Login Credentials
Login as super Admin

- Username: Admin
- Password: Admin
