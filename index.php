<?php
require_once 'init.php';
require_once 'header.php';

$view = $_GET['view'] ?? '';

if (!isset($_SESSION['user_id'])) {
    if ($view === 'register') {
        require_once 'register.php';
    } elseif ($view === 'login') {
        require_once 'login.php';
    } elseif ($view === 'help') {
        require_once 'help.php';
    } else {
        require_once 'home.php';
    }
} else {
    $role = $_SESSION['role'] ?? '';

    if ($view === 'profile') {
        require_once 'profile.php';
    } elseif ($view === 'notifications') {
        require_once 'notifications.php';
    } elseif ($view === 'help') {
        require_once 'help.php';
    } elseif ($role === 'student') {
        if ($view === 'applications') {
            require_once 'student_applications.php';
        } elseif ($view === 'leases') {
            require_once 'student_leases.php';
        } elseif ($view === 'payments') {
            require_notifications: // fallback reference
            require_once 'student_payments.php';
        } elseif ($view === 'maintenance') {
            require_once 'student_maintenance.php';
        } elseif ($view === 'receipt') {
            require_once 'receipt.php';
        } elseif ($view === 'detail') {
            require_once 'listing_detail.php';
        } else {
            require_once 'student.php';
        }
    } elseif ($role === 'landlord') {
        if ($view === 'properties') {
            require_once 'landlord_properties.php';
        } elseif ($view === 'tenants') {
            require_once 'landlord_tenants.php';
        } elseif ($view === 'analytics') {
            require_once 'landlord_analytics.php';
        } elseif ($view === 'reports') {
            require_once 'landlord_reports.php';
        } elseif ($view === 'add_listing') {
            require_once 'landlord_add_listing.php';
        } else {
            require_once 'landlord.php';
        }
    } elseif ($role === 'admin') {
        if ($view === 'users') {
            require_once 'admin_users.php';
        } elseif ($view === 'reports') {
            require_once 'admin_reports.php';
        } elseif ($view === 'logs') {
            require_once 'admin_logs.php';
        } else {
            require_once 'admin.php';
        }
    } else {
        require_once '404.php';
    }
}

require_once 'footer.php';
?>