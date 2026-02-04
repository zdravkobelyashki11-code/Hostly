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
        <div>
            <span class="mr-4 text-slate-600 font-semibold">Welcome, {{ auth()->user()->name }}</span>
            @if(auth()->user()->role && auth()->user()->role->name === 'Host')
                <a href="{{ route('host.dashboard') }}" class="mr-2 px-6 py-2.5 rounded-full font-semibold bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-all">Dashboard</a>
            @endif
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
                    <h1 class="text-4xl font-bold mb-2">{{ $property->title }}</h1>
                    <p class="text-xl text-slate-500">{{ $property->city }}, {{ $property->country }}</p>
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

                    <button class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 transition-all shadow-lg">
                        Book Now
                    </button>
                </div>

                <div>
                    <h2 class="text-xl font-bold mb-3">About this property</h2>
                    <p class="text-slate-600 leading-relaxed">{{ $property->description }}</p>
                </div>

                <div>
                    <h2 class="text-xl font-bold mb-3">Location</h2>
                    <p class="text-slate-600">{{ $property->location }}</p>
                </div>

                @if($property->host)
                    <div class="bg-slate-100 rounded-xl p-4">
                        <h2 class="text-lg font-bold mb-1">Hosted by {{ $property->host->name }}</h2>
                        <p class="text-slate-500 text-sm">Contact: {{ $property->host->email }}</p>
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>