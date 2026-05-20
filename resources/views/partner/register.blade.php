@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .register-hero {
        background: linear-gradient(135deg, var(--dark-charcoal), var(--dark-secondary));
        padding: 60px 0;
    }

    .register-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        padding: 40px;
        margin-top: -40px;
        margin-bottom: 60px;
    }

    .form-label {
        font-weight: 600;
        color: var(--dark-charcoal);
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 2px solid var(--off-white);
        border-radius: var(--radius-md);
        padding: 12px 16px;
        transition: var(--transition-fast);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
    }

    .package-summary {
        background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(255, 107, 53, 0.05));
        border-radius: var(--radius-md);
        padding: 24px;
        margin-bottom: 24px;
        border-left: 4px solid var(--primary-orange);
    }

    .package-summary h5 {
        font-family: 'Playfair Display', serif;
        color: var(--dark-charcoal);
        margin-bottom: 12px;
    }

    .package-price {
        font-size: 28px;
        font-weight: 700;
        color: var(--primary-orange);
    }

    .package-price span {
        font-size: 14px;
        font-weight: 400;
        color: var(--muted-gray);
    }

    .info-icon {
        color: var(--primary-orange);
        cursor: help;
    }

    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: 700;
        color: var(--dark-charcoal);
    }
</style>
@endpush

@section('content')
<section class="register-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="text-white">Restaurant Partner Registration</h1>
                <p class="text-white-50">Complete the form below to get started</p>
            </div>
        </div>
    </div>
</section>

<div class="container-fluid-custom">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="register-card">
                <div class="package-summary">
                    <h5><i class="bi bi-box-seam me-2"></i>Selected Package</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $package->name }}</strong>
                            <p class="mb-0 text-muted small">{{ $package->description }}</p>
                        </div>
                        <div class="package-price">
                            ${{ number_format($package->price, 2) }}<span>/{{ $package->billing_cycle }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('partner.store') }}">
                    @csrf
                    <input type="hidden" name="package_id" value="{{ $package->id }}">

                    <h5 class="mb-4 mt-2"><i class="bi bi-person-badge me-2"></i>Owner Information</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="owner_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('owner_name') is-invalid @enderror"
                                   id="owner_name" name="owner_name" value="{{ old('owner_name') }}"
                                   placeholder="e.g. John Doe" required>
                            @error('owner_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="national_id" class="form-label">National ID *</label>
                            <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                   id="national_id" name="national_id" value="{{ old('national_id') }}"
                                   placeholder="e.g. 1234567890" required>
                            @error('national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}"
                                   placeholder="e.g. john@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone') }}"
                                   placeholder="e.g. +1 (555) 123-4567" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" placeholder="Min. 8 characters" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation"
                                   placeholder="Re-enter your password" required>
                        </div>
                    </div>

                    <h5 class="mb-4 mt-4"><i class="bi bi-shop-window me-2"></i>Restaurant Information</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="restaurant_name" class="form-label">Restaurant Name *</label>
                            <input type="text" class="form-control @error('restaurant_name') is-invalid @enderror"
                                   id="restaurant_name" name="restaurant_name" value="{{ old('restaurant_name') }}"
                                   placeholder="e.g. The Italian Kitchen" required>
                            @error('restaurant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="restaurant_address" class="form-label">Restaurant Address *</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('restaurant_address') is-invalid @enderror"
                                       id="restaurant_address" name="restaurant_address" value="{{ old('restaurant_address') }}"
                                       placeholder="e.g. 123 Main Street, City" required>
                                <button type="button" class="btn btn-primary-custom" onclick="searchAddress()" title="Verify address">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <div id="addressStatus" class="mt-1 small"></div>
                            @error('restaurant_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', '0') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', '0') }}">

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Set Location on Map
                            <i class="bi bi-info-circle info-icon" data-bs-toggle="tooltip" title="Click on the map or search above to set your restaurant location"></i>
                        </label>
                        <div id="map" style="height: 300px; border-radius: var(--radius-md); border: 2px solid var(--light-gray);"></div>
                        <div id="locationStatus" class="mt-2 small"></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="commercial_registration_number" class="form-label">
                                Commercial Registration Number
                                <i class="bi bi-info-circle info-icon" data-bs-toggle="tooltip" title="Optional - Required for VAT purposes"></i>
                            </label>
                            <input type="text" class="form-control @error('commercial_registration_number') is-invalid @enderror"
                                   id="commercial_registration_number" name="commercial_registration_number"
                                   value="{{ old('commercial_registration_number') }}"
                                   placeholder="e.g. CR-2024-12345">
                            @error('commercial_registration_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tax_id" class="form-label">
                                Tax ID
                                <i class="bi bi-info-circle info-icon" data-bs-toggle="tooltip" title="Optional - For tax invoices"></i>
                            </label>
                            <input type="text" class="form-control @error('tax_id') is-invalid @enderror"
                                   id="tax_id" name="tax_id" value="{{ old('tax_id') }}"
                                   placeholder="e.g. TAX-1234567">
                            @error('tax_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary-custom btn-lg">
                            <i class="bi bi-credit-card me-2"></i>Proceed to Payment
                        </button>
                        <a href="{{ route('partner.pricing') }}" class="btn btn-outline-secondary">
                            Back to Pricing
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el)
    })
    initMap()
})

let map, marker
let userLat = 30.0444
let userLng = 31.2357

function initMap() {
    map = L.map('map').setView([userLat, userLng], 14)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map)
    marker = L.marker([userLat, userLng], { draggable: true }).addTo(map)

    map.on('click', function (e) { setLocation(e.latlng.lat, e.latlng.lng) })
    marker.on('dragend', function (e) {
        var pos = e.target.getLatLng()
        setLocation(pos.lat, pos.lng)
    })
}

function setLocation(lat, lng) {
    userLat = lat
    userLng = lng
    marker.setLatLng([lat, lng])
    document.getElementById('latitude').value = lat.toFixed(6)
    document.getElementById('longitude').value = lng.toFixed(6)
    updateLocationStatus(true, 'Location set: ' + lat.toFixed(4) + ', ' + lng.toFixed(4))
    reverseGeocode(lat, lng)
}

function updateLocationStatus(valid, message) {
    var el = document.getElementById('locationStatus')
    el.innerHTML = valid
        ? '<span class="text-success"><i class="bi bi-check-circle me-1"></i>' + message + '</span>'
        : '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>' + message + '</span>'
}

function reverseGeocode(lat, lng) {
    var url = "{{ route('geocode.reverse') }}?lat=" + lat + "&lng=" + lng
    fetch(url)
        .then(function (r) { return r.json() })
        .then(function (data) {
            if (data && data.formatted_address) {
                var addr = document.getElementById('restaurant_address')
                if (!addr.value || addr.dataset.autoFilled !== 'true') {
                    addr.value = data.formatted_address
                    addr.dataset.autoFilled = 'true'
                }
                updateLocationStatus(true, 'Verified: ' + data.formatted_address.substring(0, 80) + '...')
            } else {
                updateLocationStatus(true, 'Location set (address lookup unavailable)')
            }
        })
        .catch(function () {
            updateLocationStatus(true, 'Location set')
        })
}

function searchAddress() {
    var query = document.getElementById('restaurant_address').value
    if (!query) {
        updateLocationStatus(false, 'Please enter an address first')
        return
    }

    var statusEl = document.getElementById('addressStatus')
    statusEl.innerHTML = '<span class="text-muted"><i class="bi bi-hourglass me-1"></i>Verifying address...</span>'

    var url = "{{ route('geocode.search') }}?q=" + encodeURIComponent(query)
    fetch(url)
        .then(function (r) { return r.json() })
        .then(function (data) {
            if (data && data.length > 0) {
                var result = data[0]
                var lat = parseFloat(result.latitude)
                var lng = parseFloat(result.longitude)
                setLocation(lat, lng)
                statusEl.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Address verified</span>'
                map.setView([lat, lng], 16)
                document.getElementById('restaurant_address').value = result.address || result.name
                document.getElementById('restaurant_address').dataset.autoFilled = 'true'
            } else {
                statusEl.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Address not found. Try a different address or click on the map.</span>'
            }
        })
        .catch(function () {
            statusEl.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Verification failed. Please try again.</span>'
        })
}

function nominatimUrl(path, params) {
    params.email = NOMINATIM_EMAIL
    return 'https://nominatim.openstreetmap.org' + path + '?' + Object.keys(params).map(function (k) {
        return encodeURIComponent(k) + '=' + encodeURIComponent(params[k])
    }).join('&')
}

function reverseGeocode(lat, lng) {
    var url = nominatimUrl('/reverse', { format: 'json', lat: lat, lon: lng, addressdetails: 1, 'accept-language': 'en' })
    fetch(url)
        .then(function (r) { return r.json() })
        .then(function (data) {
            if (data && data.error) {
                updateLocationStatus(true, 'Location set (address lookup unavailable)')
                return
            }
            if (data && data.display_name) {
                var addr = document.getElementById('restaurant_address')
                if (!addr.value || addr.dataset.autoFilled !== 'true') {
                    addr.value = data.display_name
                    addr.dataset.autoFilled = 'true'
                }
                updateLocationStatus(true, 'Verified: ' + data.display_name.substring(0, 80) + '...')
            } else {
                updateLocationStatus(true, 'Location set (address lookup unavailable)')
            }
        })
        .catch(function () {
            updateLocationStatus(true, 'Location set')
        })
}

function searchAddress() {
    var query = document.getElementById('restaurant_address').value
    if (!query) {
        updateLocationStatus(false, 'Please enter an address first')
        return
    }

    var statusEl = document.getElementById('addressStatus')
    statusEl.innerHTML = '<span class="text-muted"><i class="bi bi-hourglass me-1"></i>Verifying address...</span>'

    var url = nominatimUrl('/search', { format: 'json', q: query, limit: 5, 'accept-language': 'en' })
    fetch(url)
        .then(function (r) { return r.json() })
        .then(function (data) {
            if (data && data.error) {
                statusEl.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>' + data.error + '</span>'
                return
            }
            if (data && data.length > 0) {
                var result = data[0]
                var lat = parseFloat(result.lat)
                var lng = parseFloat(result.lon)
                setLocation(lat, lng)
                statusEl.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Address verified</span>'
                map.setView([lat, lng], 16)
                document.getElementById('restaurant_address').value = result.display_name
                document.getElementById('restaurant_address').dataset.autoFilled = 'true'
            } else {
                statusEl.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Address not found. Try a different address or click on the map.</span>'
            }
        })
        .catch(function () {
            statusEl.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Verification failed. Please try again.</span>'
        })
}

document.getElementById('restaurant_address')?.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault()
        searchAddress()
    }
})

document.querySelector('form')?.addEventListener('submit', function (e) {
    var lat = parseFloat(document.getElementById('latitude').value)
    var lng = parseFloat(document.getElementById('longitude').value)

    if (lat === 0 && lng === 0) {
        if (navigator.geolocation) {
            e.preventDefault()
            updateLocationStatus(false, 'Getting your location...')
            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    setLocation(pos.coords.latitude, pos.coords.longitude)
                    e.target.submit()
                },
                function () {
                    updateLocationStatus(false, 'Please set your restaurant location on the map before submitting.')
                }
            )
            return
        }
        e.preventDefault()
        updateLocationStatus(false, 'Please set your restaurant location on the map before submitting.')
        return
    }

    var error = verifyLocation(lat, lng)
    if (error) {
        e.preventDefault()
        updateLocationStatus(false, error)
        document.getElementById('locationStatus').scrollIntoView({ behavior: 'smooth', block: 'center' })
    }
})
</script>
@endpush
@endsection