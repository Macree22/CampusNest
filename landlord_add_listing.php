<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header("Location: index.php");
    exit;
}
?>

<div class="space-y-6 max-w-2xl mx-auto">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Landlord Portal</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Add New Accommodation</h2>
            <p class="mt-1 text-sm text-slate-600">Submit a new property listing for admin verification.</p>
        </div>
        <a href="index.php" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Back</a>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <form method="post" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="action" value="add_listing">
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Property Title</label>
                <input type="text" name="title" required placeholder="e.g. Modern Studio in Braamfontein" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Rent (R)</label>
                    <input type="number" name="monthlyRent" required placeholder="4500" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Room Type</label>
                    <select name="roomType" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500 text-sm">
                        <option value="Single">Single</option>
                        <option value="Shared">Shared</option>
                        <option value="Studio">Studio</option>
                        <option value="Bachelor">Bachelor</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Street Address & Suburb</label>
                <input type="text" name="address" required placeholder="15 Ameshoff Street, Braamfontein" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Property Description & Amenities</label>
                <textarea name="description" rows="3" required placeholder="Fully furnished with high-speed Wi-Fi, security, and study desk..." class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500 text-sm"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Featured Cover Image</label>
                <input type="file" name="listingImage" accept="image/*" required class="w-full p-2 border border-slate-200 rounded-xl text-sm bg-slate-50">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition text-sm">Submit Listing for Verification</button>
        </form>
    </div>
</div>