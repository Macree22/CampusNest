<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Occupancy & Listing Metrics
$stats = $pdo->query("
    SELECT 
        COUNT(*) as total, 
        SUM(availabilityStatus = 'Occupied') as occupied, 
        SUM(availabilityStatus = 'Vacant') as vacant,
        SUM(availabilityStatus = 'Under Maintenance') as maintenance
    FROM Listing
")->fetch();

$totalListings = max(1, $stats['total']);
$occupancyRate = round(($stats['occupied'] / $totalListings) * 100, 1);

// Financial Collection Metrics
$revenueStats = $pdo->query("
    SELECT 
        SUM(CASE WHEN paymentStatus = 'Successful' THEN amountPaid ELSE 0 END) as totalCollected,
        SUM(CASE WHEN paymentStatus = 'Pending' THEN amountPaid ELSE 0 END) as totalPending
    FROM Payment
")->fetch();

// Application & Booking Totals
$appCounts = $pdo->query("
    SELECT 
        COUNT(*) as totalApps,
        SUM(applicationStatus = 'Pending') as pendingApps,
        SUM(applicationStatus = 'Approved') as approvedApps
    FROM Application
")->fetch();

$activeBookings = $pdo->query("SELECT COUNT(*) FROM Booking WHERE bookingStatus = 'Active'")->fetchColumn();
$pendingListings = $pdo->query("SELECT l.*, land.firstName, land.lastName FROM Listing l JOIN Landlord land ON l.landlordID = land.landlordID WHERE l.isVerifiedByAdmin = 0")->fetchAll();
$recentPayments = $pdo->query("SELECT p.*, s.firstName, s.lastName, l.title FROM Payment p JOIN Student s ON p.studentID = s.studentID JOIN Listing l ON p.listingID = l.listingID ORDER BY p.paymentDate DESC LIMIT 5")->fetchAll();
?>

<div class="space-y-8 max-w-6xl mx-auto">                 
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">                     
        <div>                             
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Administration Module</p>                             
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Platform Governance & Financial Analytics</h2>                         
        </div>
        <div class="flex gap-2">
            <a href="index.php?view=users" class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Manage Users</a>
        </div>
    </div>                 

    <!-- High-Level Metric Cards -->
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">                     
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-slate-500 text-xs uppercase tracking-wider font-semibold">Occupancy Rate</p>
            <p class="text-3xl font-bold mt-2 text-blue-600"><?php echo $occupancyRate; ?>%</p>
            <p class="text-xs text-slate-400 mt-1"><?php echo $stats['occupied']; ?> of <?php echo $stats['total']; ?> units occupied</p>
        </div>                     
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-slate-500 text-xs uppercase tracking-wider font-semibold">Total Revenue Collected</p>
            <p class="text-3xl font-bold mt-2 text-emerald-600"><?php echo randFormat($revenueStats['totalCollected'] ?? 0); ?></p>
            <p class="text-xs text-slate-400 mt-1">Successful transactions</p>
        </div>                     
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-slate-500 text-xs uppercase tracking-wider font-semibold">Active Tenancies</p>
            <p class="text-3xl font-bold mt-2 text-slate-900"><?php echo $activeBookings; ?></p>
            <p class="text-xs text-slate-400 mt-1">Bound by active lease bookings</p>
        </div>                     
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-slate-500 text-xs uppercase tracking-wider font-semibold">Pending Approvals</p>
            <p class="text-3xl font-bold mt-2 text-amber-600"><?php echo $appCounts['pendingApps']; ?></p>
            <p class="text-xs text-slate-400 mt-1">Awaiting landlord review</p>
        </div>                 
    </div>     

    <div class="grid gap-6 xl:grid-cols-2">
        <!-- Pending Verification Queue -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">                     
            <h3 class="text-xl font-bold text-slate-900">Pending Listings Verification (<?php echo count($pendingListings); ?>)</h3>                     
            <div class="space-y-3 max-h-80 overflow-y-auto pr-1">                         
                <?php if (count($pendingListings) === 0): ?>
                    <p class="text-sm text-slate-500 py-6 text-center">No property listings currently require verification.</p>
                <?php endif; ?>
                <?php foreach ($pendingListings as $listing): ?>                                     
                    <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center text-sm">                                         
                        <div>
                            <p class="font-semibold text-slate-900"><?php echo htmlspecialchars($listing['title']); ?></p>
                            <p class="text-xs text-slate-500">Owner: <?php echo htmlspecialchars($listing['firstName'] . ' ' . $listing['lastName']); ?> &bull; <?php echo randFormat($listing['monthlyRent']); ?></p>
                        </div>
                        <form method="post">                                             
                            <input type="hidden" name="action" value="verify_listing">                                             
                            <input type="hidden" name="listingID" value="<?php echo $listing['listingID']; ?>">                                             
                            <button type="submit" class="bg-emerald-600 text-white px-3 py-2 rounded-xl text-xs font-semibold hover:bg-emerald-700 transition">Verify</button>                                         
                        </form>                                     
                    </div>                                 
                <?php endforeach; ?>                     
            </div>                 
        </div>

        <!-- Recent System Transactions Feed -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
            <h3 class="text-xl font-bold text-slate-900">Recent Payment Transactions</h3>
            <div class="space-y-3">
                <?php foreach ($recentPayments as $pay): ?>
                    <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center text-sm">
                        <div>
                            <p class="font-semibold text-slate-900"><?php echo htmlspecialchars($pay['firstName'] . ' ' . $pay['lastName']); ?></p>
                            <p class="text-xs text-slate-500"><?php echo htmlspecialchars($pay['title']); ?> &bull; Ref: <?php echo htmlspecialchars($pay['transactionReference']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-emerald-600"><?php echo randFormat($pay['amountPaid']); ?></p>
                            <p class="text-[10px] text-slate-400"><?php echo date('Y-m-d', strtotime($pay['paymentDate'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>