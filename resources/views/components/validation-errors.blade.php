@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-4 rounded-md bg-red-50 border border-red-200 p-4 text-sm text-red-800']) }}>
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
