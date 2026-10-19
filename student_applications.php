<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$appStmt = $pdo->prepare("SELECT a.*, l.title, l.roomType, l.listingID, l.monthlyRent FROM Application a JOIN Listing l ON a.listingID = l.listingID WHERE a.studentID = ?");
$appStmt->execute([$_SESSION['user_id']]);
$studentApplications = $appStmt->fetchAll();
?>

<div class="space-y-6 max-w-4xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Student Portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">My Accommodation Applications</h2>
        </div>
        <div class="flex gap-2">
            <a href="?view=search" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Browse Search</a>
            <a href="?view=payments" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Payments</a>
        </div>
    </div>

    <div class="space-y-4">
        <?php if (count($studentApplications) === 0): ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center text-slate-500">You have not submitted any property applications yet.</div>
        <?php endif; ?>
        <?php foreach ($studentApplications as $application): ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($application['title']); ?></h4>
                        <p class="text-sm text-slate-600">Applied: <?php echo $application['applicationDate']; ?> | Move-in: <?php echo $application['preferredMoveInDate']; ?></p>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-sm font-semibold <?php echo $application['applicationStatus'] === 'Approved' ? 'bg-emerald-100 text-emerald-800' : ($application['applicationStatus'] === 'Pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'); ?>">
                        <?php echo $application['applicationStatus']; ?>
                    </span>
                </div>
                <?php if ($application['applicationStatus'] === 'Approved'): ?>
                    <form method="post" class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <input type="hidden" name="action" value="pay">
                        <input type="hidden" name="applicationID" value="<?php echo $application['applicationID']; ?>">
                        <input type="hidden" name="listingID" value="<?php echo $application['listingID']; ?>">
                        <input type="hidden" name="amount" value="<?php echo $application['monthlyRent']; ?>">
                        <select name="paymentMethod" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                            <option value="Credit Card">Credit Card</option>
                            <option value="EFT">EFT</option>
                        </select>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Pay Rent <?php echo randFormat($application['monthlyRent']); ?></button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>