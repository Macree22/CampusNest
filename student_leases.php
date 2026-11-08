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

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Student portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">My lease agreements</h2>
            <p class="mt-1 text-sm text-slate-600">Active tenancy contracts and lease durations.</p>
        </div>
        <div class="flex gap-2">
            <a href="?view=search" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Search</a>
            <a href="?view=payments" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Payments</a>
        </div>
    </div>

    <div class="space-y-4">
        <?php if (count($myLeases) === 0): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500 shadow-sm">No active lease agreements found.</div>
        <?php endif; ?>
        <?php foreach ($myLeases as $lease): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h4 class="text-xl font-semibold text-slate-900"><?php echo htmlspecialchars($lease['title']); ?></h4>
                        <p class="mt-1 text-sm text-slate-500"><?php echo htmlspecialchars($lease['address']); ?></p>
                    </div>
                    <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $lease['bookingStatus'] === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700'; ?>">
                        <?php echo htmlspecialchars($lease['bookingStatus']); ?> lease
                    </span>
                </div>

                <div class="mt-5 grid gap-4 rounded-2xl bg-slate-50 p-4 md:grid-cols-3">
                    <div>
                        <span class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Start date</span>
                        <p class="mt-2 text-sm font-medium text-slate-900"><?php echo htmlspecialchars($lease['bookingDate']); ?></p>
                    </div>
                    <div>
                        <span class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Duration</span>
                        <p class="mt-2 text-sm font-medium text-slate-900"><?php echo htmlspecialchars($lease['durationMonths']); ?> months</p>
                    </div>
                    <div>
                        <span class="block text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Monthly rent</span>
                        <p class="mt-2 text-sm font-semibold text-emerald-700"><?php echo randFormat($lease['monthlyRent']); ?></p>
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-2 border-t border-slate-100 pt-3 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                    <span>Landlord contact: <b class="text-slate-700"><?php echo htmlspecialchars($lease['landlordFirst'] . ' ' . $lease['landlordLast']); ?></b> (<?php echo htmlspecialchars($lease['landlordEmail']); ?>)</span>
                    <a href="?view=maintenance" class="font-semibold text-blue-600 hover:text-blue-700">Log maintenance &rarr;</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>