@extends('layouts.theme')

@section('title', 'Public Landing Page | Nogaru')
@section('page-title', 'Public Landing Page')

@section('content')
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('admin.landing.update') }}">
        @csrf
        @method('PUT')
        @php($content = $landingPage->content)
        <div class="card"><div class="card-body"><h4 class="header-title">Hero</h4><div class="row g-3">
            @foreach (['hero_eyebrow' => 'Eyebrow', 'hero_title' => 'Title', 'hero_highlight' => 'Highlighted title'] as $field => $label)
                <div class="col-md-4"><label class="form-label" for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" value="{{ old($field, $content[$field] ?? '') }}">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            @endforeach
            <div class="col-12"><label class="form-label" for="hero_subtitle">Subtitle</label><textarea id="hero_subtitle" name="hero_subtitle" class="form-control" rows="2">{{ old('hero_subtitle', $content['hero_subtitle'] ?? '') }}</textarea></div>
        </div></div></div>
        <div class="card"><div class="card-body"><h4 class="header-title">Section headings</h4><div class="row g-3">
            @foreach (['company_heading' => 'Rental companies', 'destination_heading' => 'Destinations', 'vehicle_heading' => 'Featured vehicles', 'why_heading' => 'Why choose Nogaru', 'pricing_heading' => 'Business pricing', 'faq_heading' => 'FAQ', 'footer_text' => 'Footer message'] as $field => $label)
                <div class="col-md-6"><label class="form-label" for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" class="form-control" value="{{ old($field, $content[$field] ?? '') }}"></div>
            @endforeach
        </div></div></div>
        <div class="card"><div class="card-body"><h4 class="header-title">Customer testimonial</h4><div class="row g-3"><div class="col-12"><label class="form-label" for="testimonial_quote">Quote</label><textarea id="testimonial_quote" name="testimonial_quote" class="form-control" rows="3">{{ old('testimonial_quote', $content['testimonial']['quote'] ?? '') }}</textarea></div><div class="col-md-6"><label class="form-label" for="testimonial_name">Name</label><input id="testimonial_name" name="testimonial_name" class="form-control" value="{{ old('testimonial_name', $content['testimonial']['name'] ?? '') }}"></div><div class="col-md-6"><label class="form-label" for="testimonial_role">Role / location</label><input id="testimonial_role" name="testimonial_role" class="form-control" value="{{ old('testimonial_role', $content['testimonial']['role'] ?? '') }}"></div></div></div></div>
        <div class="card"><div class="card-body"><h4 class="header-title">Featured rental companies</h4><p class="text-muted">Select up to four companies shown on the public home page.</p><div class="row g-2">@foreach($businesses as $business)<div class="col-md-6"><label class="form-check"><input class="form-check-input" type="checkbox" name="featured_business_slugs[]" value="{{ $business->slug }}" @checked(in_array($business->slug, old('featured_business_slugs', $landingPage->featured_business_slugs ?? []), true))><span class="form-check-label">{{ $business->name }} <small class="text-muted">{{ $business->city }}</small></span></label></div>@endforeach</div></div></div>
        <div class="card"><div class="card-body"><h4 class="header-title">Featured vehicles</h4><p class="text-muted">Select up to three vehicles. If none are selected, the newest available vehicles are displayed.</p><div class="row g-2">@foreach($cars as $car)<div class="col-md-6"><label class="form-check"><input class="form-check-input" type="checkbox" name="featured_car_ids[]" value="{{ $car->id }}" @checked(in_array($car->id, old('featured_car_ids', $landingPage->featured_car_ids ?? []), true))><span class="form-check-label">{{ $car->car_model ?: $car->vehicle_type }} <small class="text-muted">· {{ $car->business->name }}</small></span></label></div>@endforeach</div></div></div>
        <div class="d-flex gap-2 mb-4"><button class="btn btn-primary" type="submit"><i class="ti ti-device-floppy me-1"></i>Save landing page</button><a href="{{ route('home') }}" class="btn btn-light" target="_blank">Preview</a></div>
    </form>
@endsection
