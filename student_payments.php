<?php
$payStmt = $pdo->prepare("
    SELECT p.*, l.title, l.address 
    FROM Payment p 
    JOIN Listing l ON p.listingID = l.listingID 
    WHERE p.studentID = ? 
    ORDER BY p.paymentDate DESC
");
$payStmt->execute([$_SESSION['user_id']]);
$myPayments = $payStmt->fetchAll();
?>
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Student Portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Payment History & Receipts</h2>
            <p class="mt-1 text-sm text-slate-600">View and download official payment receipts for your accommodations[cite: 2].</p>
        </div>
        <div class="flex gap-2">
            <a href="?view=search" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Search</a>
            <a href="?view=applications" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold">Applications</a>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Transaction Ref</th>
                        <th class="px-4 py-3">Property</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Method</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (count($myPayments) === 0): ?>
                        <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">No payment records found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($myPayments as $pay): ?>
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-medium text-slate-900"><?php echo htmlspecialchars($pay['transactionReference']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($pay['title']); ?></td>
                            <td class="px-4 py-4 font-semibold text-emerald-600"><?php echo randFormat($pay['amountPaid']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($pay['paymentMethod']); ?></td>
                            <td class="px-4 py-4 text-slate-500"><?php echo date('Y-m-d', strtotime($pay['paymentDate'])); ?></td>
                            <td class="px-4 py-4">
                                <a href="?view=receipt&paymentID=<?php echo $pay['paymentID']; ?>" class="inline-flex items-center px-3 py-1.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-semibold">View Receipt</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>