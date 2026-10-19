<?php
//  User Account Management & Settings
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];
$userID = $_SESSION['user_id'];
$table = ucfirst($role); // Student, Landlord, or Admin
$idCol = $role . 'ID';
if ($role === 'admin') $idCol = 'adminID';

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM $table WHERE $idCol = ?");
$stmt->execute([$userID]);
$currentUserData = $stmt->fetch();
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm flex justify-between items-center">
        <div>
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Account Settings</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Manage Profile</h2>
            <p class="mt-1 text-sm text-slate-600">Update your personal information and credentials.</p>
        </div>
        <a href="?view=<?php echo $role === 'student' ? 'search' : 'dashboard'; ?>" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Back</a>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <form method="post" class="space-y-5">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">First Name</label>
                    <input type="text" name="firstName" value="<?php echo htmlspecialchars($currentUserData['firstName']); ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                    <input type="text" name="lastName" value="<?php echo htmlspecialchars($currentUserData['lastName']); ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($currentUserData['email']); ?>" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500">
            </div>

            <?php if ($role === 'student'): ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Student Number</label>
                    <input type="text" name="studentNumber" value="<?php echo htmlspecialchars($currentUserData['studentNumber'] ?? ''); ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500">
                </div>
            <?php elseif ($role === 'landlord'): ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Company Name</label>
                    <input type="text" name="companyName" value="<?php echo htmlspecialchars($currentUserData['companyName'] ?? ''); ?>" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500">
                </div>
            <?php endif; ?>

            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-sm font-semibold text-slate-900 mb-3">Change Password (Optional)</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                        <input type="password" name="newPassword" placeholder="Leave blank to keep current password" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition">Save Changes</button>
        </form>
    </div>
</div>