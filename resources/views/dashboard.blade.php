<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 flex items-center justify-center p-6">
    <div class="w-full max-w-3xl bg-white shadow-sm rounded-xl ring-1 ring-slate-200 p-8">
        <div class="flex items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-semibold">Dashboard</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-slate-800">Log out</button>
            </form>
        </div>

        <p class="text-slate-600">You are logged in as <strong>{{ auth()->user()->name }}</strong>.</p>
    </div>
</body>
</html>
