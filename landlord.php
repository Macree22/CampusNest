<?php
$pendStmt = $pdo->prepare("SELECT a.*, s.firstName, s.lastName, s.studentNumber, l.title FROM Application a JOIN Student s ON a.studentID = s.studentID JOIN Listing l ON a.listingID = l.listingID WHERE l.landlordID = ? AND a.applicationStatus = 'Pending'");
$pendStmt->execute([$_SESSION['user_id']]);
$pendingApplications = $pendStmt->fetchAll();

$listStmt = $pdo->prepare("SELECT * FROM Listing WHERE landlordID = ? ORDER BY dateListingCreated DESC");
$listStmt->execute([$_SESSION['user_id']]);
$myListings = $listStmt->fetchAll();

$payStmt = $pdo->prepare("SELECT p.*, s.firstName, s.lastName, l.title FROM Payment p JOIN Student s ON p.studentID = s.studentID JOIN Listing l ON p.listingID = l.listingID WHERE l.landlordID = ? ORDER BY p.paymentDate DESC");
$payStmt->execute([$_SESSION['user_id']]);
$recentPayments = $payStmt->fetchAll();

$maintStmt = $pdo->prepare("SELECT m.*, s.firstName, s.lastName, l.title FROM MaintenanceRequest m JOIN Student s ON m.studentID = s.studentID JOIN Listing l ON m.listingID = l.listingID WHERE l.landlordID = ? ORDER BY m.requestDate DESC");
$maintStmt->execute([$_SESSION['user_id']]);
$maintenanceRequests = $maintStmt->fetchAll();
?>
<div class="space-y-8">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Landlord portal</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Portfolio overview</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="?view=properties" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Manage Units</a>
                <a href="?view=reports" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Reports</a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-900">Pending Applications</h3>
                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700"><?php echo count($pendingApplications); ?></span>
            </div>

            <div class="divide-y divide-slate-100">
                <?php foreach ($pendingApplications as $app): ?>
                    <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div class="min-w-0">
                            <p class="truncate text-base font-semibold text-slate-900"><?php echo htmlspecialchars($app['firstName'] . ' ' . $app['lastName']); ?></p>
                            <p class="mt-1 truncate text-sm text-slate-500"><?php echo htmlspecialchars($app['title']); ?></p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">Pending</span>

                            <form method="post" class="m-0">
                                <input type="hidden" name="action" value="approve">
                                <input type="hidden" name="applicationID" value="<?php echo $app['applicationID']; ?>">
                                <button type="submit" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Approve</button>
                            </form>

                            <form method="post" class="m-0">
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="applicationID" value="<?php echo $app['applicationID']; ?>">
                                <button type="submit" class="rounded-xl bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100">Reject</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Add New Listing</h3>

            <form method="post" enctype="multipart/form-data" class="mt-5 space-y-4">
                <input type="hidden" name="action" value="add_listing">

                <div>
                    <input type="text" name="title" placeholder="Property title" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:outline-none">
                </div>

                <div>
                    <input type="text" name="address" placeholder="Address / suburb" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:outline-none">
                </div>

                <div>
                    <input type="number" name="monthlyRent" placeholder="Monthly rent (R)" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:outline-none">
                </div>

                <div>
                    <select name="roomType" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 focus:border-slate-300 focus:bg-white focus:outline-none">
                        <option>Single</option>
                        <option>Shared</option>
                        <option>Studio</option>
                        <option>Bachelor</option>
                    </select>
                </div>

                <div>
                    <input type="file" name="listingImage" accept="image/*" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white">
                </div>

                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">Submit Listing</button>
            </form>
        </aside>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Tenant Maintenance Requests</h3>
            <div class="space-y-3">
                <?php foreach ($maintenanceRequests as $req): ?>
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900"><?php echo htmlspecialchars($req['title']); ?> (<?php echo $req['urgencyLevel']; ?>)</p>
                                <p class="mt-1 text-xs text-slate-600"><?php echo htmlspecialchars($req['description']); ?></p>
                            </div>
                            <span class="rounded-full bg-slate-200 px-2 py-1 text-[10px] font-semibold text-slate-700"><?php echo htmlspecialchars($req['requestStatus']); ?></span>
                        </div>
                        <form method="post" class="mt-3 space-y-2">
                            <input type="hidden" name="action" value="update_maintenance">
                            <input type="hidden" name="requestID" value="<?php echo $req['requestID']; ?>">
                            <div class="flex gap-2">
                                <select name="requestStatus" class="flex-1 rounded-lg border border-slate-200 bg-white p-2 text-xs">
                                    <option value="Submitted" <?php echo $req['requestStatus']=='Submitted'?'selected':'';?>>Submitted</option>
                                    <option value="In Progress" <?php echo $req['requestStatus']=='In Progress'?'selected':'';?>>In Progress</option>
                                    <option value="Resolved" <?php echo $req['requestStatus']=='Resolved'?'selected':'';?>>Resolved</option>
                                    <option value="Closed" <?php echo $req['requestStatus']=='Closed'?'selected':'';?>>Closed</option>
                                </select>
                                <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Save</button>
                            </div>
                            <textarea name="landlordComment" rows="2" placeholder="Add a landlord note..." class="w-full rounded-lg border border-slate-200 bg-white p-2 text-xs text-slate-700 placeholder:text-slate-400"><?php echo htmlspecialchars($req['landlordComment'] ?? ''); ?></textarea>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Rental Income Logs</h3>
            <div class="space-y-2">
                <?php foreach ($recentPayments as $pay): ?>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3 text-sm">
                        <span><b><?php echo $pay['firstName']; ?></b> - <?php echo $pay['title']; ?></span>
                        <span class="font-bold text-emerald-600"><?php echo randFormat($pay['amountPaid']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>