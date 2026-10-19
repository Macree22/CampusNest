<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: index.php");
    exit;
}

$landlordID = $_SESSION['user_id'];
$earningsStmt = $pdo->prepare("
    SELECT p.*, l.title, s.firstName, s.lastName 
    FROM Payment p 
    JOIN Listing l ON p.listingID = l.listingID 
    JOIN Student s ON p.studentID = s.studentID 
    WHERE l.landlordID = ? AND p.paymentStatus = 'Successful' 
    ORDER BY p.paymentDate DESC
");
$earningsStmt->execute([$landlordID]);
$earnings = $earningsStmt->fetchAll();

$totalEarnings = array_sum(array_column($earnings, 'amountPaid'));
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Landlord Portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Financial Earnings Analytics</h2>
            <p class="text-sm text-slate-600 mt-1">Total Realized Rental Income: <b><?php echo randFormat($totalEarnings); ?></b></p>
        </div>
        <a href="index.php" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Dashboard</a>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Transaction Ref</th>
                        <th class="px-4 py-3">Property Unit</th>
                        <th class="px-4 py-3">Tenant Name</th>
                        <th class="px-4 py-3">Amount Received</th>
                        <th class="px-4 py-3">Payment Method</th>
                        <th class="px-4 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (count($earnings) === 0): ?>
                        <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">No payment records found for your properties.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($earnings as $earn): ?>
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-medium text-slate-900"><?php echo htmlspecialchars($earn['transactionReference']); ?></td>
                            <td class="px-4 py-4 text-slate-700"><?php echo htmlspecialchars($earn['title']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($earn['firstName'] . ' ' . $earn['lastName']); ?></td>
                            <td class="px-4 py-4 font-bold text-emerald-600"><?php echo randFormat($earn['amountPaid']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($earn['paymentMethod']); ?></td>
                            <td class="px-4 py-4 text-slate-500"><?php echo date('Y-m-d', strtotime($earn['paymentDate'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>