<?php
// Debugging check: Fetch total listings regardless of filters
$debugStmt = $pdo->query("SELECT COUNT(*) FROM Listing");
$totalListingsInDb = $debugStmt->fetchColumn();

$debugVerified = $pdo->query("SELECT COUNT(*) FROM Listing WHERE isVerifiedByAdmin = 1 AND availabilityStatus = 'Vacant'");
$totalVacantVerified = $debugVerified->fetchColumn();

// Standard Filter Logic
$locationFilter = trim($_GET['location'] ?? ''); 
$roomFilter = $_GET['roomType'] ?? 'All'; 
$minPrice = $_GET['minPrice'] ?? ''; 
$maxPrice = $_GET['maxPrice'] ?? ''; 

$query = "SELECT * FROM Listing WHERE availabilityStatus = 'Vacant' AND isVerifiedByAdmin = 1";
$params = [];
if ($roomFilter !== 'All') { $query .= " AND roomType = ?"; $params[] = $roomFilter; }
if ($locationFilter !== '') { $query .= " AND address LIKE ?"; $params[] = "%$locationFilter%"; }
if ($minPrice !== '') { $query .= " AND monthlyRent >= ?"; $params[] = (int)$minPrice; }
if ($maxPrice !== '') { $query .= " AND monthlyRent <= ?"; $params[] = (int)$maxPrice; }

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vacantListings = $stmt->fetchAll();

// If filters returned nothing, fallback to showing all verified vacant listings as a safety net
if (empty($vacantListings) && ($locationFilter !== '' || $roomFilter !== 'All' || $minPrice !== '' || $maxPrice !== '')) {
    $fallbackStmt = $pdo->query("SELECT * FROM Listing WHERE availabilityStatus = 'Vacant' AND isVerifiedByAdmin = 1");
    $vacantListings = $fallbackStmt->fetchAll();
    $filterMessage = "No properties matched your exact filters. Showing all available listings instead.";
}
?>

<div class="space-y-6">                 
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">                     
        <div>                             
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Student Portal</p>                             
            <h2 class="mt-2 text-3xl font-semibold text-slate-900">Find Your Student Accommodation</h2>                         
        </div>                         
        <div class="flex gap-3">                             
            <a href="index.php?view=search" class="px-4 py-3 rounded-2xl font-semibold bg-blue-600 text-white">Search</a>                             
            <a href="index.php?view=applications" class="px-4 py-3 rounded-2xl font-semibold bg-slate-100 text-slate-700">My Applications</a>                         
        </div>                     
    </div>                 

    <!-- System Diagnostic Banner -->
    <div class="rounded-2xl bg-slate-100 p-4 text-xs text-slate-600 flex justify-between items-center">
        <span><b>System Diagnostics:</b> Total properties in database: <b><?php echo $totalListingsInDb; ?></b> | Vacant & Verified properties: <b><?php echo $totalVacantVerified; ?></b></span>
        <?php if ($totalListingsInDb == 0): ?>
            <a href="setup_db.php" class="text-blue-600 font-bold underline">Run setup_db.php to seed data</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($filterMessage)): ?>
        <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800">
            <?php echo $filterMessage; ?>
        </div>
    <?php endif; ?>

    <div class="grid gap-6 xl:grid-cols-[320px_1fr]">                         
        <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm h-fit">                             
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Filters</h3>                             
            <form method="get" class="space-y-4">                                 
                <input type="hidden" name="view" value="search">                                 
                <label class="block text-sm font-medium text-slate-700">Location</label>                                 
                <input type="text" name="location" value="<?php echo htmlspecialchars($locationFilter); ?>" placeholder="e.g. Braamfontein" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none">                                 
                <label class="block text-sm font-medium text-slate-700">Room Type</label>                                 
                <select name="roomType" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none">                                     
                    <?php foreach (['All', 'Single', 'Shared', 'Studio', 'Bachelor'] as $type): ?>                                         
                        <option value="<?php echo $type; ?>" <?php echo $roomFilter === $type ? 'selected' : ''; ?>><?php echo $type; ?></option>                                     
                    <?php endforeach; ?>                                 
                </select>                                 
                <div class="grid grid-cols-2 gap-2">                                     
                    <input type="number" name="minPrice" value="<?php echo htmlspecialchars($minPrice); ?>" placeholder="Min R" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">                                     
                    <input type="number" name="maxPrice" value="<?php echo htmlspecialchars($maxPrice); ?>" placeholder="Max R" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">                                 
                </div>                                 
                <button type="submit" class="w-full rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white hover:bg-blue-700">Apply Filters</button>                             
            </form>                         
        </aside>                         

        <section class="grid gap-5 md:grid-cols-2">                             
            <?php if (count($vacantListings) === 0): ?>
                <div class="md:col-span-2 rounded-3xl border border-slate-200 bg-white p-12 text-center text-slate-500">
                    <p class="text-lg font-semibold">No vacant accommodations found.</p>
                    <p class="text-sm mt-1">Make sure you have executed <a href="setup_db.php" class="text-blue-600 underline">setup_db.php</a> to populate initial properties.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($vacantListings as $listing): ?>                                     
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">                                         
                    <div>
                        <div class="aspect-video overflow-hidden rounded-2xl bg-slate-100 mb-4">
                            <img src="<?php echo htmlspecialchars($listing['imagePath']); ?>" class="h-full w-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80'" />
                        </div>                                         
                        <span class="text-xs uppercase tracking-wider text-slate-500"><?php echo htmlspecialchars($listing['roomType']); ?></span>                                             
                        <h3 class="text-lg font-semibold text-slate-900">
                            <a href="index.php?view=detail&id=<?php echo $listing['listingID']; ?>" class="hover:text-blue-600 transition"><?php echo htmlspecialchars($listing['title']); ?></a>
                        </h3>                                             
                        <p class="text-sm text-slate-600 mb-4"><?php echo htmlspecialchars($listing['address']); ?></p>                                         
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">                                                 
                        <p class="text-xl font-bold text-slate-900"><?php echo randFormat($listing['monthlyRent']); ?></p>                                                 
                        <form method="post">                                                     
                            <input type="hidden" name="action" value="apply">                                                     
                            <input type="hidden" name="listingID" value="<?php echo $listing['listingID']; ?>">                                                     
                            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Apply Now</button>                                                 
                        </form>                                             
                    </div>                                     
                </div>                                 
            <?php endforeach; ?>                             
        </section>                     
    </div>             
</div>