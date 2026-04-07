<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Hostly</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="flex justify-between items-center px-10 py-6 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <a href="/" class="text-2xl font-bold tracking-tight text-indigo-600">Hostly</a>
        <div class="flex items-center gap-4">
            <a href="{{ route('properties.search') }}" class="text-slate-600 hover:text-indigo-600 font-semibold transition-colors">All listings</a>
            <a href="{{ route('profile.edit') }}" class="px-6 py-2.5 rounded-full font-semibold text-slate-600 hover:bg-slate-100 transition-all">Profile</a>
            @if(auth()->user()->role && auth()->user()->role->name === 'Host')
                <a href="{{ route('host.dashboard') }}" class="px-6 py-2.5 rounded-full font-semibold text-slate-600 hover:bg-slate-100 transition-all">Dashboard</a>
            @else
                <a href="{{ route('guest.dashboard') }}" class="px-6 py-2.5 rounded-full font-semibold text-slate-600 hover:bg-slate-100 transition-all">My Bookings</a>
            @endif
            <form action="/logout" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-2.5 rounded-full font-semibold border border-slate-200 hover:bg-slate-50 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-10">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Messages</h1>
            <p class="text-slate-500 mt-1">
                Conversation with {{ $otherUser->name }} about {{ $booking->property->title }}
            </p>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                <div class="font-semibold text-slate-800">{{ $booking->property->title }}</div>
                <div class="text-sm text-slate-500">
                    {{ $booking->check_in->format('M d, Y') }} - {{ $booking->check_out->format('M d, Y') }}
                </div>
            </div>

            <div class="px-6 py-6 space-y-4 min-h-80">
                @if(session('success'))
                    <div class="p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @forelse($booking->messages as $message)
                    @php $isOwnMessage = (int) $message->sender_id === (int) auth()->id(); @endphp
                    <div class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-lg rounded-2xl px-4 py-3 {{ $isOwnMessage ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-800' }}">
                            <div class="text-sm font-semibold mb-1">
                                {{ $message->sender->name }}
                            </div>
                            <div class="whitespace-pre-wrap break-words">{{ $message->body }}</div>
                            <div class="mt-2 text-xs {{ $isOwnMessage ? 'text-indigo-100' : 'text-slate-500' }}">
                                {{ $message->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-slate-500">
                        No messages yet. Start the conversation below.
                    </div>
                @endforelse
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                <form action="{{ route('bookings.messages.store', $booking) }}" method="POST" class="space-y-3">
                    @csrf
                    <label for="body" class="block text-sm font-semibold text-slate-700">New message</label>
                    <textarea
                        name="body"
                        id="body"
                        rows="4"
                        maxlength="1000"
                        required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 outline-none"
                    >{{ old('body') }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
