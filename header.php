<!DOCTYPE html> 
<html lang="en"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusNest - Student Accommodation Management System</title>     
    <script src="https://cdn.tailwindcss.com"></script> 
</head> 
<body class="bg-slate-50 text-slate-900">     
    <div class="mx-auto max-w-7xl px-4 py-6">         
        <header class="mb-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">             
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">                 
                <div class="flex items-center gap-6">                     
                    <div>
                        <a href="index.php" class="text-2xl font-bold text-slate-900 tracking-tight">CampusNest</a>                     
                        <p class="text-xs text-slate-500">Student Accommodation Management System</p>                 
                    </div>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
                        <nav class="hidden md:flex gap-4 text-sm font-medium text-slate-600">
                            <a href="index.php?view=search" class="hover:text-blue-600 transition">Browse</a>
                            <a href="index.php?view=applications" class="hover:text-blue-600 transition">Applications</a>
                            <a href="index.php?view=leases" class="hover:text-blue-600 transition">Leases</a>
                            <a href="index.php?view=payments" class="hover:text-blue-600 transition">Payments</a>
                            <a href="index.php?view=maintenance" class="hover:text-blue-600 transition">Maintenance</a>
                        </nav>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'landlord'): ?>
                        <nav class="hidden md:flex gap-4 text-sm font-medium text-slate-600">
                            <a href="index.php" class="hover:text-blue-600 transition">Dashboard</a>
                            <a href="index.php?view=properties" class="hover:text-blue-600 transition">Units</a>
                            <a href="index.php?view=tenants" class="hover:text-blue-600 transition">Tenants</a>
                            <a href="index.php?view=analytics" class="hover:text-blue-600 transition">Earnings</a>
                            <a href="index.php?view=reports" class="hover:text-blue-600 transition">Reports</a>
                        </nav>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <nav class="hidden md:flex gap-4 text-sm font-medium text-slate-600">
                            <a href="index.php" class="hover:text-blue-600 transition">Dashboard</a>
                            <a href="index.php?view=users" class="hover:text-blue-600 transition">Users</a>
                            <a href="index.php?view=reports" class="hover:text-blue-600 transition">Financial Audit</a>
                            <a href="index.php?view=logs" class="hover:text-blue-600 transition">Activity Logs</a>
                        </nav>
                    <?php endif; ?>
                </div>                 
                <div class="flex flex-wrap gap-3 items-center">                     
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="index.php?view=notifications" class="px-3 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-2xl transition">Alerts</a>
                        <span class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-full">
                            <b><?php echo htmlspecialchars($_SESSION['name']); ?></b> (<?php echo ucfirst($_SESSION['role']); ?>)
                        </span>
                        <a href="index.php?view=profile" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-2xl transition">Settings</a>
                        <form method="post" class="inline">
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-2xl transition">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="index.php?view=login" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 rounded-2xl transition">Log In</a>
                        <a href="index.php?view=register" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-2xl transition">Register</a>
                    <?php endif; ?>
                </div>             
            </div>         
        </header>         
        <?php if (!empty($flash)): ?>             
            <div class="mb-6 rounded-2xl bg-emerald-50 px-6 py-4 text-emerald-900 shadow-sm border border-emerald-200">                 
                <?php echo htmlspecialchars($flash); ?>             
            </div>         
        <?php endif; ?>