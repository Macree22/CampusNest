<?php
require_once 'init.php';

$tables = ['Admin', 'Landlord', 'Student', 'Listing', 'ListingImage', 'Application', 'Booking', 'Payment', 'MaintenanceRequest', 'Notification'];
$files = [
    'index.php', 'db.php', 'init.php', 'header.php', 'footer.php', 
    'home.php', 'login.php', 'register.php', 'student.php', 'student_applications.php', 
    'student_payments.php', 'student_leases.php', 'student_maintenance.php', 'landlord.php', 
    'landlord_properties.php', 'landlord_tenants.php', 'landlord_analytics.php', 'landlord_reports.php', 
    'landlord_add_listing.php', 'admin.php', 'admin_users.php', 'admin_reports.php', 
    'admin_logs.php', 'notifications.php', 'help.php', '404.php', 'receipt.php', 
    'listing_detail.php', 'setup_db.php', 'health_check.php'
];
?>

<div class="max-w-4xl mx-auto space-y-6 py-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">System Diagnostics</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">CampusNest Environment Audit</h2>
            <p class="text-sm text-slate-600 mt-1">Verifying database connectivity, record counts, and file completeness.</p>
        </div>
        <a href="index.php" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">Return Home</a>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-lg">Database Table Records</h3>
            <ul class="space-y-2 text-sm">
                <?php foreach ($tables as $tbl): 
                    try {
                        $count = $pdo->query("SELECT COUNT(*) FROM $tbl")->fetchColumn();
                        echo "<li class='flex justify-between p-3 bg-slate-50 rounded-xl'><span>$tbl</span><b class='text-blue-600'>$count rows</b></li>";
                    } catch (Exception $e) {
                        echo "<li class='flex justify-between p-3 bg-red-50 text-red-700 rounded-xl'><span>$tbl</span><b>Missing / Error</b></li>";
                    }
                endforeach; ?>
            </ul>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-900 text-lg">Source File Dependency Status</h3>
            <ul class="space-y-2 text-sm max-h-[380px] overflow-y-auto pr-1">
                <?php foreach ($files as $file): 
                    $exists = file_exists(__DIR__ . '/' . $file);
                    echo "<li class='flex justify-between p-3 bg-slate-50 rounded-xl'><span>$file</span><b class='" . ($exists ? 'text-emerald-600' : 'text-red-600') . "'>" . ($exists ? 'Present' : 'Missing') . "</b></li>";
                endforeach; ?>
            </ul>
        </div>
    </div>
</div>