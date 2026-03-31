<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->title }} - Hostly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    {{-- Navigation --}}
    <nav class="flex justify-between items-center px-10 py-6 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <a href="/" class="text-2xl font-bold tracking-tight text-indigo-600">Hostly</a>
        <div class="flex items-center gap-4">
            <a href="{{ route('properties.search') }}" class="text-slate-600 hover:text-indigo-600 font-semibold transition-colors">All listings</a>
            
            @auth
                @if(auth()->user()->role && auth()->user()->role->name === 'Guest')
                    <a href="{{ route('guest.dashboard') }}" class="mr-2 px-6 py-2.5 rounded-full font-semibold text-slate-600 hover:bg-slate-100 transition-all">My Bookings</a>
                @endif
                @if(auth()->user()->role && auth()->user()->role->name === 'Host')
                    <a href="{{ route('host.dashboard') }}" class="mr-2 px-6 py-2.5 rounded-full font-semibold bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-all">Dashboard</a>
                @endif
            @endauth
            <form action="/logout" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-2.5 rounded-full font-semibold border border-slate-200 hover:bg-slate-50 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-10">
        {{-- Back Button --}}
        <a href="/" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 mb-6">
            <span class="mr-2">←</span> Back to listings
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            {{-- Image Gallery --}}
            <div class="space-y-4">
                {{-- Primary Image --}}
                <div class="rounded-2xl overflow-hidden shadow-lg">
                    @if($property->primaryImage)
                        <img src="{{ $property->primaryImage->display_url }}" alt="{{ $property->title }}" class="w-full h-80 object-cover">
                    @else
                        <div class="w-full h-80 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                            <span class="text-white text-6xl">🏠</span>
                        </div>
                    @endif
                </div>
                
                {{-- Additional Images --}}
                @if($property->images->count() > 1)
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($property->images->skip(1)->take(3) as $image)
                            <div class="rounded-xl overflow-hidden">
                                <img src="{{ $image->display_url }}" alt="{{ $property->title }}" class="w-full h-24 object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Property Details --}}
            <div class="space-y-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-4xl font-bold">{{ $property->title }}</h1>
                        <span class="bg-indigo-100 text-indigo-800 text-sm font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                            ⭐ {{ $property->reviews->count() > 0 ? number_format($property->averageRating(), 1) : 'New' }}
                        </span>
                    </div>
                    <p class="text-xl text-slate-500">{{ $property->city }}, {{ $property->country }} • {{ $property->reviews->count() }} reviews</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg">
                    <div class="text-3xl font-bold text-indigo-600 mb-4">
                        ${{ number_format($property->price_per_night, 0) }} <span class="text-lg font-normal text-slate-500">/ night</span>
                    </div>
                    
                    <div class="flex gap-6 text-slate-600 mb-6">
                        <div class="text-center">
                            <div class="text-2xl">🛏️</div>
                            <div class="font-semibold">{{ $property->bedrooms }} beds</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl">🚿</div>
                            <div class="font-semibold">{{ $property->bathrooms }} baths</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl">👥</div>
                            <div class="font-semibold">{{ $property->max_guests }} guests</div>
                        </div>
                    </div>

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Booking Form --}}
                    @if(!$property->host || $property->host_id !== auth()->id())
                        <form action="{{ route('bookings.store', $property) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="check_in" class="block text-sm font-semibold text-slate-600 mb-1">Check-in</label>
                                    <input type="date" name="check_in" id="check_in" value="{{ old('check_in') }}" min="{{ date('Y-m-d') }}" required
                                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                                </div>
                                <div>
                                    <label for="check_out" class="block text-sm font-semibold text-slate-600 mb-1">Check-out</label>
                                    <input type="date" name="check_out" id="check_out" value="{{ old('check_out') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none">
                                </div>
                            </div>
                            <button type="submit" class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 transition-all shadow-lg">
                                Book Now
                            </button>
                        </form>
                    @else
                        <div class="w-full py-4 bg-slate-100 text-slate-500 rounded-xl font-semibold text-center">
                            This is your property
                        </div>
                    @endif
                </div>

                <div>
                    <h2 class="text-xl font-bold mb-3">About this property</h2>
                    <p class="text-slate-600 leading-relaxed">{{ $property->description }}</p>
                </div>

                <div>
                    <h2 class="text-xl font-bold mb-3">Location</h2>
                    <p class="text-slate-600">{{ $property->street_address }}, {{ $property->city }}, {{ $property->country }}</p>
                </div>

                @if($property->host)
                    <div class="bg-slate-100 rounded-xl p-6 shadow-sm border border-slate-200 mt-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 bg-white rounded-full p-1 shadow-sm">
                                <div class="w-full h-full bg-indigo-100 rounded-full flex items-center justify-center text-xl text-indigo-600 font-bold">
                                    {{ substr($property->host->name, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">Hosted by {{ $property->host->name }}</h2>
                                <p class="text-sm text-slate-500">Joined in {{ $property->host->created_at->format('F Y') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm text-slate-600 mb-4 border-y border-slate-200 py-3">
                            <div>⭐ {{ $property->host->receivedReviews()->count() > 0 ? number_format($property->host->averageRating(), 1) : 'New' }} ({{ $property->host->receivedReviews()->count() }} Reviews)</div>
                            <div>⏱ Response rate: 95%</div> <!-- Hardcoded -->
                            <div>
                                ✅ @if($property->host->email_verified_at) Email Verified @else Not Verified @endif
                            </div>
                        </div>

                        @if($property->host->profile?->bio)
                            <div class="text-slate-700 italic border-l-4 border-indigo-200 pl-3 py-1 mb-4">
                                "{{ $property->host->profile->bio }}"
                            </div>
                        @else
                            <div class="text-slate-500 italic mb-4">
                                This host hasn't added a bio yet.
                            </div>
                        @endif

                        <div class="text-sm">
                            <strong class="text-slate-700">Contact:</strong> {{ $property->host->email }}
                        </div>
                    </div>
                    </div>
                @endif
                
                {{-- Property Reviews --}}
                <div class="mt-10 pt-8 border-t border-slate-200">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">Guest Reviews <span class="bg-indigo-100 text-indigo-800 text-sm px-3 py-1 rounded-full">⭐ {{ $property->reviews->count() > 0 ? number_format($property->averageRating(), 1) : 'New' }}</span></h2>
                    @if($property->reviews->count() > 0)
                        @php
                            $avgAccuracy = collect($property->reviews)->avg('sub_ratings.accuracy');
                            $avgCleanliness = collect($property->reviews)->avg('sub_ratings.cleanliness');
                            $avgLocation = collect($property->reviews)->avg('sub_ratings.location');
                            $avgValue = collect($property->reviews)->avg('sub_ratings.value');
                        @endphp
                        
                        <div class="grid grid-cols-2 gap-x-12 gap-y-3 mb-8 pb-8 border-b border-slate-100">
                            <div class="flex justify-between items-center"><span class="text-slate-600">Accuracy</span><div class="flex items-center gap-2"><div class="w-32 bg-slate-200 h-1.5 rounded-full overflow-hidden"><div class="bg-slate-900 h-1.5 rounded-full" style="width: {{ ($avgAccuracy / 5) * 100 }}%"></div></div><strong class="text-slate-900 w-6 text-right">{{ number_format($avgAccuracy, 1) }}</strong></div></div>
                            <div class="flex justify-between items-center"><span class="text-slate-600">Cleanliness</span><div class="flex items-center gap-2"><div class="w-32 bg-slate-200 h-1.5 rounded-full overflow-hidden"><div class="bg-slate-900 h-1.5 rounded-full" style="width: {{ ($avgCleanliness / 5) * 100 }}%"></div></div><strong class="text-slate-900 w-6 text-right">{{ number_format($avgCleanliness, 1) }}</strong></div></div>
                            <div class="flex justify-between items-center"><span class="text-slate-600">Location</span><div class="flex items-center gap-2"><div class="w-32 bg-slate-200 h-1.5 rounded-full overflow-hidden"><div class="bg-slate-900 h-1.5 rounded-full" style="width: {{ ($avgLocation / 5) * 100 }}%"></div></div><strong class="text-slate-900 w-6 text-right">{{ number_format($avgLocation, 1) }}</strong></div></div>
                            <div class="flex justify-between items-center"><span class="text-slate-600">Value</span><div class="flex items-center gap-2"><div class="w-32 bg-slate-200 h-1.5 rounded-full overflow-hidden"><div class="bg-slate-900 h-1.5 rounded-full" style="width: {{ ($avgValue / 5) * 100 }}%"></div></div><strong class="text-slate-900 w-6 text-right">{{ number_format($avgValue, 1) }}</strong></div></div>
                        </div>

                        <div class="space-y-6">
                            @foreach($property->reviews as $review)
                                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                                    <div class="flex items-center gap-4 mb-3">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center font-bold text-indigo-600">
                                            {{ substr($review->reviewer->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $review->reviewer->name }}</div>
                                            <div class="text-sm text-slate-500">{{ $review->created_at->format('F Y') }} • ⭐ {{ $review->rating }}</div>
                                        </div>
                                    </div>
                                    <p class="text-slate-600">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-slate-500 italic">No reviews yet for this property.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>
</html>
