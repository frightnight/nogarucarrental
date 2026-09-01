@props(['title', 'image' => null, 'status' => 'available', 'seats' => null, 'vehicleType' => null, 'transmission' => null, 'rentalType' => null, 'price' => null, 'priceLabel' => '24hrs', 'secondaryPrice' => null, 'href', 'actionLabel' => 'View Details', 'selected' => false])

@php
    $isAvailable = strtolower((string) $status) === 'available';
    $rentalLabel = $rentalType ? str($rentalType)->replace('_', ' ')->title() : 'Self-drive rental';
@endphp

@once
    <style>
        .vehicle-listing-card { border: 1px solid #e7ebf2; border-radius: 16px; box-shadow: 0 5px 18px rgba(16, 36, 62, .08); color: #152342; overflow: hidden; }
        .vehicle-listing-card.is-selected { border-color: #276ef1; box-shadow: 0 0 0 2px rgba(39, 110, 241, .15); }
        .vehicle-listing-card__media { aspect-ratio: 1.5 / 1; background: #edf2f8; position: relative; }
        .vehicle-listing-card__image { height: 100%; object-fit: cover; width: 100%; }
        .vehicle-listing-card__badge { border-radius: 999px; font-size: .75rem; font-weight: 700; padding: .42rem .62rem; position: absolute; top: .75rem; }
        .vehicle-listing-card__badge--availability { background: #e7f8ee; color: #087a3d; left: .75rem; }
        .vehicle-listing-card__badge--service { background: #e8eefb; color: #243a66; right: .75rem; }
        .vehicle-listing-card__body { padding: 1rem; }
        .vehicle-listing-card__title { font-size: 1.1rem; font-weight: 750; letter-spacing: -.02em; line-height: 1.25; margin: 0 0 .75rem; }
        .vehicle-listing-card__tags { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: .85rem; }
        .vehicle-listing-card__tag { background: #eefaf8; border-radius: 999px; color: #008a86; font-size: .76rem; padding: .35rem .6rem; }
        .vehicle-listing-card__tag--service { background: #fff3ec; color: #df4c11; }
        .vehicle-listing-card__specs { color: #637088; display: grid; font-size: .82rem; gap: .45rem 1rem; grid-template-columns: repeat(2, minmax(0, 1fr)); margin-bottom: 1rem; }
        .vehicle-listing-card__spec { align-items: center; display: flex; gap: .4rem; min-width: 0; }
        .vehicle-listing-card__footer { align-items: end; border-top: 1px solid #edf0f4; display: flex; gap: .75rem; justify-content: space-between; padding-top: .9rem; }
        .vehicle-listing-card__price { color: #152342; font-size: 1.2rem; font-weight: 800; letter-spacing: -.02em; line-height: 1.1; }
        .vehicle-listing-card__price-label { color: #8792a5; font-size: .78rem; font-weight: 600; }
        .vehicle-listing-card__secondary-price { color: #788399; font-size: .75rem; margin-top: .2rem; }
        .vehicle-listing-card__button { background: #162f65; border-color: #162f65; border-radius: .55rem; font-weight: 700; white-space: nowrap; }
        .vehicle-listing-card__button:hover, .vehicle-listing-card__button:focus { background: #102650; border-color: #102650; }
    </style>
@endonce

<article {{ $attributes->merge(['class' => 'vehicle-listing-card h-100'.($selected ? ' is-selected' : '')]) }}>
    <div class="vehicle-listing-card__media">
        @if($image)<img src="{{ $image }}" class="vehicle-listing-card__image" alt="{{ $title }}">@else<div class="h-100 d-flex align-items-center justify-content-center text-muted"><i class="ti ti-car" style="font-size: 3rem;"></i></div>@endif
        <span class="vehicle-listing-card__badge vehicle-listing-card__badge--availability">{{ $isAvailable ? 'Available' : str($status)->title() }}</span><span class="vehicle-listing-card__badge vehicle-listing-card__badge--service">Pickup</span>
    </div>
    <div class="vehicle-listing-card__body d-flex flex-column">
        <h3 class="vehicle-listing-card__title">{{ $title }}</h3>
        <div class="vehicle-listing-card__tags"><span class="vehicle-listing-card__tag">{{ $rentalLabel }}</span><span class="vehicle-listing-card__tag vehicle-listing-card__tag--service"><i class="ti ti-map-pin me-1"></i>Pickup &amp; Drop-off</span></div>
        <div class="vehicle-listing-card__specs">
            @if($seats)<span class="vehicle-listing-card__spec"><i class="ti ti-user"></i>{{ $seats }} Seats</span>@endif @if($vehicleType)<span class="vehicle-listing-card__spec"><i class="ti ti-briefcase"></i>{{ $vehicleType }}</span>@endif @if($transmission)<span class="vehicle-listing-card__spec"><i class="ti ti-settings"></i>{{ $transmission }}</span>@endif @if($rentalType)<span class="vehicle-listing-card__spec"><i class="ti ti-calendar"></i>{{ $rentalLabel }}</span>@endif
        </div>
        <div class="vehicle-listing-card__footer"><div><div class="vehicle-listing-card__price">{{ $price ?? 'On request' }}@if($price)<span class="vehicle-listing-card__price-label">/{{ $priceLabel }}</span>@endif</div>@if($secondaryPrice)<div class="vehicle-listing-card__secondary-price">{{ $secondaryPrice }}</div>@endif</div><a href="{{ $href }}" class="btn btn-primary vehicle-listing-card__button">{{ $actionLabel }}</a></div>
    </div>
</article>
