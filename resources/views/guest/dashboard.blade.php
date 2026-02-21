<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Dashboard - Hostly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    @php use App\Models\Booking; @endphp
    <nav class="flex justify-between items-center px-10 py-6 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <a href="/" class="text-2xl font-bold tracking-tight text-indigo-600">Hostly</a>
        <div>
            <span class="mr-4 text-slate-600 font-semibold">Welcome, {{ auth()->user()->name }}</span>
            <a href="{{ route('guest.dashboard') }}" class="mr-2 px-6 py-2.5 rounded-full font-semibold bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-all">My Bookings</a>
            @if(auth()->user()->is_host)
                <a href="{{ route('host.dashboard') }}" class="mr-2 px-6 py-2.5 rounded-full font-semibold text-slate-600 hover:bg-slate-100 transition-all">Host Dashboard</a>
            @endif
            <form action="/logout" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-2.5 rounded-full font-semibold border border-slate-200 hover:bg-slate-50 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">My Bookings</h1>
                <p class="text-slate-500 mt-1">Manage your trips and reservations</p>
            </div>
            <a href="{{ route('properties.search') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                Explore Properties
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        {{-- Bookings Table --}}
        @if($bookings->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">Property</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">Dates</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">Total Price</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800">
                                        <a href="{{ route('properties.show', $booking->property) }}" class="hover:text-indigo-600">{{ $booking->property->title }}</a>
                                    </div>
                                    <div class="text-sm text-slate-500">{{ $booking->property->city }}, {{ $booking->property->country }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <div>{{ $booking->check_in->format('M d, Y') }} &rarr;</div>
                                    <div>{{ $booking->check_out->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-800 font-semibold">${{ number_format($booking->total_price, 2) }}</td>
                                <td class="px-6 py-4">
                                    @if($booking->status === Booking::STATUS_CONFIRMED)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Approved</span>
                                    @elseif($booking->status === Booking::STATUS_PENDING)
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-full">Pending Approval</span>
                                    @elseif($booking->status === Booking::STATUS_REJECTED)
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($booking->status !== Booking::STATUS_REJECTED && $booking->check_in > now())
                                        <a href="{{ route('guest.bookings.edit', $booking) }}" class="inline-block px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">Edit Dates</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
                <div class="text-5xl mb-4">✈️</div>
                <h3 class="text-xl font-semibold text-slate-800 mb-2">No bookings yet</h3>
                <p class="text-slate-500 mb-6">Start planning your next adventure</p>
                <a href="{{ route('properties.search') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all">
                    Explore Properties
                </a>
            </div>
        @endif
    </main>
</body>
</html>
