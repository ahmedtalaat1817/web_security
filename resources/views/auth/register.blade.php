@extends('layouts.app')
@section('title', 'Register')

@section('styles')
<style>
    .auth-page {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-tertiary) 100%);
        padding: 40px 20px;
        position: relative;
        overflow: hidden;
    }

    [data-theme="dark"] .auth-page {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
    }

    .auth-page::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, transparent 50%);
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .auth-card {
        background: var(--bg-secondary);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border-default);
        box-shadow: var(--shadow-xl);
        overflow: hidden;
        position: relative;
        z-index: 2;
        max-width: 800px;
        margin: 0 auto;
    }

    .auth-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 40px;
        text-align: center;
    }

    .auth-logo {
        width: 72px;
        height: 72px;
        background: white;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 32px;
        color: var(--primary);
        box-shadow: var(--shadow-lg);
    }

    .auth-title {
        font-family: var(--font-display);
        font-size: 26px;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .auth-subtitle {
        color: rgba(255, 255, 255, 0.85);
        margin-top: 6px;
        font-size: var(--text-sm);
    }

    .auth-body {
        padding: 40px;
    }

    .section-title-form {
        font-size: var(--text-lg);
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--primary);
        display: block;
    }

    .form-input-custom {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid var(--border-default);
        border-radius: var(--radius-md);
        font-size: var(--text-sm);
        transition: all var(--transition-fast);
        background: var(--bg-secondary);
        color: var(--text-primary);
    }

    .form-input-custom::placeholder {
        color: var(--text-muted);
    }

    .form-input-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        outline: none;
    }

    .role-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 32px;
    }

    .role-option {
        position: relative;
    }

    .role-option input {
        position: absolute;
        opacity: 0;
    }

    .role-card {
        padding: 24px;
        border: 2px solid var(--border-default);
        border-radius: var(--radius-lg);
        text-align: center;
        cursor: pointer;
        transition: all var(--transition-base);
        background: var(--bg-secondary);
    }

    .role-card:hover {
        border-color: var(--primary);
    }

    .role-option input:checked + .role-card {
        border-color: var(--primary);
        background: rgba(59, 130, 246, 0.05);
    }

    .role-icon {
        width: 56px;
        height: 56px;
        background: var(--bg-tertiary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 24px;
        color: var(--text-tertiary);
    }

    .role-option input:checked + .role-card .role-icon {
        background: var(--primary);
        color: white;
    }

    .role-name {
        font-weight: 700;
        color: var(--text-primary);
    }

    .role-desc {
        font-size: var(--text-xs);
        color: var(--text-muted);
        margin-top: 4px;
    }

    .special-fields {
        background: var(--bg-tertiary);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin-bottom: 24px;
    }

    .special-fields-title {
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #map {
        height: 300px;
        border-radius: var(--radius-md);
        margin-top: 12px;
        z-index: 1;
    }

    .auth-btn {
        width: 100%;
        padding: 16px;
        font-size: var(--text-base);
        font-weight: 600;
        border-radius: var(--radius-md);
        margin-top: 20px;
    }

    .auth-footer {
        text-align: center;
        color: var(--text-secondary);
        margin-top: 24px;
        font-size: var(--text-sm);
    }

    .auth-footer a {
        color: var(--primary);
        font-weight: 600;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        font-size: var(--text-sm);
        margin-bottom: 6px;
    }

    @media (max-width: 576px) {
        .role-selector {
            grid-template-columns: 1fr;
        }

        .auth-card {
            max-width: 100%;
        }

        .auth-header {
            padding: 32px 24px;
        }

        .auth-body {
            padding: 24px;
        }
    }
</style>
@endsection

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Join Quickbite today</p>
        </div>

        <div class="auth-body">
            @if($errors->any())
            <div class="alert alert-danger mb-4" style="background: var(--danger-light); border: 1px solid var(--danger); border-radius: var(--radius-md); padding: 16px;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                    <li style="color: var(--danger);">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <label class="section-title-form">I want to join as</label>
                <div class="role-selector">
                    <label class="role-option">
                        <input type="radio" name="type" value="customer" checked>
                        <div class="role-card">
                            <div class="role-icon"><i class="bi bi-person"></i></div>
                            <div class="role-name">Customer</div>
                            <div class="role-desc">Order food</div>
                        </div>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="type" value="restaurant">
                        <div class="role-card">
                            <div class="role-icon"><i class="bi bi-shop"></i></div>
                            <div class="role-name">Restaurant</div>
                            <div class="role-desc">Sell food</div>
                        </div>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="type" value="rider">
                        <div class="role-card">
                            <div class="role-icon"><i class="bi bi-motorcycle"></i></div>
                            <div class="role-name">Rider</div>
                            <div class="role-desc">Deliver food</div>
                        </div>
                    </label>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input-custom" required placeholder="Your full name">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input-custom" required placeholder="your@email.com">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input-custom" required minlength="8" placeholder="Min 8 characters">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-input-custom" required placeholder="Confirm password">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-input-custom" placeholder="Your phone number">
                </div>

                <div id="restaurantFields" style="display: none;">
                    <div class="special-fields">
                        <div class="special-fields-title">
                            <i class="bi bi-shop text-primary"></i> Restaurant Details
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Restaurant Name</label>
                            <input type="text" name="restaurant_name" class="form-input-custom" id="restaurantName" placeholder="Your restaurant name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Search Location</label>
                            <div class="input-group">
                                <input type="text" class="form-input-custom" id="addressSearch" placeholder="Search address..." style="border-radius: var(--radius-md) 0 0 var(--radius-md);">
                                <button type="button" class="btn btn-primary" onclick="searchLocation()" style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <div id="searchResults" class="mt-2" style="display: none;"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Set Location on Map</label>
                            <div id="map"></div>
                            <small class="text-muted d-block mt-2">Click on the map to set your restaurant location</small>
                            <div id="locationStatus" class="mt-2"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" step="0.00000001" name="latitude" class="form-input-custom" id="latitude" placeholder="Latitude">
                            </div>
                            <div class="col-md-6">
                                <input type="number" step="0.00000001" name="longitude" class="form-input-custom" id="longitude" placeholder="Longitude">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="riderFields" style="display: none;">
                    <div class="special-fields">
                        <div class="special-fields-title">
                            <i class="bi bi-motorcycle text-success"></i> Rider Details
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Vehicle Type</label>
                                <select name="vehicle_type" class="form-input-custom">
                                    <option value="car">Car</option>
                                    <option value="motorcycle">Motorcycle</option>
                                    <option value="bicycle">Bicycle</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Vehicle Plate</label>
                                <input type="text" name="vehicle_plate" class="form-input-custom" placeholder="ABC-1234">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">License Number</label>
                                <input type="text" name="license_number" class="form-input-custom" placeholder="License number">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-custom auth-btn">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>
            </form>

            <div class="auth-footer">
                <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map;
let marker;
let userLat = 30.0444;
let userLng = 31.2357;

document.getElementById('userType')?.addEventListener('change', handleTypeChange);
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', handleTypeChange);
});

function handleTypeChange() {
    const type = document.querySelector('input[name="type"]:checked').value;
    const restaurantFields = document.getElementById('restaurantFields');
    const riderFields = document.getElementById('riderFields');

    if (type === 'restaurant') {
        restaurantFields.style.display = 'block';
        riderFields.style.display = 'none';
        setTimeout(initMap, 300);
    } else if (type === 'rider') {
        restaurantFields.style.display = 'none';
        riderFields.style.display = 'block';
    } else {
        restaurantFields.style.display = 'none';
        riderFields.style.display = 'none';
    }
}

function initMap() {
    if (map) { map.remove(); map = null; }

    map = L.map('map').setView([userLat, userLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    marker = L.marker([userLat, userLng], { draggable: true }).addTo(map);

    map.on('click', function(e) {
        userLat = e.latlng.lat;
        userLng = e.latlng.lng;
        marker.setLatLng([userLat, userLng]);
        document.getElementById('latitude').value = userLat.toFixed(6);
        document.getElementById('longitude').value = userLng.toFixed(6);
        document.getElementById('locationStatus').innerHTML = '<div class="alert py-2 px-3 mb-0" style="background: var(--success-light); color: var(--success); border-radius: var(--radius-md);"><i class="bi bi-check-circle me-1"></i> Location set!</div>';
    });

    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('latitude').value = pos.lat.toFixed(6);
        document.getElementById('longitude').value = pos.lng.toFixed(6);
    });
}

async function searchLocation() {
    const query = document.getElementById('addressSearch').value;
    const resultsDiv = document.getElementById('searchResults');
    if (!query) return;

    resultsDiv.style.display = 'block';
    resultsDiv.innerHTML = '<div class="p-2" style="color: var(--text-muted);">Searching...</div>';

    try {
        const response = await fetch("{{ route('geocode.search') }}?q=" + encodeURIComponent(query));
        const data = await response.json();
        resultsDiv.innerHTML = '';

        if (data.length > 0) {
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'p-2 border-bottom';
                div.style.cursor = 'pointer';
                div.style.color = 'var(--text-primary)';
                div.innerHTML = '<strong>' + (item.address || item.name) + '</strong>';
                div.onclick = function() {
                    userLat = parseFloat(item.latitude);
                    userLng = parseFloat(item.longitude);
                    document.getElementById('latitude').value = userLat.toFixed(6);
                    document.getElementById('longitude').value = userLng.toFixed(6);
                    document.getElementById('addressSearch').value = item.address || item.name;
                    resultsDiv.style.display = 'none';
                    if (map) { map.setView([userLat, userLng], 16); marker.setLatLng([userLat, userLng]); }
                };
                resultsDiv.appendChild(div);
            });
        } else {
            resultsDiv.innerHTML = '<div class="p-2" style="color: var(--text-muted);">No results found</div>';
        }
    } catch (e) {
        resultsDiv.innerHTML = '<div class="p-2" style="color: var(--danger);">Search error</div>';
    }
}

document.getElementById('addressSearch')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); searchLocation(); }
});

document.querySelector('form')?.addEventListener('submit', function(e) {
    var type = document.querySelector('input[name="type"]:checked')?.value
    if (type === 'restaurant') {
        var lat = document.getElementById('latitude').value
        var lng = document.getElementById('longitude').value
        var err = verifyLocation(lat, lng)
        if (err) {
            e.preventDefault()
            var status = document.getElementById('locationStatus')
            if (status) {
                status.innerHTML = '<div class="alert py-2 px-3 mt-2" style="background: var(--danger-light); color: var(--danger); border-radius: var(--radius-md);"><i class="bi bi-exclamation-circle me-1"></i>' + err + '</div>'
                status.scrollIntoView({ behavior: 'smooth', block: 'center' })
            }
        }
    }
})
</script>
@endsection