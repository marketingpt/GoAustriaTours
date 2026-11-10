<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin login | {{ setting('brand_name') }}</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-brand-900 p-4">
    <main class="w-full max-w-sm rounded-2xl bg-ivory p-8 shadow-lift">
        <p class="text-center font-display text-3xl text-brand-800">{{ setting('brand_name') }}</p>
        <p class="eyebrow mb-6 mt-1 text-center">Admin</p>
        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1 block text-sm font-medium">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="input">
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-medium">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="input">
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" value="1" class="accent-brand-700"> Keep me signed in</label>
            @error('email')<p class="text-sm font-medium text-red-700" role="alert">{{ $message }}</p>@enderror
            @if (session('throttle'))<p class="text-sm text-red-700">{{ session('throttle') }}</p>@endif
            <button class="w-full rounded-full bg-cta-600 py-3 font-semibold text-white hover:bg-cta-700">Sign in</button>
        </form>
    </main>
</body>
</html>
