@extends('layout')

@section('title', 'Edit Product')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h1 class="card-header">Edit Product</h1>

    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name" class="form-label">Product Name *</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="purchase_price" class="form-label">Purchase Price (৳) *</label>
                <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required>
            </div>

            <div class="form-group">
                <label for="sell_price" class="form-label">Sell Price (৳) *</label>
                <input type="number" step="0.01" class="form-control" id="sell_price" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" required>
            </div>
        </div>

        <div class="card" style="background-color: #fef3c7; border-left: 4px solid #f59e0b; margin-bottom: 1.5rem;">
            <h4 style="color: #92400e; margin-bottom: 0.5rem;">⚠️ Stock Update Warning</h4>
            <p style="color: #78350f; margin: 0; font-size: 0.9rem;">
                Current Stock: <strong>{{ $product->current_stock }} units</strong><br>
                Opening Stock: <strong>{{ $product->opening_stock }} units</strong><br>
                Already Sold: <strong>{{ $product->opening_stock - $product->current_stock }} units</strong>
            </p>
            <p style="color: #78350f; margin-top: 0.5rem; margin-bottom: 0; font-size: 0.9rem;">
                Note: Changing opening stock will adjust current stock proportionally. Be careful not to set opening stock below already sold quantity.
            </p>
        </div>

        <div class="form-group">
            <label for="opening_stock" class="form-label">Opening Stock *</label>
            <input type="number" class="form-control" id="opening_stock" name="opening_stock" value="{{ old('opening_stock', $product->opening_stock) }}" min="{{ $product->opening_stock - $product->current_stock }}" required>
            <small class="text-muted">Minimum allowed: {{ $product->opening_stock - $product->current_stock }} (already sold quantity)</small>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
