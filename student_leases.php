<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$leaseStmt = $pdo->prepare("
    SELECT b.*, l.title, l.address, l.monthlyRent, land.firstName as landlordFirst, land.lastName as landlordLast, land.email as landlordEmail
    FROM Booking b
    JOIN Listing l ON b.listingID = l.listingID
    JOIN Landlord land ON l.landlordID = land.landlordID
    WHERE b.studentID = ?
    ORDER BY b.bookingDate DESC
");
$leaseStmt->execute([$_SESSION['user_id']]);
$myLeases = $leaseStmt->fetchAll();
?>

<div class="space-y-6 max-w-4xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Student Portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">My Lease Agreements</h2>
            <p class="mt-1 text-sm text-slate-600">Active tenancy contracts and lease durations.</p>
        </div>
        <div class="flex gap-2">
            <a href="?view=search" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Search</a>
            <a href="?view=payments" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Payments</a>
        </div>
    </div>

    <div class="space-y-4">
        <?php if (count($myLeases) === 0): ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center text-slate-500">No active lease agreements found.</div>
        <?php endif; ?>
        <?php foreach ($myLeases as $lease): ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($lease['title']); ?></h4>
                        <p class="text-xs text-slate-600"><?php echo htmlspecialchars($lease['address']); ?></p>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-semibold <?php echo $lease['bookingStatus'] === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800'; ?>">
                        <?php echo htmlspecialchars($lease['bookingStatus']); ?> Lease
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm bg-slate-50 p-4 rounded-2xl">
                    <div>
                        <span class="text-slate-500 text-xs block uppercase">Start Date</span>
                        <b class="text-slate-900"><?php echo htmlspecialchars($lease['bookingDate']); ?></b>
                    </div>
                    <div>
                        <span class="text-slate-500 text-xs block uppercase">Duration</span>
                        <b class="text-slate-900"><?php echo htmlspecialchars($lease['durationMonths']); ?> Months</b>
                    </div>
                    <div>
                        <span class="text-slate-500 text-xs block uppercase">Monthly Rent</span>
                        <b class="text-emerald-600"><?php echo randFormat($lease['monthlyRent']); ?></b>
                    </div>
                </div>

                <div class="text-xs text-slate-500 pt-2 border-t border-slate-100 flex justify-between items-center">
                    <span>Landlord Contact: <b><?php echo htmlspecialchars($lease['landlordFirst'] . ' ' . $lease['landlordLast']); ?></b> (<?php echo htmlspecialchars($lease['landlordEmail']); ?>)</span>
                    <a href="?view=maintenance" class="text-blue-600 font-semibold hover:underline">Log Maintenance &rarr;</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>