<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$maintStmt = $pdo->prepare("
    SELECT m.*, l.title 
    FROM MaintenanceRequest m 
    JOIN Listing l ON m.listingID = l.listingID 
    WHERE m.studentID = ? 
    ORDER BY m.requestDate DESC
");
$maintStmt->execute([$_SESSION['user_id']]);
$myRequests = $maintStmt->fetchAll();

$leaseStmt = $pdo->prepare("
    SELECT b.listingID, l.title 
    FROM Booking b 
    JOIN Listing l ON b.listingID = l.listingID 
    WHERE b.studentID = ? AND b.bookingStatus = 'Active'
");
$leaseStmt->execute([$_SESSION['user_id']]);
$activeLease = $leaseStmt->fetch();
?>

<div class="space-y-6 max-w-4xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Student Portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Maintenance Requests</h2>
            <p class="mt-1 text-sm text-slate-600">Report property issues and track resolution progress.</p>
        </div>
        <a href="index.php" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Dashboard</a>
    </div>

    <?php if ($activeLease): ?>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-slate-900">Submit New Maintenance Ticket</h3>
            <form method="post" class="space-y-4">
                <input type="hidden" name="action" value="submit_maintenance">
                <input type="hidden" name="listingID" value="<?php echo $activeLease['listingID']; ?>">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Property Unit</label>
                    <input type="text" value="<?php echo htmlspecialchars($activeLease['title']); ?>" disabled class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-100 text-slate-600 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Urgency Level</label>
                    <select name="urgencyLevel" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm outline-none">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Issue Description</label>
                    <textarea name="description" rows="3" required placeholder="Describe the maintenance issue in detail..." class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm outline-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition">Submit Request</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
        <h3 class="text-lg font-bold text-slate-900">Ticket History</h3>
        <div class="space-y-3">
            <?php if (count($myRequests) === 0): ?>
                <p class="text-sm text-slate-500 text-center py-4">No maintenance requests logged.</p>
            <?php endif; ?>
            <?php foreach ($myRequests as $req): ?>
                <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center text-sm">
                    <div class="space-y-1">
                        <p class="font-semibold text-slate-900"><?php echo htmlspecialchars($req['title']); ?> <span class="text-xs font-normal text-slate-500">(<?php echo $req['urgencyLevel']; ?>)</span></p>
                        <p class="text-xs text-slate-600"><?php echo htmlspecialchars($req['description']); ?></p>
                        <p class="text-[10px] text-slate-400">Logged: <?php echo $req['requestDate']; ?></p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $req['requestStatus'] === 'Resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'; ?>">
                        <?php echo $req['requestStatus']; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>