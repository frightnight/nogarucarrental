<x-layout title="Login">
    <x-card>
        <h1 class="text-2xl font-semibold mb-6">Log in</h1>

        <x-validation-errors />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email">Email</x-input-label>
                <x-text-input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus />
            </div>

            <div>
                <x-input-label for="password">Password</x-input-label>
                <x-text-input id="password" name="password" type="password" required />
            </div>

            <div class="flex items-center gap-3">
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox" name="remember" class="mr-2 rounded border-slate-300 text-slate-700 focus:ring-slate-500" />
                    Remember me
                </label>
            </div>

            <x-primary-button class="w-full">Sign in</x-primary-button>
        </form>

        <p class="mt-6 text-sm text-slate-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-slate-900 font-medium">Register</a>
        </p>
    </x-card>
</x-layout>
