<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Profile - {{ $guest->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="flex justify-between items-center px-10 py-6 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <a href="/" class="text-2xl font-bold tracking-tight text-indigo-600">Hostly</a>
        <div>
            <a href="{{ route('host.dashboard') }}" class="mr-4 text-slate-600 font-semibold hover:text-indigo-600">Back to Dashboard</a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-10">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="bg-indigo-600 h-32"></div>
            <div class="px-8 pb-8">
                <div class="relative flex justify-between items-end -mt-16 mb-6">
                    <div class="w-32 h-32 bg-white rounded-full p-2">
                        <div class="w-full h-full bg-slate-200 rounded-full flex items-center justify-center text-4xl text-slate-500 font-bold">
                            {{ substr($guest->name, 0, 1) }}
                        </div>
                    </div>
                </div>

                <h1 class="text-3xl font-bold text-slate-800 mb-2">{{ $guest->name }}</h1>
                <p class="text-slate-500 mb-8 flex flex-col gap-2">
                    <span class="flex items-center gap-2">✉️ {{ $guest->email }}</span>
                    <span class="flex items-center gap-2">📅 Member since {{ $guest->created_at->format('F Y') }}</span>
                </p>

                <div class="pt-6 border-t border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800 mb-4">About this guest</h2>
                    
                    <div class="space-y-4">
                        @if($guest->profile?->location)
                        <div>
                            <strong class="text-slate-700">Location:</strong>
                            <span class="text-slate-600">{{ $guest->profile->location }}</span>
                        </div>
                        @endif

                        <div>
                            <strong class="text-slate-700">Reputation:</strong>
                            <span class="text-slate-600">⭐ {{ $guest->receivedReviews()->count() > 0 ? number_format($guest->averageRating(), 1) : 'New' }} ({{ $guest->receivedReviews()->count() }} Reviews)</span>
                        </div>

                        <div>
                            <strong class="text-slate-700">Verification:</strong>
                            @if($guest->email_verified_at)
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Email Verified</span>
                            @else
                                <span class="text-slate-500 italic">Not Verified</span>
                            @endif
                        </div>

                        @if($guest->profile?->bio)
                        <div class="mt-4">
                            <strong class="text-slate-700 block mb-1">Bio:</strong>
                            <p class="text-slate-600 leading-relaxed italic border-l-4 border-indigo-200 pl-4 py-1">
                                "{{ $guest->profile->bio }}"
                            </p>
                        </div>
                        @else
                        <p class="text-slate-500 italic mt-4">This guest hasn't added a bio yet.</p>
                        @endif
                    </div>
                </div>

                {{-- Guest Reviews --}}
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800 mb-4">Reviews from Hosts</h2>
                    @if($guest->receivedReviews()->count() > 0)
                        <div class="space-y-4">
                            @foreach($guest->receivedReviews() as $review)
                                <div class="bg-gray-50 p-4 rounded-xl border border-slate-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="font-semibold text-slate-700">Property Host</div>
                                        <div class="text-sm text-slate-500">{{ $review->created_at->format('F Y') }} • ⭐ {{ $review->rating }}</div>
                                    </div>
                                    <p class="text-slate-600 text-sm">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-slate-500 italic text-sm">No reviews from hosts yet.</p>
                    @endif
                </div>
                
                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                    <a href="{{ route('host.dashboard') }}" class="px-6 py-3 rounded-xl font-semibold text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all">Back to Bookings</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
