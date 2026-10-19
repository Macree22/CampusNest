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
<div class="space-y-6">                 
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">                     
        <div>                             
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Landlord Portal</p>                             
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Portfolio & Application Management</h2>                         
        </div>                         
        <div class="flex flex-wrap gap-2 items-center">
            <a href="?view=properties" class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Manage Units</a>
            <a href="?view=reports" class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">View Reports</a>
            <span class="px-4 py-2.5 bg-blue-50 text-blue-900 rounded-2xl text-sm font-semibold"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
        </div>                     
    </div>                 

    <div class="grid gap-6 xl:grid-cols-2">                     
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">                         
            <h3 class="text-xl font-bold">Pending Applications (<?php echo count($pendingApplications); ?>)</h3>                         
            <?php foreach ($pendingApplications as $app): ?>                                 
                <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center">                                     
                    <div>                                             
                        <p class="text-xs font-semibold text-slate-500 uppercase"><?php echo htmlspecialchars($app['title']); ?></p>                                             
                        <h4 class="font-bold text-slate-900"><?php echo htmlspecialchars($app['firstName'] . ' ' . $app['lastName']); ?></h4>                                             
                        <p class="text-xs text-slate-600">Move-in: <?php echo $app['preferredMoveInDate']; ?></p>                                         
                    </div>                                         
                    <div class="flex gap-2">                                             
                        <form method="post"><input type="hidden" name="action" value="approve"><input type="hidden" name="applicationID" value="<?php echo $app['applicationID']; ?>"><button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-semibold">Approve</button></form>                                             
                        <form method="post"><input type="hidden" name="action" value="reject"><input type="hidden" name="applicationID" value="<?php echo $app['applicationID']; ?>"><button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-xl text-xs font-semibold">Reject</button></form>                                         
                    </div>                                     
                </div>                             
            <?php endforeach; ?>                         
        </div>                     

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">                         
            <h3 class="text-xl font-bold mb-4">Add New Listing</h3>                         
            <form method="post" enctype="multipart/form-data" class="space-y-3">                             
                <input type="hidden" name="action" value="add_listing">                             
                <input type="text" name="title" placeholder="Property Title" required class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 text-sm">                             
                <input type="text" name="address" placeholder="Address / Suburb" required class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 text-sm">                             
                <input type="number" name="monthlyRent" placeholder="Monthly Rent (R)" required class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 text-sm">                             
                <select name="roomType" class="w-full p-3 rounded-xl border border-slate-200 bg-slate-50 text-sm">                                 
                    <option>Single</option><option>Shared</option><option>Studio</option><option>Bachelor</option>                             
                </select>                             
                <input type="file" name="listingImage" accept="image/*" required class="w-full p-2 border border-slate-200 rounded-xl text-sm">                             
                <button type="submit" class="w-full bg-blue-600 py-3 rounded-xl text-white font-semibold text-sm hover:bg-blue-700">Submit Listing</button>                     
            </form>                 
        </div>             
    </div>                 

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-xl font-bold mb-4">Tenant Maintenance Requests</h3>
            <div class="space-y-3">
                <?php foreach ($maintenanceRequests as $req): ?>
                    <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center text-sm">
                        <div>
                            <p class="font-semibold"><?php echo htmlspecialchars($req['title']); ?> (<?php echo $req['urgencyLevel']; ?>)</p>
                            <p class="text-slate-600 text-xs"><?php echo htmlspecialchars($req['description']); ?></p>
                        </div>
                        <form method="post" class="flex gap-2">
                            <input type="hidden" name="action" value="update_maintenance">
                            <input type="hidden" name="requestID" value="<?php echo $req['requestID']; ?>">
                            <select name="requestStatus" class="border rounded-lg p-1 text-xs">
                                <option value="Submitted" <?php echo $req['requestStatus']=='Submitted'?'selected':'';?>>Submitted</option>
                                <option value="In Progress" <?php echo $req['requestStatus']=='In Progress'?'selected':'';?>>In Progress</option>
                                <option value="Resolved" <?php echo $req['requestStatus']=='Resolved'?'selected':'';?>>Resolved</option>
                            </select>
                            <button type="submit" class="bg-slate-900 text-white px-2 py-1 rounded-lg text-xs">Save</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-xl font-bold mb-4">Rental Income Logs</h3>
            <div class="space-y-2">
                <?php foreach ($recentPayments as $pay): ?>
                    <div class="p-3 bg-slate-50 rounded-xl flex justify-between text-sm">
                        <span><b><?php echo $pay['firstName']; ?></b> - <?php echo $pay['title']; ?></span>
                        <span class="font-bold text-emerald-600"><?php echo randFormat($pay['amountPaid']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>