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

<div class="space-y-8 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Administration Module</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">User Account Management</h2>
            <p class="mt-1 text-sm text-slate-600">Monitor and manage registered students and property landlords.</p>
        </div>
        <a href="?view=admin" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Back to Dashboard</a>
    </div>

    <!-- Students Directory -->
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
        <h3 class="text-xl font-bold text-slate-900">Registered Students (<?php echo count($students); ?>)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Student #</th>
                        <th class="px-4 py-3">Full Name</th>
                        <th class="px-4 py-3">Email Address</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Registered Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (count($students) === 0): ?>
                        <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No student accounts found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($students as $stu): ?>
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($stu['studentNumber'] ?? 'N/A'); ?></td>
                            <td class="px-4 py-4 text-slate-700"><?php echo htmlspecialchars($stu['firstName'] . ' ' . $stu['lastName']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($stu['email']); ?></td>
                            <td class="px-4 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    <?php echo htmlspecialchars($stu['accountStatus'] ?? 'Active'); ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-slate-500"><?php echo date('Y-m-d', strtotime($stu['registrationDate'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Landlords Directory -->
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
        <h3 class="text-xl font-bold text-slate-900">Registered Landlords (<?php echo count($landlords); ?>)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Company / Name</th>
                        <th class="px-4 py-3">Owner Name</th>
                        <th class="px-4 py-3">Email Address</th>
                        <th class="px-4 py-3">Verification Status</th>
                        <th class="px-4 py-3">Date Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (count($landlords) === 0): ?>
                        <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No landlord accounts found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($landlords as $land): ?>
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-semibold text-slate-900"><?php echo htmlspecialchars($land['companyName'] ?? 'Independent'); ?></td>
                            <td class="px-4 py-4 text-slate-700"><?php echo htmlspecialchars($land['firstName'] . ' ' . $land['lastName']); ?></td>
                            <td class="px-4 py-4 text-slate-600"><?php echo htmlspecialchars($land['email']); ?></td>
                            <td class="px-4 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">
                                    <?php echo htmlspecialchars($land['verificationStatus'] ?? 'Verified'); ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-slate-500"><?php echo date('Y-m-d', strtotime($land['dateJoined'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>