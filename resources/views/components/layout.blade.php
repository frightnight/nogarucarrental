@props(['title' => config('app.name', 'Laravel')])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 flex items-center justify-center p-6">
    {{ $slot }}
</body>
</html>
