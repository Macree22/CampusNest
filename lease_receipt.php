<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$bookingID = $_GET['bookingID'] ?? null;
if (!$bookingID) {
    echo "<div class='p-8 text-center text-red-600'>No lease record selected.</div>";
    exit;
}

$stmt = $pdo->prepare("
    SELECT b.*, s.firstName AS studentFirst, s.lastName AS studentLast, s.email AS studentEmail, s.studentNumber,
           l.title, l.address, l.monthlyRent, l.roomType,
           land.firstName AS landlordFirst, land.lastName AS landlordLast, land.email AS landlordEmail,
           land.companyName AS landlordCompany
    FROM Booking b
    JOIN Student s ON b.studentID = s.studentID
    JOIN Listing l ON b.listingID = l.listingID
    JOIN Landlord land ON l.landlordID = land.landlordID
    WHERE b.bookingID = ? AND b.studentID = ?
");
$stmt->execute([$bookingID, $_SESSION['user_id']]);
$lease = $stmt->fetch();

if (!$lease) {
    echo "<div class='p-8 text-center text-red-600'>Lease not found or not authorized.</div>";
    exit;
}

$startDate = new DateTime($lease['bookingDate']);
$endDate = (clone $startDate)->modify('+' . (int)$lease['durationMonths'] . ' months');
$contractValue = (float)($lease['totalContractValue'] ?: ($lease['monthlyRent'] * $lease['durationMonths']));
?>

<div class="max-w-4xl mx-auto my-8 bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-200 px-8 py-6 bg-slate-50">
        <div>
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">CampusNest</p>
            <h2 class="mt-2 text-3xl font-bold text-slate-900">Lease Agreement & Receipt</h2>
        </div>
        <button onclick="window.print()" class="rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-700 transition">Print / Save as PDF</button>
    </div>

    <div class="px-8 py-8 space-y-8">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="rounded-2xl border border-slate-200 p-5 bg-slate-50">
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Student</p>
                <div class="mt-3 space-y-1 text-sm text-slate-700">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($lease['studentFirst'] . ' ' . $lease['studentLast']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($lease['studentEmail']); ?></p>
                    <p><strong>Student Number:</strong> <?php echo htmlspecialchars($lease['studentNumber']); ?></p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-5 bg-slate-50">
                <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Landlord</p>
                <div class="mt-3 space-y-1 text-sm text-slate-700">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($lease['landlordFirst'] . ' ' . $lease['landlordLast']); ?></p>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($lease['landlordCompany']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($lease['landlordEmail']); ?></p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Property Details</p>
            <div class="mt-3 grid md:grid-cols-3 gap-4 text-sm text-slate-700">
                <p><strong>Title:</strong> <?php echo htmlspecialchars($lease['title']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($lease['address']); ?></p>
                <p><strong>Room Type:</strong> <?php echo htmlspecialchars($lease['roomType']); ?></p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 p-5">
            <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Lease Summary</p>
            <div class="mt-4 grid md:grid-cols-4 gap-4 text-sm text-slate-700">
                <div>
                    <p class="text-slate-500 text-xs uppercase">Start Date</p>
                    <p class="mt-1 font-semibold text-slate-900"><?php echo htmlspecialchars($lease['bookingDate']); ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase">End Date</p>
                    <p class="mt-1 font-semibold text-slate-900"><?php echo htmlspecialchars($endDate->format('Y-m-d')); ?></p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase">Duration</p>
                    <p class="mt-1 font-semibold text-slate-900"><?php echo (int)$lease['durationMonths']; ?> months</p>
                </div>
                <div>
                    <p class="text-slate-500 text-xs uppercase">Monthly Rent</p>
                    <p class="mt-1 font-semibold text-emerald-700"><?php echo 'R' . number_format((float)$lease['monthlyRent'], 2, '.', ','); ?></p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 p-5 bg-emerald-50">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs uppercase tracking-wider text-emerald-700 font-semibold">Total Contract Value</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-900"><?php echo 'R' . number_format($contractValue, 2, '.', ','); ?></p>
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wider text-slate-500">Status</p>
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1.5 text-sm font-semibold text-emerald-800 mt-2"><?php echo htmlspecialchars($lease['bookingStatus']); ?></span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 p-5 text-sm text-slate-700">
            <p class="font-semibold text-slate-900 mb-2">Lease Agreement Terms</p>
            <p>This lease agreement confirms the student’s accommodation tenancy from <?php echo htmlspecialchars($lease['bookingDate']); ?> to <?php echo htmlspecialchars($endDate->format('Y-m-d')); ?> for the property described above. The student agrees to pay the monthly rent of <?php echo 'R' . number_format((float)$lease['monthlyRent'], 2, '.', ','); ?>, with a total contract value of <?php echo 'R' . number_format($contractValue, 2, '.', ','); ?> over <?php echo (int)$lease['durationMonths']; ?> months.</p>
        </div>

        <div class="text-xs text-slate-500 border-t border-slate-200 pt-4">
            <p>Generated on <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            background: #ffffff !important;
        }

        .header, .footer, nav, .topbar, .sidebar, button {
            display: none !important;
        }

        .max-w-4xl {
            max-width: 100% !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }

        .rounded-3xl, .rounded-2xl {
            border-radius: 0 !important;
            border: 1px solid #e2e8f0 !important;
        }
    }
</style>
