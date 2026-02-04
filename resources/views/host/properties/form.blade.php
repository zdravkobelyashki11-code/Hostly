<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($property) ? 'Edit' : 'Create' }} Property - Hostly</title>
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

    <main class="max-w-3xl mx-auto px-6 py-10">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('host.dashboard') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">← Back to Dashboard</a>
            <h1 class="text-3xl font-bold text-slate-800 mt-4">{{ isset($property) ? 'Edit Property' : 'Add New Property' }}</h1>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <form 
            action="{{ isset($property) ? route('host.properties.update', $property) : route('host.properties.store') }}" 
            method="POST"
            enctype="multipart/form-data"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8"
        >
            @csrf
            @if(isset($property))
                @method('PUT')
            @endif

            <div class="space-y-6">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">Property Title</label>
                    <input type="text" name="title" id="title" 
                        value="{{ old('title', $property->title ?? '') }}"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="e.g., Cozy Beach House"
                        required>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Describe your property..."
                        required>{{ old('description', $property->description ?? '') }}</textarea>
                </div>

                {{-- Price --}}
                <div>
                    <label for="price_per_night" class="block text-sm font-semibold text-slate-700 mb-2">Price per Night ($)</label>
                    <input type="number" name="price_per_night" id="price_per_night" step="0.01" min="0"
                        value="{{ old('price_per_night', $property->price_per_night ?? '') }}"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="150.00"
                        required>
                </div>

                {{-- Location Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="location" class="block text-sm font-semibold text-slate-700 mb-2">Address</label>
                        <input type="text" name="location" id="location"
                            value="{{ old('location', $property->location ?? '') }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="123 Main St"
                            required>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-semibold text-slate-700 mb-2">City</label>
                        <input type="text" name="city" id="city"
                            value="{{ old('city', $property->city ?? '') }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Miami"
                            required>
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-semibold text-slate-700 mb-2">Country</label>
                        <input type="text" name="country" id="country"
                            value="{{ old('country', $property->country ?? '') }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="USA"
                            required>
                    </div>
                </div>

                {{-- Property Details --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="max_guests" class="block text-sm font-semibold text-slate-700 mb-2">Max Guests</label>
                        <input type="number" name="max_guests" id="max_guests" min="1"
                            value="{{ old('max_guests', $property->max_guests ?? 1) }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required>
                    </div>
                    <div>
                        <label for="bedrooms" class="block text-sm font-semibold text-slate-700 mb-2">Bedrooms</label>
                        <input type="number" name="bedrooms" id="bedrooms" min="0"
                            value="{{ old('bedrooms', $property->bedrooms ?? 1) }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required>
                    </div>
                    <div>
                        <label for="bathrooms" class="block text-sm font-semibold text-slate-700 mb-2">Bathrooms</label>
                        <input type="number" name="bathrooms" id="bathrooms" min="0"
                            value="{{ old('bathrooms', $property->bathrooms ?? 1) }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required>
                    </div>
                </div>

                {{-- Images --}}
                <div>
                    <label for="images" class="block text-sm font-semibold text-slate-700 mb-2">Property Images</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-sm text-slate-500 mt-1">You can select multiple images. Max 5MB each.</p>
                </div>

                {{-- Active Status --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $property->is_active ?? true) ? 'checked' : '' }}
                        class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-semibold text-slate-700">Property is active and visible to guests</label>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button type="submit" class="w-full px-6 py-4 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                        {{ isset($property) ? 'Update Property' : 'Create Property' }}
                    </button>
                </div>
            </div>
        </form>
    </main>
</body>
</html>