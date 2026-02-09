<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hostly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-100">
    {{-- Top Navigation --}}
    <nav class="bg-slate-900 text-white px-8 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <span class="text-xl font-bold text-indigo-400">Hostly</span>
            <span class="text-slate-400">|</span>
            <span class="text-sm text-slate-300">Admin Dashboard</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-slate-300">{{ auth()->user()->email }}</span>
            <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-700 rounded-lg text-sm hover:bg-slate-600 transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-6 py-8" x-data="{ activeTab: 'users' }">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Dashboard</h1>
            <p class="text-slate-500">Manage users and properties</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tab Navigation --}}
        <div class="flex gap-2 mb-6">
            <button 
                @click="activeTab = 'users'"
                :class="activeTab === 'users' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                class="px-6 py-3 rounded-xl font-semibold transition-all shadow-sm"
            >
                👥 Users ({{ $users->count() }})
            </button>
            <button 
                @click="activeTab = 'properties'"
                :class="activeTab === 'properties' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50'"
                class="px-6 py-3 rounded-xl font-semibold transition-all shadow-sm"
            >
                🏠 Properties ({{ $hosts->sum(fn($h) => $h->properties->count()) }})
            </button>
        </div>

        {{-- Users Tab --}}
        <div x-show="activeTab === 'users'" x-cloak>
            {{-- Add User Button --}}
            <div class="mb-4 flex justify-end">
                <a href="{{ route('admin.users.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                    + Add New User
                </a>
            </div>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Role</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">Registered</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $user->id }}</td>
                                <td class="px-6 py-4 font-medium text-slate-800">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if($user->role)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                            @if($user->role->name === 'Admin') bg-purple-100 text-purple-700
                                            @elseif($user->role->name === 'Host') bg-blue-100 text-blue-700
                                            @else bg-slate-100 text-slate-700 @endif">
                                            {{ $user->role->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-sm">No role</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="inline-block px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Properties Tab --}}
        <div x-show="activeTab === 'properties'" x-cloak>
            {{-- Add Property Button --}}
            <div class="mb-4 flex justify-end">
                <a href="{{ route('admin.properties.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                    + Add New Property
                </a>
            </div>
            @forelse($hosts as $host)
                <div class="bg-white rounded-2xl shadow-lg mb-6 overflow-hidden" x-data="{ open: true }">
                    {{-- Host Header --}}
                    <button 
                        @click="open = !open"
                        class="w-full px-6 py-4 flex justify-between items-center bg-slate-50 hover:bg-slate-100 transition-colors"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-indigo-600 font-bold">{{ substr($host->name, 0, 1) }}</span>
                            </div>
                            <div class="text-left">
                                <h3 class="font-bold text-slate-800">{{ $host->name }}</h3>
                                <p class="text-sm text-slate-500">{{ $host->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-semibold">
                                {{ $host->properties->count() }} {{ $host->properties->count() === 1 ? 'property' : 'properties' }}
                            </span>
                            <span x-text="open ? '▼' : '▶'" class="text-slate-400 text-sm"></span>
                        </div>
                    </button>

                    {{-- Properties List --}}
                    <div x-show="open" x-collapse>
                        @if($host->properties->count() > 0)
                            <div class="divide-y divide-slate-100">
                                @foreach($host->properties as $property)
                                    <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                                        {{-- Property Image --}}
                                        <div class="w-16 h-12 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($property->primaryImage)
                                                <img src="{{ $property->primaryImage->display_url }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                                    <span class="text-white text-xs">🏠</span>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Property Info --}}
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-slate-800 truncate">{{ $property->title }}</h4>
                                            <p class="text-sm text-slate-500">{{ $property->city }}, {{ $property->country }}</p>
                                        </div>

                                        {{-- Property Stats --}}
                                        <div class="flex items-center gap-6 text-sm text-slate-600">
                                            <span>${{ number_format($property->price_per_night, 0) }}/night</span>
                                            <span>🛏️ {{ $property->bedrooms }}</span>
                                            <span>👥 {{ $property->max_guests }}</span>
                                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $property->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                                {{ $property->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.properties.edit', $property) }}" class="px-3 py-1.5 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">Edit</a>
                                            <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this property?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="px-6 py-8 text-center text-slate-500">
                                No properties yet
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <div class="text-4xl mb-4">🏠</div>
                    <h3 class="text-xl font-bold text-slate-700 mb-2">No hosts yet</h3>
                    <p class="text-slate-500">Hosts will appear here once they register</p>
                </div>
            @endforelse
        </div>
    </main>
</body>
</html>
