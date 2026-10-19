<?php
// Fetch a few featured verified listings for public display
$stmt = $pdo->query("SELECT * FROM Listing WHERE availabilityStatus = 'Vacant' AND isVerifiedByAdmin = 1 ORDER BY dateListingCreated DESC LIMIT 4");
$featuredListings = $stmt->fetchAll();
?>

<div class="space-y-12 max-w-6xl mx-auto py-6">
    <!-- Hero Section -->
    <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-blue-600 to-blue-800 p-10 text-white shadow-lg flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="space-y-4 max-w-xl">
            <span class="px-4 py-1.5 rounded-full bg-blue-500/50 text-xs font-semibold uppercase tracking-wider">CampusNest Platform</span>
            <h2 class="text-4xl font-bold tracking-tight">Find Your Ideal Student Accommodation Near Campus</h2>
            <p class="text-blue-100 text-sm leading-relaxed">Secure, verified student housing with seamless application tracking, direct landlord communication, and digital rent payments.</p>
            <div class="flex gap-3 pt-2">
                <a href="?view=login" class="px-6 py-3 rounded-2xl bg-white text-blue-900 font-semibold text-sm hover:bg-blue-50 transition">Log In</a>
                <a href="?view=register" class="px-6 py-3 rounded-2xl bg-blue-500 text-white font-semibold text-sm hover:bg-blue-400 transition border border-blue-400">Create Account</a>
            </div>
        </div>
        <div class="w-full md:w-80 aspect-video rounded-2xl overflow-hidden bg-blue-900 shadow-md">
            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80" alt="Student Housing" class="w-full h-full object-cover opacity-90">
        </div>
    </div>

    <!-- Featured Properties Grid -->
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Available Now</p>
                <h3 class="text-2xl font-semibold text-slate-900">Featured Verified Listings</h3>
            </div>
            <a href="?view=login" class="text-sm font-semibold text-blue-600 hover:underline">View All &rarr;</a>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($featuredListings as $listing): ?>
                <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-slate-100 mb-3">
                            <img src="<?php echo htmlspecialchars($listing['imagePath']); ?>" class="h-full w-full object-cover">
                        </div>
                        <span class="text-xs uppercase tracking-wider text-slate-500"><?php echo htmlspecialchars($listing['roomType']); ?></span>
                        <h4 class="font-semibold text-slate-900 mt-1"><?php echo htmlspecialchars($listing['title']); ?></h4>
                        <p class="text-xs text-slate-600 mt-0.5 truncate"><?php echo htmlspecialchars($listing['address']); ?></p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="font-bold text-slate-900"><?php echo randFormat($listing['monthlyRent']); ?></span>
                        <a href="?view=login" class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-600 text-xs font-semibold hover:bg-blue-100 transition">Apply</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>