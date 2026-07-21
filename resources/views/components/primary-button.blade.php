<button {{ $attributes->merge(['type' => 'submit', 'class' => 'rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-slate-800']) }}>
    {{ $slot }}
</button>
