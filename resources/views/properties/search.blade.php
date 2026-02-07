<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Properties - Hostly</title>
    <meta name="description" content="Search and filter short-term rental properties. Find your perfect stay with Hostly.">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    {{-- Navigation --}}
    <nav class="flex justify-between items-center px-10 py-6 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <a href="/" class="text-2xl font-bold tracking-tight text-indigo-600">Hostly</a>
        <div class="flex items-center gap-4">
            <a href="{{ route('properties.search') }}" class="text-slate-600 hover:text-indigo-600 font-semibold transition-colors">Browse All</a>
            @auth
                <span class="text-slate-600 font-semibold">Welcome, {{ auth()->user()->name }}</span>
                @if(auth()->user()->role && auth()->user()->role->name === 'Host')
                    <a href="{{ route('host.dashboard') }}" class="px-6 py-2.5 rounded-full font-semibold bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-all">Dashboard</a>
                @endif
                <form action="/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-2.5 rounded-full font-semibold border border-slate-200 hover:bg-slate-50 transition-all">Logout</button>
                </form>
            @else
                <a href="/login" class="px-6 py-2.5 rounded-full font-semibold border border-slate-200 hover:bg-slate-50 transition-all">Login</a>
                <a href="/register" class="px-6 py-2.5 rounded-full font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">Join Now</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10">
        {{-- Page Header --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold mb-3">Find Your Perfect Stay</h1>
            <p class="text-slate-500 text-lg">Browse and filter properties to find exactly what you're looking for</p>
        </div>

        {{-- Search & Filters --}}
        <form method="GET" action="{{ route('properties.search') }}" class="bg-white rounded-2xl shadow-lg p-6 mb-10">
            {{-- Search Bar --}}
            <div class="mb-6">
                <div class="relative">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}"
                        placeholder="Search properties by name or description..."
                        class="w-full px-6 py-4 pr-14 bg-slate-50 border border-slate-200 rounded-xl text-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                    >
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-2xl hover:scale-110 transition-transform">
                        🔍
                    </button>
                </div>
            </div>

            {{-- Filter Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                {{-- City Filter --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">City</label>
                    <select name="city" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Country Filter --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">Country</label>
                    <select name="country" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="">All Countries</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ request('country') == $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Min Price --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">Min Price</label>
                    <input 
                        type="number" 
                        name="min_price" 
                        value="{{ request('min_price') }}"
                        placeholder="$0"
                        min="0"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                    >
                </div>

                {{-- Max Price --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">Max Price</label>
                    <input 
                        type="number" 
                        name="max_price" 
                        value="{{ request('max_price') }}"
                        placeholder="Any"
                        min="0"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                    >
                </div>

                {{-- Bedrooms --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">Bedrooms</label>
                    <input 
                        type="number" 
                        name="bedrooms" 
                        value="{{ request('bedrooms') }}"
                        placeholder="Any"
                        min="1"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                    >
                </div>

                {{-- Bathrooms --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">Bathrooms</label>
                    <input 
                        type="number" 
                        name="bathrooms" 
                        value="{{ request('bathrooms') }}"
                        placeholder="Any"
                        min="1"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                    >
                </div>

                {{-- Guests --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">Guests</label>
                    <input 
                        type="number" 
                        name="guests" 
                        value="{{ request('guests') }}"
                        placeholder="Any"
                        min="1"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                    >
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('properties.search') }}" class="px-6 py-3 rounded-xl font-semibold border border-slate-200 hover:bg-slate-50 transition-all">
                    Clear Filters
                </a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                    Apply Filters
                </button>
            </div>
        </form>

        {{-- Results Count --}}
        <div class="mb-6">
            <p class="text-slate-600">
                <span class="font-bold text-slate-900">{{ $properties->total() }}</span> 
                {{ $properties->total() === 1 ? 'property' : 'properties' }} found
            </p>
        </div>

        {{-- Results Grid --}}
        @if($properties->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">
                @foreach($properties as $property)
                    <a href="{{ route('properties.show', $property) }}" class="block group">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            {{-- Property Image --}}
                            <div class="relative h-48 overflow-hidden">
                                @if($property->primaryImage)
                                    <img 
                                        src="{{ $property->primaryImage->display_url }}" 
                                        alt="{{ $property->title }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    >
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                        <span class="text-white text-4xl">🏠</span>
                                    </div>
                                @endif
                                <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-indigo-600">
                                    ${{ number_format($property->price_per_night, 0) }}/night
                                </div>
                            </div>
                            
                            {{-- Property Details --}}
                            <div class="p-5">
                                <h3 class="font-bold text-lg mb-1 truncate">{{ $property->title }}</h3>
                                <p class="text-slate-500 text-sm mb-3">{{ $property->city }}, {{ $property->country }}</p>
                                <div class="flex items-center gap-4 text-sm text-slate-600">
                                    <span>🛏️ {{ $property->bedrooms }} bed</span>
                                    <span>🚿 {{ $property->bathrooms }} bath</span>
                                    <span>👥 {{ $property->max_guests }} guests</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center">
                {{ $properties->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-20">
                <div class="text-6xl mb-4">🔍</div>
                <h2 class="text-2xl font-bold mb-2">No properties found</h2>
                <p class="text-slate-500 mb-6">Try adjusting your filters or search terms</p>
                <a href="{{ route('properties.search') }}" class="inline-block px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all">
                    Clear All Filters
                </a>
            </div>
        @endif
    </main>

    {{-- Footer --}}
    <footer class="border-t border-slate-200 py-8 mt-10">
        <div class="max-w-7xl mx-auto px-6 text-center text-slate-500">
            <p>&copy; 2026 Hostly. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
