<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$maintStmt = $pdo->prepare("SELECT m.*, l.title FROM MaintenanceRequest m JOIN Listing l ON m.listingID = l.listingID WHERE m.studentID = ? ORDER BY m.requestDate DESC");
$maintStmt->execute([$_SESSION['user_id']]);
$myRequests = $maintStmt->fetchAll();

$leaseStmt = $pdo->prepare("SELECT b.listingID, l.title FROM Booking b JOIN Listing l ON b.listingID = l.listingID WHERE b.studentID = ? AND b.bookingStatus = 'Active'");
$leaseStmt->execute([$_SESSION['user_id']]);
$activeLease = $leaseStmt->fetch();

$timeline = ['Submitted', 'In Progress', 'Resolved', 'Closed'];
?>

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Student portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Maintenance requests</h2>
            <p class="mt-1 text-sm text-slate-600">Report property issues and track resolution progress.</p>
        </div>
        <a href="index.php" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Dashboard</a>
    </div>

    <?php if ($activeLease): ?>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Submit a new maintenance ticket</h3>
            <form method="post" class="mt-5 space-y-4">
                <input type="hidden" name="action" value="submit_maintenance">
                <input type="hidden" name="listingID" value="<?php echo $activeLease['listingID']; ?>">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Property unit</label>
                    <input type="text" value="<?php echo htmlspecialchars($activeLease['title']); ?>" disabled class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-600">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Urgency level</label>
                    <select name="urgencyLevel" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:border-slate-300 focus:bg-white focus:outline-none">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Issue description</label>
                    <textarea name="description" rows="3" required placeholder="Describe the maintenance issue in detail..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:outline-none"></textarea>
                </div>
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Submit request</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-900">Ticket history</h3>
        <div class="mt-5 space-y-4">
            <?php if (count($myRequests) === 0): ?>
                <p class="py-4 text-center text-sm text-slate-500">No maintenance requests logged.</p>
            <?php endif; ?>
            <?php foreach ($myRequests as $req): ?>
                <?php
                $stageIndex = array_search($req['requestStatus'], $timeline, true);
                if ($stageIndex === false) {
                    $stageIndex = 0;
                }
                ?>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900"><?php echo htmlspecialchars($req['title']); ?> <span class="text-xs font-normal text-slate-500">(<?php echo htmlspecialchars($req['urgencyLevel']); ?>)</span></p>
                            <p class="mt-1 text-xs text-slate-600"><?php echo htmlspecialchars($req['description']); ?></p>
                            <p class="mt-2 text-[10px] text-slate-400">Logged: <?php echo $req['requestDate']; ?></p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                            <?php foreach ($timeline as $index => $step): ?>
                                <div class="flex-1 text-center">
                                    <div class="mb-1 flex items-center justify-center">
                                        <span class="inline-block h-2.5 w-2.5 rounded-full <?php echo $index <= $stageIndex ? 'bg-emerald-500' : 'bg-slate-300'; ?>"></span>
                                    </div>
                                    <span class="<?php echo $index <= $stageIndex ? 'text-emerald-700' : 'text-slate-400'; ?>"><?php echo $step; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if (!empty($req['landlordComment'])): ?>
                        <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-3 text-sm text-slate-700">
                            <p class="mb-1 font-semibold text-blue-800">Landlord comment</p>
                            <p><?php echo htmlspecialchars($req['landlordComment']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>