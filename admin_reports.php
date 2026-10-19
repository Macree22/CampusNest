<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$auditPayments = $pdo->query("
    SELECT p.*, s.firstName, s.lastName, l.title, l.city 
    FROM Payment p 
    JOIN Student s ON p.studentID = s.studentID 
    JOIN Listing l ON p.listingID = l.listingID 
    ORDER BY p.paymentDate DESC
")->fetchAll();

$totalSuccessful = array_sum(array_column(array_filter($auditPayments, fn($p) => $p['paymentStatus'] === 'Successful'), 'amountPaid'));
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Administration Module</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Platform Financial Audit Report</h2>
            <p class="text-sm text-slate-600 mt-1">Total Successful Revenue: <b><?php echo randFormat($totalSuccessful); ?></b></p>
        </div>
        <div class="flex gap-2">
            <a href="index.php?view=admin" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Dashboard</a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">Print Audit</button>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Student</th>
                        <th class="px-4 py-3">Property</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Method</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($auditPayments as $pay): ?>
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-medium text-slate-900"><?php echo htmlspecialchars($pay['transactionReference']); ?></td>
                            <td class="px-4 py-4 text-slate-700"><?php echo htmlspecialchars($pay['firstName'] . ' ' . $pay['lastName']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($pay['title']); ?></td>
                            <td class="px-4 py-4 font-bold text-emerald-600"><?php echo randFormat($pay['amountPaid']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($pay['paymentMethod']); ?></td>
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $pay['paymentStatus'] === 'Successful' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'; ?>">
                                    <?php echo $pay['paymentStatus']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-slate-500"><?php echo date('Y-m-d', strtotime($pay['paymentDate'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>