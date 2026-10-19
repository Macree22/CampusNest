<?php
//Fallback Error Handler for Invalid Routes
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?view=home");
    exit;
}

$role = $_SESSION['role'] ?? 'student';
$dashboardView = $role === 'student' ? 'search' : 'dashboard';
?>

<div class="max-w-md mx-auto my-16 bg-white border border-slate-200 rounded-3xl p-8 text-center space-y-4 shadow-sm">
    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto font-bold text-xl">!</div>
    <h3 class="text-xl font-bold text-slate-900">Page Not Found</h3>
    <p class="text-sm text-slate-600">The requested view or resource does not exist or you do not have permission to access it.</p>
    <div>
        <a href="index.php?view=<?php echo $dashboardView; ?>" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition">Return to Dashboard</a>
    </div>
</div>