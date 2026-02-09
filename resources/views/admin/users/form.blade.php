<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($user) ? 'Edit' : 'Create' }} User - Hostly Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100">
    {{-- Top Navigation --}}
    <nav class="bg-slate-900 text-white px-8 py-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <span class="text-xl font-bold text-indigo-400">Hostly</span>
            <span class="text-slate-400">|</span>
            <span class="text-sm text-slate-300">Admin Dashboard</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-slate-300">{{ auth()->user()->email }}</span>
            <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-700 rounded-lg text-sm hover:bg-slate-600 transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-6 py-10">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">← Back to Dashboard</a>
            <h1 class="text-3xl font-bold text-slate-800 mt-4">{{ isset($user) ? 'Edit User' : 'Add New User' }}</h1>
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
            action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" 
            method="POST"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8"
        >
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="space-y-6">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">Name</label>
                    <input type="text" name="name" id="name" 
                        value="{{ old('name', $user->name ?? '') }}"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="John Doe"
                        required>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                    <input type="email" name="email" id="email" 
                        value="{{ old('email', $user->email ?? '') }}"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="john@example.com"
                        required>
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                        Password {{ isset($user) ? '(leave blank to keep current)' : '' }}
                    </label>
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="••••••••"
                        {{ isset($user) ? '' : 'required' }}>
                </div>

                {{-- Role --}}
                <div>
                    <label for="role_id" class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                    <select name="role_id" id="role_id"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required>
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button type="submit" class="w-full px-6 py-4 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">
                        {{ isset($user) ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </div>
        </form>
    </main>
</body>
</html>
