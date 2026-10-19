<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: index.php");
    exit;
}

$landlordID = $_SESSION['user_id'];
$listings = $pdo->prepare("SELECT * FROM Listing WHERE landlordID = ? ORDER BY dateListingCreated DESC");
$listings->execute([$landlordID]);
$myListings = $listings->fetchAll();
?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Landlord Portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Manage Portfolio Listings</h2>
            <p class="mt-1 text-sm text-slate-600">Update rental rates, availability status, or remove units.</p>
        </div>
        <a href="index.php" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Dashboard</a>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <?php foreach ($myListings as $listing): ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4 flex flex-col justify-between">
                <div>
                    <div class="aspect-video rounded-2xl overflow-hidden bg-slate-100 mb-4">
                        <img src="<?php echo htmlspecialchars($listing['imagePath']); ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs uppercase tracking-wider text-slate-500"><?php echo htmlspecialchars($listing['roomType']); ?></span>
                            <h3 class="font-bold text-slate-900 text-lg mt-0.5"><?php echo htmlspecialchars($listing['title']); ?></h3>
                            <p class="text-xs text-slate-600"><?php echo htmlspecialchars($listing['address']); ?></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $listing['availabilityStatus'] === 'Occupied' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800'; ?>">
                            <?php echo htmlspecialchars($listing['availabilityStatus']); ?>
                        </span>
                    </div>
                </div>

                <form method="post" class="space-y-3 pt-4 border-t border-slate-100">
                    <input type="hidden" name="action" value="update_listing">
                    <input type="hidden" name="listingID" value="<?php echo $listing['listingID']; ?>">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Monthly Rent (R)</label>
                            <input type="number" name="monthlyRent" value="<?php echo $listing['monthlyRent']; ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                            <select name="availabilityStatus" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                <option value="Vacant" <?php echo $listing['availabilityStatus'] === 'Vacant' ? 'selected' : ''; ?>>Vacant</option>
                                <option value="Occupied" <?php echo $listing['availabilityStatus'] === 'Occupied' ? 'selected' : ''; ?>>Occupied</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 rounded-xl bg-slate-900 py-2.5 text-xs font-semibold text-white hover:bg-slate-800 transition">Update Unit</button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>