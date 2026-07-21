<x-layout title="Register">
    <x-card>
        <h1 class="text-2xl font-semibold mb-6">Create an account</h1>

        <x-validation-errors />

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="name">Name</x-input-label>
                <x-text-input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus />
            </div>

            <div>
                <x-input-label for="email">Email</x-input-label>
                <x-text-input id="email" name="email" type="email" value="{{ old('email') }}" required />
            </div>

            <div>
                <x-input-label for="password">Password</x-input-label>
                <x-text-input id="password" name="password" type="password" required />
            </div>

            <div>
                <x-input-label for="password_confirmation">Confirm Password</x-input-label>
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" required />
            </div>

            <x-primary-button class="w-full">Register</x-primary-button>
        </form>

        <p class="mt-6 text-sm text-slate-600">
            Already have an account?
            <a href="{{ route('login') }}" class="text-slate-900 font-medium">Log in</a>
        </p>
    </x-card>
</x-layout>
