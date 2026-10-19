<?php
// Ensure only authenticated landlords can access this report
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: index.php");
    exit;
}

// Fetch all applications for properties owned by this landlord, joined with student and listing info
$reportStmt = $pdo->prepare("
    SELECT a.*, s.firstName, s.lastName, s.studentNumber, l.title 
    FROM Application a 
    JOIN Student s ON a.studentID = s.studentID 
    JOIN Listing l ON a.listingID = l.listingID 
    WHERE l.landlordID = ? 
    ORDER BY a.applicationDate DESC
");
$reportStmt->execute([$_SESSION['user_id']]);
$applicationsReport = $reportStmt->fetchAll();

// Calculate summary counts for the report dashboard
$totalApps = count($applicationsReport);
$pendingCount = count(array_filter($applicationsReport, function ($app) {
    return $app['applicationStatus'] === 'Pending';
}));
$approvedCount = count(array_filter($applicationsReport, function ($app) {
    return $app['applicationStatus'] === 'Approved';
}));
$rejectedCount = count(array_filter($applicationsReport, function ($app) {
    return $app['applicationStatus'] === 'Rejected';
}));
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Report Header -->
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Landlord Portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Application Status Report</h2>
            <p class="text-sm text-slate-600 mt-1">Generated: <?php echo date('F d, Y'); ?> by <?php echo htmlspecialchars($_SESSION['name']); ?></p>
        </div>
        <div class="flex gap-2">
            <a href="index.php" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Back to Dashboard</a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">Print Report</button>
        </div>
    </div>

    <!-- Summary Count Cards -->
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-amber-700 font-semibold">Pending Applications</p>
            <p class="text-3xl font-bold text-amber-900 mt-2"><?php echo $pendingCount; ?></p>
        </div>
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-emerald-700 font-semibold">Approved Applications</p>
            <p class="text-3xl font-bold text-emerald-900 mt-2"><?php echo $approvedCount; ?></p>
        </div>
        <div class="rounded-3xl border border-red-200 bg-red-50 p-5 shadow-sm">
            <p class="text-xs uppercase tracking-wider text-red-700 font-semibold">Rejected Applications</p>
            <p class="text-3xl font-bold text-red-900 mt-2"><?php echo $rejectedCount; ?></p>
        </div>
    </div>

    <!-- Application Details Table -->
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Application Records</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">App ID</th>
                        <th class="px-4 py-3">Property</th>
                        <th class="px-4 py-3">Student Name</th>
                        <th class="px-4 py-3">Student Number</th>
                        <th class="px-4 py-3">Date Applied</th>
                        <th class="px-4 py-3">Move-In Date</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if ($totalApps === 0): ?>
                        <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">No applications received for your portfolio properties.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($applicationsReport as $app): ?>
                        <?php
                            $badgeClass = 'bg-slate-100 text-slate-700';
                            if ($app['applicationStatus'] === 'Approved') $badgeClass = 'bg-emerald-100 text-emerald-800';
                            elseif ($app['applicationStatus'] === 'Pending') $badgeClass = 'bg-amber-100 text-amber-800';
                            elseif ($app['applicationStatus'] === 'Rejected') $badgeClass = 'bg-red-100 text-red-800';
                        ?>
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-medium text-slate-900">APP-<?php echo str_pad($app['applicationID'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-4 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($app['title']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($app['firstName'] . ' ' . $app['lastName']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($app['studentNumber'] ?? 'N/A'); ?></td>
                            <td class="px-4 py-4 text-slate-500"><?php echo date('Y-m-d', strtotime($app['applicationDate'])); ?></td>
                            <td class="px-4 py-4 text-slate-500"><?php echo htmlspecialchars($app['preferredMoveInDate']); ?></td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($app['applicationStatus']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>