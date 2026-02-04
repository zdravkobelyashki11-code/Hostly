<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Hostly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md border border-slate-100">
        <h2 class="text-3xl font-bold text-slate-800 mb-2">Create Account</h2>
        <p class="text-slate-500 mb-8">Join the Hostly marketplace today.</p>

        <form action="/register" method="POST" class="space-y-5">
            @csrf 
            
            <!-- Global Error Message -->
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none @error('name') border-red-500 @enderror">
               
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none @error('email') border-red-500 @enderror">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">I want to be a:</label>
                <select name="role_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none bg-white @error('role_id') border-red-500 @enderror">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"> {{ $role->name }} </option>
                    @endforeach
                </select>
               
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none @error('password') border-red-500 @enderror">
               
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 mt-4">
                Sign Up
            </button>
        </form>
    </div>
</body>
</html>