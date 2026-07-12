<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white shadow-sm rounded-xl ring-1 ring-slate-200 p-8">
        <h1 class="text-2xl font-semibold mb-6">Log in</h1>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-800">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="password">Password</label>
                <input id="password" name="password" type="password" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
            </div>

            <div class="flex items-center gap-3">
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox" name="remember" class="mr-2 rounded border-slate-300 text-slate-700 focus:ring-slate-500" />
                    Remember me
                </label>
            </div>

            <button type="submit" class="w-full rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-slate-800">Sign in</button>
        </form>

        <p class="mt-6 text-sm text-slate-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-slate-900 font-medium">Register</a>
        </p>
    </div>
</body>
</html>
