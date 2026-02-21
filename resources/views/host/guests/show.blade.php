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
                    <p class="text-slate-600 leading-relaxed">
                        This guest is registered on Hostly and ready to book properties. Detailed guest reviews and profiles will be coming soon!
                    </p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                    <a href="{{ route('host.dashboard') }}" class="px-6 py-3 rounded-xl font-semibold text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all">Back to Bookings</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
