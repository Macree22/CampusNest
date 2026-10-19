# CampusNest

## Student Accommodation Management System

CampusNest is a multi-role PHP and PDO web application for managing student housing, property portfolios, applications, lease agreements, digital payments, maintenance requests, and administrative governance.

## Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Directory Structure](#project-directory-structure)
- [How to Run](#how-to-run)
- [Application Routes](#application-routes)
- [User Workflows](#user-workflows)
- [Database Overview](#database-overview)
- [Security Notes](#security-notes)
- [Troubleshooting](#troubleshooting)
- [Pre-Seeded Test Accounts](#pre-seeded-test-accounts)

## Features

### Students

- Browse verified vacant accommodation listings.
- Filter properties by location, room type, and price range.
- View detailed property information and image galleries.
- Submit and track accommodation applications.
- Pay rent for approved applications and view digital receipts.
- View active leases and submit maintenance requests.
- Read account notifications and update profile details.

### Landlords

- Publish and manage property listings.
- Update rent and property availability.
- Review, approve, or reject student applications.
- View tenants, maintenance requests, payment records, and financial analytics.
- Generate application status reports.

### Administrators

- Review platform occupancy and revenue metrics.
- Verify newly submitted property listings.
- Manage user directories.
- Review platform payment reports and system logs.

## Technology Stack

- **Backend:** PHP with PDO
- **Database:** MySQL or MariaDB
- **Frontend:** Server-rendered PHP views with Tailwind CSS utility classes
- **Web server:** Apache through WAMP, XAMPP, or another PHP-compatible stack
- **Authentication:** PHP sessions with role-based routing

## Project Directory Structure

The project uses a flat root web directory with modular views, core controllers, and asset files:

```text
campusnest/
|
|- index.php                 # Central front-controller router
|- init.php                  # Session bootstrap, core functions, and action handler
|- db.php                    # PDO database connection
|- setup_db.php              # Automated database schema installer and seeder
|- health_check.php          # Environment and table diagnostics utility
|
|- header.php                # Global navigation header component
|- footer.php                # Global page footer component
|- home.php                  # Public landing page with featured listings
|- login.php                 # User authentication login view
|- register.php              # Multi-role account registration view
|- profile.php               # User account settings and profile manager
|- help.php                  # Support center and FAQ guide
|- notifications.php         # User alert notification center
|- 404.php                   # Error handler for invalid routes
|
|- student.php               # Student search and property filter portal
|- student_applications.php  # Student application tracker
|- student_leases.php        # Active student lease contracts viewer
|- student_payments.php      # Payment history and receipt log
|- student_maintenance.php   # Student maintenance ticket portal
|- listing_detail.php        # Individual property view with image gallery
|- receipt.php               # Printable official digital payment receipt
|
|- landlord.php              # Landlord dashboard for application reviews
|- landlord_properties.php   # Landlord unit portfolio management
|- landlord_tenants.php      # Active tenant roster manager
|- landlord_analytics.php    # Financial earnings analytics view
|- landlord_reports.php      # Printable application status report
|- landlord_add_listing.php  # Form to publish new property listings
|
|- admin.php                 # Administrator governance and metrics dashboard
|- admin_users.php           # User accounts directory management
|- admin_reports.php         # Platform financial audit report
|- admin_logs.php            # Global activity logs auditor
|
`- images/
	`- default.jpg             # Default fallback property image
```

## File-by-File Description

- **`index.php`**: Central front controller. It reads `?view=...`, checks authentication and user roles, and routes requests to the appropriate view.
- **`init.php`**: Starts sessions, includes the database connection, defines `randFormat`, and processes global POST actions including authentication, profiles, applications, payments, maintenance requests, listing updates, and verification.
- **`db.php`**: Configures the PDO connection to the MySQL `campusnest` database.
- **`setup_db.php`**: Creates the database schema, recreates the required tables, and seeds test data.
- **`health_check.php`**: Provides diagnostics for database tables and required files.
- **`header.php`**: Renders the global navigation header for guests, students, landlords, and administrators.
- **`footer.php`**: Renders the shared footer and branding markup.
- **`home.php`**: Public home view with featured verified listings.
- **`login.php`**: Login form for existing users.
- **`register.php`**: Registration form for student and landlord accounts.
- **`profile.php`**: Authenticated profile and password management.
- **`help.php`**: Frequently asked questions and support guidance.
- **`notifications.php`**: Displays notifications for the active user.
- **`404.php`**: Handles broken or unauthorized routes.
- **`student.php`**: Student accommodation search with location, room type, and price filters.
- **`student_applications.php`**: Student application tracker with approved-unit payment access.
- **`student_leases.php`**: Active lease terms and landlord contact information.
- **`student_payments.php`**: Payment history and digital receipt links.
- **`student_maintenance.php`**: Maintenance ticket submission and progress tracking.
- **`listing_detail.php`**: Detailed property information, image gallery, and landlord contact details.
- **`receipt.php`**: Printable digital payment receipt.
- **`landlord.php`**: Landlord dashboard for applications, maintenance requests, and payment tracking.
- **`landlord_properties.php`**: Landlord property and availability management.
- **`landlord_tenants.php`**: Active tenant roster for landlord properties.
- **`landlord_analytics.php`**: Landlord rental income and payment method analytics.
- **`landlord_reports.php`**: Printable application status report.
- **`landlord_add_listing.php`**: New property listing form.
- **`admin.php`**: Platform metrics and pending listing verification dashboard.
- **`admin_users.php`**: Directory of registered students and landlords.
- **`admin_reports.php`**: Platform-wide financial audit report.
- **`admin_logs.php`**: System notification and activity log auditor.

## How to Run

### Prerequisites

- PHP 7.4 or newer with the PDO MySQL extension enabled.
- Apache with PHP support.
- MySQL or MariaDB.
- A browser such as Chrome, Edge, or Firefox.

### Installation

1. Place the `CampusNest` folder in your local web server root, such as `www/campusnest/` for WAMP or `htdocs/campusnest/` for XAMPP.
2. Start Apache and MySQL from the WAMP or XAMPP control panel.
3. Confirm the database credentials in [db.php](db.php) and [setup_db.php](setup_db.php).
4. Run the installer at [http://localhost/campusnest/setup_db.php](http://localhost/campusnest/setup_db.php).
5. Launch the application at [http://localhost/campusnest/index.php](http://localhost/campusnest/index.php).

> **Warning:** `setup_db.php` drops and recreates the application tables before seeding test data. Do not run it against a database containing data you need to keep.

## Application Routes

The application uses `index.php` as its front controller. Views are selected with the `view` query parameter and access is restricted by session role.

| View | Access | Module |
| --- | --- | --- |
| `/` or `?view=home` | Guest | `home.php` |
| `?view=login` | Guest | `login.php` |
| `?view=register` | Guest | `register.php` |
| `?view=help` | Public | `help.php` |
| `?view=search` | Student | `student.php` |
| `?view=applications` | Student | `student_applications.php` |
| `?view=payments` | Student | `student_payments.php` |
| `?view=maintenance` | Student | `student_maintenance.php` |
| `?view=leases` | Student | `student_leases.php` |
| `?view=detail&id=LISTING_ID` | Student | `listing_detail.php` |
| `?view=reports` | Landlord | `landlord_reports.php` |
| `?view=properties` | Landlord | `landlord_properties.php` |
| `?view=tenants` | Landlord | `landlord_tenants.php` |
| `?view=analytics` | Landlord | `landlord_analytics.php` |
| `?view=add_listing` | Landlord | `landlord_add_listing.php` |
| `?view=users` | Administrator | `admin_users.php` |
| `?view=reports` | Administrator | `admin_reports.php` |
| `?view=logs` | Administrator | `admin_logs.php` |
| `?view=profile` | Authenticated users | `profile.php` |
| `?view=notifications` | Authenticated users | `notifications.php` |

## User Workflows

### Student application and payment

1. Log in as a student and open the accommodation search.
2. Select a verified vacant listing and submit an application.
3. Wait for the landlord to approve the application.
4. Open the approved application and submit a payment.
5. View the receipt from the payment history page.

### Landlord property management

1. Log in as a landlord and publish a listing.
2. Wait for administrator verification.
3. Review incoming applications from the landlord dashboard.
4. Approve or reject applications and monitor tenant activity.
5. Update property availability, review payments, and handle maintenance requests.

### Administrator governance

1. Log in with an administrator account.
2. Review pending listing submissions.
3. Verify approved properties so they become visible in student search.
4. Use reports, user management, and logs to monitor the platform.

## Database Overview

The installer creates the following tables:

| Table | Purpose |
| --- | --- |
| `Admin` | Administrator accounts |
| `Landlord` | Property owner accounts |
| `Student` | Student accounts |
| `Listing` | Properties, rent, availability, and verification status |
| `ListingImage` | Additional listing gallery images |
| `Application` | Student applications and landlord decisions |
| `Booking` | Active and completed lease bookings |
| `Payment` | Rent payments and transaction references |
| `MaintenanceRequest` | Student maintenance tickets and statuses |
| `Notification` | User-specific system notifications |

Listings reference landlords. Applications, payments, bookings, and maintenance requests reference both students and listings. Foreign-key cascade rules remove dependent records when a parent account or listing is deleted.

## Security Notes

- Use prepared statements for user-supplied database values.
- Escape output with `htmlspecialchars()` before rendering it in HTML.
- Keep database credentials outside publicly accessible production files.
- Restrict setup and diagnostic scripts or remove them after deployment.
- Use HTTPS and secure session-cookie settings in production.
- Add CSRF protection before exposing the application beyond a local development environment.
- Validate role ownership on every update, payment, and maintenance action.

## Troubleshooting

### Database connection fails

Make sure MySQL is running and that the credentials in [db.php](db.php) match the local MySQL configuration. The default local WAMP configuration commonly uses the `root` user with an empty password.

### Tables or sample records are missing

Run [setup_db.php](setup_db.php) once from the browser. Remember that the installer resets the database tables and sample data.

### Images do not display

Check that Apache has internet access for remote image URLs and that [images/default.jpg](images/default.jpg) exists for local fallback images.

### A route shows the wrong page

Confirm the `view` query parameter and the logged-in role. All authenticated routing is handled by [index.php](index.php).

## Pre-Seeded Test Accounts

### Administrator Account

- **Email:** `admin@campusnest.co.za`
- **Password:** `CampusNest2026!` when created by [setup_db.php](setup_db.php), or `password123` when created by [seeder.php](seeder.php)

### Landlord Accounts

| Name | Company | Email |
| --- | --- | --- |
| Thandi Ngubane | Mzanzi Stays | `thandi@mzanzi.co.za` |
| John Smith | Smith Properties | `john@smithprop.com` |
| Sipho Dlamini | Dlamini Properties | `sipho@dlaminiprop.co.za` |
| Lerato Molefe | City Living | `lerato@cityliving.co.za` |

**Password for all landlord accounts:** `CampusNest2026!` with [setup_db.php](setup_db.php), or `password123` with [seeder.php](seeder.php).

### Student Accounts

| Name | Current email | Legacy email |
| --- | --- | --- |
| Siyabonga Mkhize | `siyabonga.mkhize@mycampus.ac.za` | `siya@student.co.za` |
| Jessica Jones | `jessica.jones@mycampus.ac.za` | `jess@student.co.za` |

**Password for all student accounts:** `CampusNest2026!` with [setup_db.php](setup_db.php), or `password123` with [seeder.php](seeder.php).

> Run only one seeding workflow at a time. The account names, email addresses, and passwords depend on which script was executed most recently.
