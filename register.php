<div class="max-w-md mx-auto mt-12 bg-white p-8 rounded-3xl shadow-sm border border-slate-200">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-slate-900">Join CampusNest</h2>
        <p class="text-slate-600 mt-2">Create your account to manage accommodations[cite: 2].</p>
    </div>
    
    <form method="post" class="space-y-5">
        <input type="hidden" name="action" value="register">
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">I am a...</label>
            <select name="role" id="roleSelect" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-blue-500 outline-none" onchange="toggleRoleFields()">
                <option value="student">Student</option>
                <option value="landlord">Property Owner / Landlord</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">First Name</label>
                <input type="text" name="firstName" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                <input type="text" name="lastName" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-blue-500 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-blue-500 outline-none">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-blue-500 outline-none">
        </div>

        <div id="studentField">
            <label class="block text-sm font-medium text-slate-700 mb-1">Student Number</label>
            <input type="text" name="studentNumber" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-blue-500 outline-none">
        </div>

        <div id="landlordField" style="display: none;">
            <label class="block text-sm font-medium text-slate-700 mb-1">Company Name</label>
            <input type="text" name="companyName" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-blue-500 outline-none">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition">Register</button>
    </form>
    
    <div class="mt-6 text-center text-sm text-slate-500">
        Already have an account? <a href="?view=login" class="text-blue-600 font-semibold hover:underline">Log in</a>
    </div>
</div>

<script>
function toggleRoleFields() {
    const role = document.getElementById('roleSelect').value;
    document.getElementById('studentField').style.display = role === 'student' ? 'block' : 'none';
    document.getElementById('landlordField').style.display = role === 'landlord' ? 'block' : 'none';
}
</script>