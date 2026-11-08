<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$appStmt = $pdo->prepare("SELECT a.*, l.title, l.roomType, l.listingID, l.monthlyRent FROM Application a JOIN Listing l ON a.listingID = l.listingID WHERE a.studentID = ?");
$appStmt->execute([$_SESSION['user_id']]);
$studentApplications = $appStmt->fetchAll();

$bookingCache = [];
if (!empty($studentApplications)) {
    $bookingIDs = array_map(function ($app) {
        return $app['applicationID'];
    }, $studentApplications);
    $bookingStmt = $pdo->prepare("SELECT * FROM Booking WHERE applicationID IN (" . str_repeat('?,', count($bookingIDs) - 1) . '?)');
    $bookingStmt->execute($bookingIDs);
    $bookingRows = $bookingStmt->fetchAll();
    foreach ($bookingRows as $booking) {
        $bookingCache[$booking['applicationID']] = $booking;
    }
}
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Student portal</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">My accommodation applications</h2>
            </div>
            <div class="flex gap-2">
                <a href="?view=search" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Browse Search</a>
                <a href="?view=payments" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Payments</a>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <?php if (count($studentApplications) === 0): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500 shadow-sm">You have not submitted any property applications yet.</div>
        <?php endif; ?>

        <?php foreach ($studentApplications as $application): ?>
            <?php $booking = $bookingCache[$application['applicationID']] ?? null; ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h4 class="truncate text-xl font-semibold text-slate-900"><?php echo htmlspecialchars($application['title']); ?></h4>
                        <p class="mt-1 text-sm text-slate-500">Applied: <?php echo $application['applicationDate']; ?> • Move-in: <?php echo $application['preferredMoveInDate']; ?></p>
                    </div>
                    <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $application['applicationStatus'] === 'Approved' ? 'bg-emerald-50 text-emerald-700' : ($application['applicationStatus'] === 'Pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700'); ?>">
                        <?php echo $application['applicationStatus']; ?>
                    </span>
                </div>

                <?php if ($application['applicationStatus'] === 'Approved'): ?>
                    <?php if ($booking && (int)$booking['studentAccepted'] === 0): ?>
                        <div class="mt-5 border-t border-slate-100 pt-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Lease ready to sign</p>
                                    <p class="text-xs text-slate-500">Total contract value: <?php echo randFormat((float)$booking['totalContractValue']); ?></p>
                                </div>

                                <form method="post" class="m-0">
                                    <input type="hidden" name="action" value="accept_lease">
                                    <input type="hidden" name="bookingID" value="<?php echo $booking['bookingID']; ?>">
                                    <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">Sign Lease Agreement</button>
                                </form>
                            </div>
                        </div>
                    <?php elseif ($booking && (int)$booking['studentAccepted'] === 1): ?>
                        <div class="mt-5 border-t border-slate-100 pt-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Lease accepted</p>
                                    <p class="text-xs text-slate-500">Status: <?php echo htmlspecialchars($booking['bookingStatus']); ?></p>
                                </div>
                                <a href="?view=lease_receipt&bookingID=<?php echo $booking['bookingID']; ?>" class="rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700">Download Lease & Receipt</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mt-5 border-t border-slate-100 pt-5 text-sm text-slate-500">Lease generation is in progress. Please wait for approval processing.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($application['applicationStatus'] === 'Approved'): ?>
                    <form method="post" class="mt-5 border-t border-slate-100 pt-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <input type="hidden" name="action" value="pay">
                            <input type="hidden" name="applicationID" value="<?php echo $application['applicationID']; ?>">
                            <input type="hidden" name="listingID" value="<?php echo $application['listingID']; ?>">
                            <input type="hidden" name="amount" value="<?php echo $application['monthlyRent']; ?>">
                            <select name="paymentMethod" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-slate-300 focus:bg-white focus:outline-none sm:max-w-[180px]">
                                <option value="Credit Card">Credit Card</option>
                                <option value="EFT">EFT</option>
                            </select>
                            <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">Pay Rent <?php echo randFormat($application['monthlyRent']); ?></button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>