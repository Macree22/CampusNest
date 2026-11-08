<?php
// Ensure only administrators can access this view
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Fetch all registered students and landlords
$students = $pdo->query("SELECT * FROM Student ORDER BY registrationDate DESC")->fetchAll();
$landlords = $pdo->query("SELECT * FROM Landlord ORDER BY dateJoined DESC")->fetchAll();
?>

<div class="mx-auto max-w-6xl space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Administration module</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">User account management</h2>
            <p class="mt-1 text-sm text-slate-600">Monitor and manage registered students and landlords.</p>
        </div>
        <a href="?view=admin" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Back to Dashboard</a>
    </div>

    <!-- Students Directory -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-0 text-left text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Student #</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Full Name</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Email Address</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Status</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Registered Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (count($students) === 0): ?>
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No student accounts found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($students as $stu): ?>
                        <tr class="bg-white">
                            <td class="px-6 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($stu['studentNumber'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 text-slate-700"><?php echo htmlspecialchars($stu['firstName'] . ' ' . $stu['lastName']); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?php echo htmlspecialchars($stu['email']); ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700"><?php echo htmlspecialchars($stu['accountStatus'] ?? 'Active'); ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?php echo date('Y-m-d', strtotime($stu['registrationDate'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Landlords Directory -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full border-separate border-spacing-0 text-left text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Company / Name</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Owner Name</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Email Address</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Verification Status</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Date Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (count($landlords) === 0): ?>
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No landlord accounts found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($landlords as $land): ?>
                        <tr class="bg-white">
                            <td class="px-6 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($land['companyName'] ?? 'Independent'); ?></td>
                            <td class="px-6 py-4 text-slate-700"><?php echo htmlspecialchars($land['firstName'] . ' ' . $land['lastName']); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?php echo htmlspecialchars($land['email']); ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700"><?php echo htmlspecialchars($land['verificationStatus'] ?? 'Verified'); ?></span>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?php echo date('Y-m-d', strtotime($land['dateJoined'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>