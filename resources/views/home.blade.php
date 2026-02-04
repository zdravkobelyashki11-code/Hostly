<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostly - Short Term Rentals</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="flex justify-between items-center px-10 py-6 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="text-2xl font-bold tracking-tight text-indigo-600">Hostly</div>
        <div>
            @auth
                
                <span class="mr-4 text-slate-600 font-semibold">Welcome, {{ auth()->user()->name }}</span>
                @if(auth()->user()->role && auth()->user()->role->name === 'Host')
                    <a href="{{ route('host.dashboard') }}" class="mr-2 px-6 py-2.5 rounded-full font-semibold bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-all">Dashboard</a>
                @endif
                <form action="/logout" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-2.5 rounded-full font-semibold border border-slate-200 hover:bg-slate-50 transition-all">Logout</button>
                </form>
            @else
                <a href="/login" class="px-6 py-2.5 rounded-full font-semibold border border-slate-200 hover:bg-slate-50 transition-all">Login</a>
                <a href="/register" class="ml-4 px-6 py-2.5 rounded-full font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">Join Now</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-20 text-center">
        <h1 class="text-6xl font-extrabold tracking-tight mb-6">
            Manage your <span class="text-indigo-600">Short-Term Rentals</span> <br>with confidence.
        </h1>
        <p class="text-xl text-slate-500 mb-10 max-w-2xl mx-auto">
            The all-in-one information system for Hosts and Guests. Handle bookings, prevent conflicts, and scale your rental business.
        </p>
        <div class="flex justify-center gap-4">
            <button class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200">Get Started</button>
            <button class="px-8 py-4 bg-white border border-slate-200 text-slate-700 rounded-2xl font-bold text-lg hover:bg-slate-50 transition-all">View Listings</button>
        </div>
    </main>


        {{-- Property Carousel Section --}}
    <section class="max-w-7xl mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-center mb-2">Featured Properties</h2>
        <p class="text-slate-500 text-center mb-10">Discover our handpicked selection of amazing stays</p>
        
        {{-- Carousel Container --}}
        <div class="relative">
            {{-- Carousel Track --}}
            <div id="carousel" class="flex gap-6 overflow-x-auto scroll-smooth pb-4 snap-x snap-mandatory" style="scrollbar-width: none; -ms-overflow-style: none;">
                @forelse($properties as $property)
                    <div class="flex-shrink-0 w-80 snap-start">
                        <a href="{{ route('properties.show', $property) }}" class="block">

                        <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300 group">
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
                    </div>
                @empty
                    <div class="w-full text-center py-10 text-slate-500">
                        No properties available yet.
                    </div>
                @endforelse
            </div>
            
            {{-- Navigation Arrows --}}
            <button onclick="scrollCarousel(-1)" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-slate-50 transition-colors z-10">
                <span class="text-xl">←</span>
            </button>
            <button onclick="scrollCarousel(1)" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-slate-50 transition-colors z-10">
                <span class="text-xl">→</span>
            </button>
        </div>
    </section>

    <script>
        function scrollCarousel(direction) {
            const carousel = document.getElementById('carousel');
            const scrollAmount = 340; // card width + gap
            carousel.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }
    </script>
</body>
</html>