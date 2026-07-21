@props(['maxWidth' => 'max-w-md'])

<div {{ $attributes->merge(['class' => "w-full {$maxWidth} bg-white shadow-sm rounded-xl ring-1 ring-slate-200 p-8"]) }}>
    {{ $slot }}
</div>
