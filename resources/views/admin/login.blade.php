<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hostly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Hostly</h1>
            <p class="text-indigo-300">Admin Portal</p>
        </div>

        {{-- Login Card --}}
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 shadow-2xl border border-white/20">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Admin Login</h2>

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-xl">
                    @foreach($errors->all() as $error)
                        <p class="text-red-300 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-xl">
                    <p class="text-green-300 text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.authenticate') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-indigo-200 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                        placeholder="admin@hostly.com"
                        required
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-indigo-200 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/30"
                >
                    Sign In
                </button>
            </form>
        </div>

        {{-- Back Link --}}
        <div class="text-center mt-6">
            <a href="/" class="text-indigo-300 hover:text-white transition-colors">← Back to website</a>
        </div>
    </div>
</body>
</html>
