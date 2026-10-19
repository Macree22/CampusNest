<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$logs = $pdo->query("SELECT * FROM Notification ORDER BY dateCreated DESC LIMIT 50")->fetchAll();
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Administration Module</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">System Notification & Activity Logs</h2>
            <p class="text-sm text-slate-600 mt-1">Auditing recent platform triggers across all users.</p>
        </div>
        <a href="index.php?view=admin" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Dashboard</a>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Log ID</th>
                        <th class="px-4 py-3">Recipient Type</th>
                        <th class="px-4 py-3">Recipient ID</th>
                        <th class="px-4 py-3">Activity Message</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (count($logs) === 0): ?>
                        <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">No activity logs recorded.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-medium text-slate-900">LOG-<?php echo str_pad($log['notificationID'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-4 py-4 text-slate-700 font-semibold"><?php echo htmlspecialchars($log['recipientType']); ?></td>
                            <td class="px-4 py-4 text-slate-600">ID #<?php echo htmlspecialchars($log['recipientID']); ?></td>
                            <td class="px-4 py-4 text-slate-800"><?php echo htmlspecialchars($log['message']); ?></td>
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $log['isRead'] ? 'bg-slate-100 text-slate-600' : 'bg-blue-100 text-blue-800'; ?>">
                                    <?php echo $log['isRead'] ? 'Read' : 'Unread'; ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-slate-500"><?php echo htmlspecialchars($log['dateCreated']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>