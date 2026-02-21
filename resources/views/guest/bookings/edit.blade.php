<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking - Hostly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="flex justify-between items-center px-10 py-6 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <a href="/" class="text-2xl font-bold tracking-tight text-indigo-600">Hostly</a>
        <div>
            <a href="{{ route('guest.dashboard') }}" class="mr-4 text-slate-600 font-semibold hover:text-indigo-600">Back to Dashboard</a>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-6 py-10">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">Edit Booking</h1>
            <p class="text-slate-500 mb-8">{{ $booking->property->title }}</p>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('guest.bookings.update', $booking) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Check-in Date</label>
                        <input type="date" name="check_in" value="{{ old('check_in', $booking->check_in->format('Y-m-d')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition-all" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Check-out Date</label>
                        <input type="date" name="check_out" value="{{ old('check_out', $booking->check_out->format('Y-m-d')) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 outline-none transition-all" required>
                    </div>
                </div>

                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl mb-8">
                    <p class="text-sm text-yellow-800 font-medium">⚠️ Note: Changing your booking dates will reset your booking status to 'Pending Approval' and recalculate the total price. The host must approve the new dates.</p>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('guest.dashboard') }}" class="px-6 py-3 rounded-xl font-semibold text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all">Cancel</a>
                    <button type="submit" class="px-6 py-3 rounded-xl font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">Submit Changes</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
