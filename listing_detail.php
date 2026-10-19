<?php
$listingID = $_GET['id'] ?? null;
if (!$listingID) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT l.*, land.firstName, land.lastName, land.email, land.companyName 
    FROM Listing l 
    JOIN Landlord land ON l.landlordID = land.landlordID 
    WHERE l.listingID = ?
");
$stmt->execute([$listingID]);
$listing = $stmt->fetch();

if (!$listing) {
    echo "<div class='p-8 text-center text-slate-600'>Property listing not found.</div>";
    exit;
}

$imgStmt = $pdo->prepare("SELECT imageURL FROM ListingImage WHERE listingID = ?");
$imgStmt->execute([$listingID]);
$images = $imgStmt->fetchAll();
?>

<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <a href="?view=search" class="text-xs font-semibold text-blue-600 hover:underline">&larr; Back to Search</a>
            <h2 class="text-3xl font-bold text-slate-900 mt-1"><?php echo htmlspecialchars($listing['title']); ?></h2>
            <p class="text-sm text-slate-600 mt-1"><?php echo htmlspecialchars($listing['address']); ?></p>
        </div>
        <div class="text-right">
            <p class="text-xs uppercase tracking-wider text-slate-500">Monthly Rent</p>
            <p class="text-2xl font-bold text-slate-900"><?php echo randFormat($listing['monthlyRent']); ?></p>
        </div>
    </div>

    <!-- Image Gallery -->
    <div class="grid gap-4 md:grid-cols-2">
        <div class="aspect-[4/3] rounded-3xl overflow-hidden bg-slate-100 border border-slate-200">
            <img src="<?php echo htmlspecialchars($listing['imagePath']); ?>" class="w-full h-full object-cover">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <?php foreach ($images as $img): ?>
                <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-slate-100 border border-slate-200">
                    <img src="<?php echo htmlspecialchars($img['imageURL']); ?>" class="w-full h-full object-cover">
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="md:col-span-2 bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-lg font-bold text-slate-900">Property Overview</h3>
            <p class="text-sm text-slate-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($listing['description'] ?? 'No description provided.')); ?></p>
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 text-sm">
                <div><span class="text-slate-500">Room Type:</span> <b><?php echo htmlspecialchars($listing['roomType']); ?></b></div>
                <div><span class="text-slate-500">Status:</span> <b><?php echo htmlspecialchars($listing['availabilityStatus']); ?></b></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 h-fit">
            <h3 class="text-lg font-bold text-slate-900">Landlord Contact</h3>
            <div class="text-sm space-y-2 text-slate-600">
                <p><b><?php echo htmlspecialchars($listing['firstName'] . ' ' . $listing['lastName']); ?></b></p>
                <?php if (!empty($listing['companyName'])): ?>
                    <p class="text-xs text-slate-500"><?php echo htmlspecialchars($listing['companyName']); ?></p>
                <?php endif; ?>
                <p class="text-xs"><?php echo htmlspecialchars($listing['email']); ?></p>
            </div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
                <form method="post" class="pt-4 border-t border-slate-100">
                    <input type="hidden" name="action" value="apply">
                    <input type="hidden" name="listingID" value="<?php echo $listing['listingID']; ?>">
                    <input type="hidden" name="moveInDate" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition">Apply For Accommodation</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>