@extends('layouts.app')
@section('title', 'Edit Menu Item')

@section('content')
<div class="container py-4">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Menu Item</h2>
        <a href="{{ route('restaurant.dashboard') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('restaurant.menu.update', $menuItem->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected($menuItem->category_id == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $menuItem->name }}" placeholder="e.g. Margherita Pizza" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Describe the item...">{{ $menuItem->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ $menuItem->price }}" placeholder="e.g. 12.99" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <div class="input-group">
                                <input type="text" name="image" id="imageUrl" class="form-control" value="{{ $menuItem->image }}" placeholder="https://... or upload a file">
                                <button type="button" class="btn btn-primary-custom" onclick="document.getElementById('imageUpload').click()">
                                    <i class="bi bi-folder2-open me-1"></i> Browse
                                </button>
                                <input type="file" id="imageUpload" accept="image/*" style="display:none">
                            </div>
                            <div id="imagePreview" class="mt-2" @if(!$menuItem->image) style="display:none;" @endif>
                                <img src="{{ $menuItem->image ?? '' }}" alt="Preview" style="max-height:120px;border-radius:8px;border:2px solid var(--light-gray);padding:4px;">
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="clearImage()"><i class="bi bi-x"></i></button>
                            </div>
                            <small class="text-muted">Upload an image or paste an image URL</small>
                        </div>
                        <script>
                        document.getElementById('imageUpload')?.addEventListener('change', function(e) {
                            const file = e.target.files[0];
                            if (!file) return;
                            const reader = new FileReader();
                            reader.onload = function(ev) {
                                const url = ev.target.result;
                                document.getElementById('imageUrl').value = url;
                                const preview = document.getElementById('imagePreview');
                                preview.querySelector('img').src = url;
                                preview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        });
                        function clearImage() {
                            document.getElementById('imageUrl').value = '';
                            document.getElementById('imagePreview').style.display = 'none';
                            document.getElementById('imageUpload').value = '';
                        }
                        document.querySelector('[name="image"]')?.addEventListener('input', function() {
                            if (this.value && this.value.startsWith('http')) {
                                const preview = document.getElementById('imagePreview');
                                preview.querySelector('img').src = this.value;
                                preview.style.display = 'block';
                            }
                        });
                        </script>
                        <div class="mb-3 d-flex gap-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_featured" class="form-check-input" id="featured" value="1" @checked($menuItem->is_featured)>
                                <label class="form-check-label" for="featured">Featured</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="is_available" class="form-check-input" id="available" value="1" @checked($menuItem->is_available)>
                                <label class="form-check-label" for="available">Available</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom">Update Menu Item</button>
                            <a href="{{ route('restaurant.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection