<!DOCTYPE html> 
<html lang="en"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusNest - Student Accommodation Management System</title>     
    <script src="https://cdn.tailwindcss.com"></script> 
</head> 
<body class="bg-slate-50 text-slate-900">     
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">         
        <header class="mb-8 rounded-2xl border border-slate-200 bg-white/90 p-4 shadow-sm backdrop-blur-sm">             
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">                 
                <div class="flex items-center gap-4">                     
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-base font-bold text-white shadow-sm">
                        CN
                    </div>
                    <div>
                        <a href="index.php" class="text-xl font-bold tracking-tight text-slate-900">CampusNest</a>                     
                        <p class="text-[11px] uppercase tracking-[0.2em] text-slate-500">Student Accommodation</p>
                    </div>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'student'): ?>
                        <nav class="hidden items-center gap-1 rounded-full border border-slate-200 bg-slate-50 p-1 md:flex">
                            <a href="index.php?view=search" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Browse</a>
                            <a href="index.php?view=applications" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Applications</a>
                            <a href="index.php?view=leases" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Leases</a>
                            <a href="index.php?view=payments" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Payments</a>
                            <a href="index.php?view=maintenance" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Maintenance</a>
                        </nav>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'landlord'): ?>
                        <nav class="hidden items-center gap-1 rounded-full border border-slate-200 bg-slate-50 p-1 md:flex">
                            <a href="index.php" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Dashboard</a>
                            <a href="index.php?view=properties" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Units</a>
                            <a href="index.php?view=tenants" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Tenants</a>
                            <a href="index.php?view=analytics" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Earnings</a>
                            <a href="index.php?view=reports" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Reports</a>
                        </nav>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <nav class="hidden items-center gap-1 rounded-full border border-slate-200 bg-slate-50 p-1 md:flex">
                            <a href="index.php" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Dashboard</a>
                            <a href="index.php?view=users" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Users</a>
                            <a href="index.php?view=reports" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Audit</a>
                            <a href="index.php?view=logs" class="rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white hover:text-slate-900">Logs</a>
                        </nav>
                    <?php endif; ?>
                </div>                 
                <div class="flex flex-wrap items-center gap-3">                     
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="index.php?view=notifications" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Alerts</a>
                        <div class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white px-2.5 py-1.5 shadow-sm">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-700">
                                <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                            </div>
                            <div class="hidden text-left sm:block">
                                <p class="text-sm font-medium text-slate-900"><?php echo htmlspecialchars($_SESSION['name']); ?></p>
                                <p class="text-[10px] uppercase tracking-[0.16em] text-slate-500"><?php echo ucfirst($_SESSION['role']); ?></p>
                            </div>
                        </div>
                        <a href="index.php?view=profile" class="rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">Settings</a>
                        <form method="post" class="m-0 inline">
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="rounded-full border border-red-100 bg-red-50 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-100">Logout</button>
                        </form>
                    <?php else: ?>
                        <a href="index.php?view=login" class="rounded-full px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Log In</a>
                        <a href="index.php?view=register" class="rounded-full bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">Register</a>
                    <?php endif; ?>
                </div>             
            </div>         
        </header>         
        <?php if (!empty($flash)): ?>             
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm">                 
                <?php echo htmlspecialchars($flash); ?>             
            </div>         
        <?php endif; ?>