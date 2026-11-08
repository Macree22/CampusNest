<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

$notifStmt = $pdo->prepare("
    SELECT *
    FROM Notification
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$notifStmt->execute([$userId]);
$notifications = $notifStmt->fetchAll();
?>

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Inbox</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Notifications</h2>
            <p class="mt-1 text-sm text-slate-600">Messages about applications, maintenance, and lease updates.</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
            <?php echo count($notifications); ?> total
        </span>
    </div>

    <div class="space-y-4">
        <?php if (count($notifications) === 0): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500 shadow-sm">
                You have no notifications right now.
            </div>
        <?php endif; ?>

        <?php foreach ($notifications as $notification): ?>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-lg text-slate-600">
                            <?php echo $notification['type'] === 'success' ? '✓' : ($notification['type'] === 'warning' ? '!' : ($notification['type'] === 'info' ? 'i' : '•')); ?>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-slate-900"><?php echo htmlspecialchars($notification['title']); ?></h3>
                            <p class="mt-1 text-sm text-slate-600"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                        </div>
                    </div>
                    <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-[11px] font-semibold <?php echo $notification['is_read'] ? 'bg-slate-100 text-slate-600' : 'bg-blue-50 text-blue-700'; ?>">
                        <?php echo $notification['is_read'] ? 'Read' : 'New'; ?>
                    </span>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-xs text-slate-500">
                    <span><?php echo htmlspecialchars($notification['created_at']); ?></span>
                    <span class="font-medium uppercase tracking-[0.14em] text-slate-400"><?php echo htmlspecialchars($notification['type']); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>