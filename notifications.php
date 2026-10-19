<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userID = $_SESSION['user_id'];
$role = ucfirst($_SESSION['role']);

$notifStmt = $pdo->prepare("SELECT * FROM Notification WHERE recipientID = ? AND recipientType = ? ORDER BY dateCreated DESC LIMIT 30");
$notifStmt->execute([$userID, $role]);
$notifications = $notifStmt->fetchAll();

// Automatically mark unread alerts as read when viewed
$pdo->prepare("UPDATE Notification SET isRead = 1 WHERE recipientID = ? AND recipientType = ? AND isRead = 0")->execute([$userID, $role]);
?>

<div class="space-y-6 max-w-3xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">System Center</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Notifications & Alerts</h2>
            <p class="mt-1 text-sm text-slate-600">Recent updates regarding your accommodations and transactions.</p>
        </div>
        <a href="index.php" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Dashboard</a>
    </div>

    <div class="space-y-3">
        <?php if (count($notifications) === 0): ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center text-slate-500">You have no new notifications.</div>
        <?php endif; ?>
        <?php foreach ($notifications as $notif): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex justify-between items-start gap-4">
                <div class="space-y-1">
                    <p class="text-sm text-slate-900 font-medium"><?php echo htmlspecialchars($notif['message']); ?></p>
                    <p class="text-xs text-slate-400"><?php echo date('Y-m-d H:i', strtotime($notif['dateCreated'])); ?></p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold <?php echo $notif['isRead'] ? 'bg-slate-100 text-slate-600' : 'bg-blue-100 text-blue-800'; ?>">
                    <?php echo $notif['isRead'] ? 'Read' : 'New'; ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>