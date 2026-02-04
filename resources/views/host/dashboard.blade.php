<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Dashboard - Hostly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Times New Roman', serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="flex justify-between items-center px-10 py-6 bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <a href="/" class="text-2xl font-bold tracking-tight text-indigo-600">Hostly</a>
        <div>
            <span class="mr-4 text-slate-600 font-semibold">Welcome, {{ auth()->user()->name }}</span>
            <a href="{{ route('host.dashboard') }}" class="mr-2 px-6 py-2.5 rounded-full font-semibold bg-indigo-100 text-indigo-600 hover:bg-indigo-200 transition-all">Dashboard</a>
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
                <h1 class="text-3xl font-bold text-slate-800">My Properties</h1>
                <p class="text-slate-500 mt-1">Manage your rental listings</p>
            </div>
            <a href="{{ route('host.properties.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                + Add New Property
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        {{-- Properties Table --}}
        @if($properties->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">Property</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">Location</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">Price/Night</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">Status</th>
                            <th class="text-right px-6 py-4 text-sm font-semibold text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($properties as $property)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800">{{ $property->title }}</div>
                                    <div class="text-sm text-slate-500">{{ $property->bedrooms }} bed • {{ $property->bathrooms }} bath • {{ $property->max_guests }} guests</div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $property->city }}, {{ $property->country }}</td>
                                <td class="px-6 py-4 text-slate-800 font-semibold">${{ number_format($property->price_per_night, 2) }}</td>
                                <td class="px-6 py-4">
                                    @if($property->is_active)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Active</span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-sm font-medium rounded-full">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('host.properties.edit', $property) }}" class="inline-block px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">Edit</a>
                                    <form action="{{ route('host.properties.destroy', $property) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this property?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
                <div class="text-5xl mb-4">🏠</div>
                <h3 class="text-xl font-semibold text-slate-800 mb-2">No properties yet</h3>
                <p class="text-slate-500 mb-6">Start by adding your first rental property</p>
                <a href="{{ route('host.properties.create') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all">
                    + Add Your First Property
                </a>
            </div>
        @endif
    </main>
</body>
</html>