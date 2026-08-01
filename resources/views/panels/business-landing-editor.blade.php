@extends('layouts.theme')

@section('title', 'Landing Page Editor | Nogaru Car Rental')
@section('page-title', 'Landing Page Editor')
@section('content')

                    <!-- Page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="{{ route('business.landing', ['businessSlug' => $business->slug]) }}/"
                                       target="_blank" class="btn btn-outline-primary btn-sm me-1">
                                        <i class="ti ti-eye me-1"></i> Preview Landing Page
                                    </a>
                                    <a href="{{ route('business.dashboard') }}" class="btn btn-outline-secondary btn-sm me-1">
                                        <i class="ti ti-arrow-left me-1"></i> Back to Dashboard
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-logout me-1"></i> Log out</button>
                                    </form>
                                </div>
                                <h4 class="page-title">Landing Page Editor</h4>
                                <p class="text-muted mb-0">Customize the content shown on your public landing page. Changes are visible immediately.</p>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ti ti-check me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('business.landing.content.update') }}">
                        @csrf

                        <!-- Hero Section -->
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0 text-white"><i class="ti ti-home me-2"></i>Home / Hero Section</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="hero_title" class="form-label fw-semibold">Hero Title</label>
                                    <input type="text" class="form-control @error('hero_title') is-invalid @enderror"
                                           id="hero_title" name="hero_title"
                                           value="{{ old('hero_title', $business->hero_title) }}"
                                           placeholder="e.g. Premium Car Rentals in {{ $business->city ?? 'Your City' }}">
                                    <div class="form-text">The main headline displayed on the hero banner.</div>
                                    @error('hero_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="hero_subtitle" class="form-label fw-semibold">Hero Subtitle</label>
                                    <textarea class="form-control @error('hero_subtitle') is-invalid @enderror"
                                              id="hero_subtitle" name="hero_subtitle" rows="2"
                                              placeholder="e.g. Discover our wide selection of well-maintained vehicles at competitive prices.">{{ old('hero_subtitle', $business->hero_subtitle) }}</textarea>
                                    <div class="form-text">A brief description shown below the title on the hero banner.</div>
                                    @error('hero_subtitle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- About Section -->
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0 text-white"><i class="ti ti-info-circle me-2"></i>About Section</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="about_title" class="form-label fw-semibold">About Title</label>
                                    <input type="text" class="form-control @error('about_title') is-invalid @enderror"
                                           id="about_title" name="about_title"
                                           value="{{ old('about_title', $business->about_title) }}"
                                           placeholder="e.g. Why Choose Us?">
                                    @error('about_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="about_content" class="form-label fw-semibold">About Content</label>
                                    <textarea class="form-control @error('about_content') is-invalid @enderror"
                                              id="about_content" name="about_content" rows="5"
                                              placeholder="Tell your business story...">{{ old('about_content', $business->about_content) }}</textarea>
                                    @error('about_content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">About Features (up to 3)</label>
                                    <div class="form-text mb-2">Each feature appears as a card with an icon, title, and description.</div>

                                    @php
                                        $features = old('about_features', $business->about_features ?? []);
                                        $featureCount = max(3, count($features));
                                    @endphp

                                    @for ($i = 0; $i < $featureCount; $i++)
                                        <div class="card bg-light mb-3">
                                            <div class="card-body">
                                                <h6 class="fw-bold mb-3">Feature {{ $i + 1 }}</h6>
                                                <div class="row g-2">
                                                    <div class="col-md-3">
                                                        <label class="form-label fs-12">Icon (Tabler icon name)</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="about_features[{{ $i }}][icon]"
                                                               value="{{ $features[$i]['icon'] ?? '' }}"
                                                               placeholder="e.g. shield-check">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fs-12">Title</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="about_features[{{ $i }}][title]"
                                                               value="{{ $features[$i]['title'] ?? '' }}"
                                                               placeholder="e.g. Reliable & Safe">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label fs-12">Description</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                               name="about_features[{{ $i }}][description]"
                                                               value="{{ $features[$i]['description'] ?? '' }}"
                                                               placeholder="e.g. All vehicles regularly maintained...">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Contact Section -->
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0 text-white"><i class="ti ti-phone me-2"></i>Contact Section</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="contact_email" class="form-label fw-semibold">Email</label>
                                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror"
                                               id="contact_email" name="contact_email"
                                               value="{{ old('contact_email', $business->contact_email) }}"
                                               placeholder="contact@business.com">
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="contact_phone" class="form-label fw-semibold">Phone</label>
                                        <input type="text" class="form-control @error('contact_phone') is-invalid @enderror"
                                               id="contact_phone" name="contact_phone"
                                               value="{{ old('contact_phone', $business->contact_phone) }}"
                                               placeholder="+1 (555) 123-4567">
                                        @error('contact_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="contact_address" class="form-label fw-semibold">Address</label>
                                        <input type="text" class="form-control @error('contact_address') is-invalid @enderror"
                                               id="contact_address" name="contact_address"
                                               value="{{ old('contact_address', $business->contact_address) }}"
                                               placeholder="123 Main St, City">
                                        @error('contact_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary fw-semibold px-4">
                                    <i class="ti ti-device-floppy me-1"></i> Save Changes
                                </button>
                                <a href="{{ route('business.landing', ['businessSlug' => $business->slug]) }}/"
                                   target="_blank" class="btn btn-outline-secondary fw-semibold px-4 ms-2">
                                    <i class="ti ti-eye me-1"></i> View Live Page
                                </a>
                            </div>
                        </div>

                    </form>

@endsection
