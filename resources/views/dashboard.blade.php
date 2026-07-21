<x-layout title="Dashboard">
    <x-card max-width="max-w-3xl">
        <div class="flex items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-semibold">Dashboard</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-primary-button>Log out</x-primary-button>
            </form>
        </div>

        <p class="text-slate-600">You are logged in as <strong>{{ auth()->user()->name }}</strong>.</p>
    </x-card>
</x-layout>
