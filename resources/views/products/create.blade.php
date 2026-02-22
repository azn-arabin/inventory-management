@extends('layout')

@section('title', 'Add Product')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h1 class="card-header">Add New Product</h1>

    <form method="POST" action="{{ route('products.store') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Product Name *</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="purchase_price" class="form-label">Purchase Price (৳) *</label>
                <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" required>
            </div>

            <div class="form-group">
                <label for="sell_price" class="form-label">Sell Price (৳) *</label>
                <input type="number" step="0.01" class="form-control" id="sell_price" name="sell_price" value="{{ old('sell_price') }}" required>
            </div>

            <div class="form-group">
                <label for="opening_stock" class="form-label">Opening Stock *</label>
                <input type="number" class="form-control" id="opening_stock" name="opening_stock" value="{{ old('opening_stock', 0) }}" required>
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Save Product</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
