<div class="max-w-md mx-auto mt-12 bg-white p-8 rounded-3xl shadow-sm border border-slate-200">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-slate-900">Welcome Back</h2>
        <p class="text-slate-600 mt-2">Sign in to manage your CampusNest account[cite: 2].</p>
    </div>
    
    <form method="post" class="space-y-5">
        <input type="hidden" name="action" value="login">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-blue-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-blue-500 outline-none">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition">Log In</button>
    </form>
    <div class="mt-6 text-center text-sm text-slate-500">
        Don't have an account? <a href="?view=register" class="text-blue-600 font-semibold hover:underline">Register</a>
    </div>
</div>