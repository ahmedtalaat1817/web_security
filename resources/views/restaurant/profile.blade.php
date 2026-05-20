@extends('layouts.dashboard')
@section('page_title', 'Restaurant Profile')
@section('sidebar_menu')
<div class="nav-item">
    <a href="{{ route('restaurant.dashboard') }}" class="nav-link">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.orders') }}" class="nav-link">
        <i class="bi bi-bag"></i> Orders
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.menu.index') }}" class="nav-link">
        <i class="bi bi-menu-button-wide"></i> Menu
    </a>
</div>
<div class="nav-item">
    <a href="{{ route('restaurant.profile') }}" class="nav-link active">
        <i class="bi bi-shop"></i> Restaurant Profile
    </a>
</div>
<div class="nav-item mt-3">
    <a href="{{ route('home') }}" class="nav-link">
        <i class="bi bi-arrow-left"></i> Back to Home
    </a>
</div>
@endsection

@section('styles')
<style>
    .profile-form {
        background: white;
        border-radius: var(--radius-lg);
        padding: 32px;
        box-shadow: var(--shadow-sm);
    }
    .form-label {
        font-weight: 600;
        color: var(--dark-charcoal);
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        border: 2px solid var(--light-gray);
        border-radius: var(--radius-md);
        padding: 12px 16px;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 4px rgba(255,107,53,0.1);
    }
    .section-label {
        font-size: 14px;
        font-weight: 700;
        color: var(--muted-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--off-white);
    }
    .image-preview {
        position: relative;
        display: inline-block;
        margin-top: 8px;
        border-radius: var(--radius-md);
        border: 2px solid var(--light-gray);
        padding: 4px;
        background: var(--off-white);
        max-width: 100%;
    }
    .image-preview img {
        max-height: 90px;
        max-width: 100%;
        border-radius: calc(var(--radius-md) - 2px);
        display: block;
        object-fit: contain;
    }
    .image-preview .btn-remove {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--danger-red);
        color: white;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: var(--transition-fast);
        padding: 0;
        line-height: 1;
    }
    .image-preview .btn-remove:hover {
        background: var(--danger);
        transform: scale(1.1);
    }
</style>
@endsection

@section('content')
@if(session('status'))
<div class="alert alert-success border-0 rounded-3 mb-4">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('restaurant.profile.update') }}" class="profile-form">
    @csrf
    @method('PUT')

    <div class="section-label">Restaurant Info</div>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label">Restaurant Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $restaurant->name) }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $restaurant->email) }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Tell customers about your restaurant...">{{ old('description', $restaurant->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="section-label mt-4">Contact &amp; Location</div>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $restaurant->address) }}">
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $restaurant->phone) }}">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="section-label mt-4">Delivery Settings</div>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Delivery Fee ($)</label>
            <input type="number" step="0.01" min="0" name="delivery_fee" class="form-control @error('delivery_fee') is-invalid @enderror" value="{{ old('delivery_fee', $restaurant->delivery_fee) }}">
            @error('delivery_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Delivery Time (min)</label>
            <input type="number" min="1" name="delivery_time_minutes" class="form-control @error('delivery_time_minutes') is-invalid @enderror" value="{{ old('delivery_time_minutes', $restaurant->delivery_time_minutes) }}">
            @error('delivery_time_minutes') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Minimum Order ($)</label>
            <input type="number" step="0.01" min="0" name="minimum_order" class="form-control @error('minimum_order') is-invalid @enderror" value="{{ old('minimum_order', $restaurant->minimum_order) }}">
            @error('minimum_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="section-label mt-4">Images</div>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label">Logo</label>
            <div class="input-group">
                <input type="text" name="logo" id="logoUrl" class="form-control @error('logo') is-invalid @enderror" value="{{ old('logo', $restaurant->logo) }}" placeholder="https://... or upload">
                <button type="button" class="btn btn-primary-custom" onclick="document.getElementById('logoUpload').click()">
                    <i class="bi bi-folder2-open"></i> Browse
                </button>
                <input type="file" id="logoUpload" accept="image/*" style="display:none">
            </div>
            <div id="logoPreview" class="image-preview" @if(!$restaurant->logo) style="display:none;" @endif>
                <img src="{{ $restaurant->logo ?? '' }}" alt="Logo Preview">
                <button type="button" class="btn-remove" onclick="clearImage('logo')"><i class="bi bi-x"></i></button>
            </div>
            @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Cover Image</label>
            <div class="input-group">
                <input type="text" name="cover_image" id="coverUrl" class="form-control @error('cover_image') is-invalid @enderror" value="{{ old('cover_image', $restaurant->cover_image) }}" placeholder="https://... or upload">
                <button type="button" class="btn btn-primary-custom" onclick="document.getElementById('coverUpload').click()">
                    <i class="bi bi-folder2-open"></i> Browse
                </button>
                <input type="file" id="coverUpload" accept="image/*" style="display:none">
            </div>
            <div id="coverPreview" class="image-preview" @if(!$restaurant->cover_image) style="display:none;" @endif>
                <img src="{{ $restaurant->cover_image ?? '' }}" alt="Cover Preview">
                <button type="button" class="btn-remove" onclick="clearImage('cover')"><i class="bi bi-x"></i></button>
            </div>
            @error('cover_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <script>
    function setupImageUpload(prefix, fieldName) {
        var uploadEl = document.getElementById(prefix + 'Upload');
        var urlEl = document.getElementById(prefix + 'Url');
        var previewEl = document.getElementById(prefix + 'Preview');

        uploadEl?.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function(ev) {
                urlEl.value = ev.target.result;
                previewEl.querySelector('img').src = ev.target.result;
                previewEl.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });

        document.querySelector('[name="' + fieldName + '"]')?.addEventListener('input', function() {
            if (this.value && this.value.startsWith('http')) {
                previewEl.querySelector('img').src = this.value;
                previewEl.style.display = 'block';
            }
        });
    }
    setupImageUpload('logo', 'logo');
    setupImageUpload('cover', 'cover_image');
    function clearImage(prefix) {
        document.getElementById(prefix + 'Url').value = '';
        document.getElementById(prefix + 'Preview').style.display = 'none';
        document.getElementById(prefix + 'Upload').value = '';
    }
    </script>
        </div>
    </div>

    <div class="section-label mt-4">Restaurant Status</div>
    <div class="mb-4">
        <div class="form-check form-switch">
            <input type="checkbox" name="is_open" class="form-check-input" id="is_open" value="1" @checked($restaurant->is_open)>
            <label class="form-check-label" for="is_open">Restaurant is currently accepting orders</label>
        </div>
    </div>

    <div class="d-flex gap-2 pt-3 border-top">
        <button type="submit" class="btn btn-primary-custom">
            <i class="bi bi-check-lg me-2"></i>Save Changes
        </button>
        <a href="{{ route('restaurant.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
@endsection