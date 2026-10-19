<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: index.php");
    exit;
}

$tenantStmt = $pdo->prepare("
    SELECT b.*, s.firstName, s.lastName, s.email, s.studentNumber, l.title, l.monthlyRent
    FROM Booking b
    JOIN Student s ON b.studentID = s.studentID
    JOIN Listing l ON b.listingID = l.listingID
    WHERE l.landlordID = ? AND b.bookingStatus = 'Active'
    ORDER BY b.bookingDate DESC
");
$tenantStmt->execute([$_SESSION['user_id']]);
$activeTenants = $tenantStmt->fetchAll();
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Landlord Portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Active Tenant Roster</h2>
            <p class="mt-1 text-sm text-slate-600">Monitor active lease tenancies and tenant contact details.</p>
        </div>
        <a href="index.php" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Back to Dashboard</a>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tenant Name</th>
                        <th class="px-4 py-3">Student #</th>
                        <th class="px-4 py-3">Property Unit</th>
                        <th class="px-4 py-3">Lease Start</th>
                        <th class="px-4 py-3">Duration</th>
                        <th class="px-4 py-3">Monthly Rent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (count($activeTenants) === 0): ?>
                        <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">No active tenants found in your portfolio.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($activeTenants as $tenant): ?>
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($tenant['firstName'] . ' ' . $tenant['lastName']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($tenant['studentNumber'] ?? 'N/A'); ?></td>
                            <td class="px-4 py-4 text-slate-700"><?php echo htmlspecialchars($tenant['title']); ?></td>
                            <td class="px-4 py-4 text-slate-500"><?php echo htmlspecialchars($tenant['bookingDate']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($tenant['durationMonths']); ?> Months</td>
                            <td class="px-4 py-4 font-bold text-emerald-600"><?php echo randFormat($tenant['monthlyRent']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>