<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Hostly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="flex justify-between items-center px-10 py-6 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <a href="/" class="text-2xl font-bold tracking-tight text-indigo-600">Hostly</a>
        <div class="flex items-center gap-4">
            <a href="{{ route('properties.search') }}" class="text-slate-600 hover:text-indigo-600 font-semibold transition-colors">All listings</a>
            
            @auth
                @if(auth()->user()->role && auth()->user()->role->name === 'Guest')
                    <a href="{{ route('guest.dashboard') }}" class="mr-2 px-6 py-2.5 rounded-full font-semibold text-slate-600 hover:bg-slate-100 transition-all">My Bookings</a>
                @endif
                @if(auth()->user()->role && auth()->user()->role->name === 'Host')
                    <a href="{{ route('host.dashboard') }}" class="mr-2 px-6 py-2.5 rounded-full font-semibold text-slate-600 hover:bg-slate-100 transition-all">Host Dashboard</a>
                @endif
            @endauth
            
            <form action="/logout" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-2.5 rounded-full font-semibold border border-slate-200 hover:bg-slate-50 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold text-slate-800 mb-8">Edit Profile</h1>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <h2 class="text-xl font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Public Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="bio" class="block text-sm font-semibold text-slate-700 mb-1">About Me (Bio)</label>
                                <textarea id="bio" name="bio" rows="4" class="w-full px-4 py-2 border border-slate-200 rounded-xl text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition-all">{{ old('bio', $user->profile?->bio) }}</textarea>
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-semibold text-slate-700 mb-1">Location (City, Country)</label>
                                <input type="text" id="location" name="location" value="{{ old('location', $user->profile?->location) }}" class="w-full px-4 py-2 border border-slate-200 rounded-xl text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition-all">
                            </div>

                            <div>
                                <label for="languages" class="block text-sm font-semibold text-slate-700 mb-1">Languages Spoken</label>
                                <input type="text" id="languages" name="languages[]" value="{{ old('languages') ? implode(', ', old('languages')) : ($user->profile?->languages ? implode(', ', $user->profile->languages) : '') }}" placeholder="e.g. English, Spanish" class="w-full px-4 py-2 border border-slate-200 rounded-xl text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition-all">
                                <p class="text-xs text-slate-500 mt-1">Separate languages with commas</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Private Information</h2>

                        <div class="space-y-4">
                            <div>
                                <label for="phone_number" class="block text-sm font-semibold text-slate-700 mb-1">Phone Number</label>
                                <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->profile?->phone_number) }}" class="w-full px-4 py-2 border border-slate-200 rounded-xl text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition-all">
                            </div>

                            @if($user->role->name === 'host')
                            <div>
                                <label for="address" class="block text-sm font-semibold text-slate-700 mb-1">Permanent Address</label>
                                <textarea id="address" name="address" rows="2" class="w-full px-4 py-2 border border-slate-200 rounded-xl text-slate-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none transition-all">{{ old('address', $user->profile?->address) }}</textarea>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
