@extends('layout')

@section('title', 'Product Details')

@section('content')
<div class="card">
    <h1 class="card-header">{{ $product->name }}</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <div>
            <h3 style="font-size: 1.2rem; margin-bottom: 1rem; color: #0062cc;">Product Information</h3>
            <p><strong>Name:</strong> {{ $product->name }}</p>
            <p><strong>Description:</strong> {{ $product->description ?: 'No description' }}</p>
            <p><strong>Purchase Price:</strong> ৳{{ number_format($product->purchase_price, 2) }}</p>
            <p><strong>Sell Price:</strong> ৳{{ number_format($product->sell_price, 2) }}</p>
            <p><strong>Profit per unit:</strong> 
                <span style="color: #10b981;">
                    ৳{{ number_format($product->sell_price - $product->purchase_price, 2) }}
                    ({{ number_format((($product->sell_price - $product->purchase_price) / $product->purchase_price) * 100, 2) }}%)
                </span>
            </p>
        </div>

        <div>
            <h3 style="font-size: 1.2rem; margin-bottom: 1rem; color: #0062cc;">Stock Information</h3>
            <p><strong>Opening Stock:</strong> {{ $product->opening_stock }} units</p>
            <p><strong>Current Stock:</strong> 
                <span style="font-size: 1.5rem; font-weight: bold; color: {{ $product->current_stock > 10 ? '#10b981' : ($product->current_stock > 0 ? '#f59e0b' : '#ef4444') }};">
                    {{ $product->current_stock }} units
                </span>
            </p>
            <p><strong>Stock Status:</strong> 
                @if($product->current_stock <= 0)
                    <span class="badge badge-danger">Out of Stock</span>
                @elseif($product->current_stock <= 10)
                    <span class="badge badge-warning">Low Stock</span>
                @else
                    <span class="badge badge-success">In Stock</span>
                @endif
            </p>
            <p><strong>Total Sold:</strong> {{ $product->opening_stock - $product->current_stock }} units</p>
            <p><strong>Inventory Value:</strong> ৳{{ number_format($product->current_stock * $product->purchase_price, 2) }}</p>
        </div>
    </div>

    <h2 style="font-size: 1.5rem; margin-top: 2rem; margin-bottom: 1rem; color: #0062cc; border-bottom: 2px solid #0062cc; padding-bottom: 0.5rem;">
        Recent Sales
    </h2>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($product->sales()->latest()->take(10)->get() as $sale)
                <tr>
                    <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                    <td>{{ $sale->customer_name }}</td>
                    <td class="text-right">{{ $sale->quantity }}</td>
                    <td class="text-right">৳{{ number_format($sale->unit_price, 2) }}</td>
                    <td class="text-right">৳{{ number_format($sale->total_amount, 2) }}</td>
                    <td>
                        @if($sale->due_amount <= 0)
                            <span class="badge badge-success">Paid</span>
                        @elseif($sale->paid_amount > 0)
                            <span class="badge badge-warning">Partial</span>
                        @else
                            <span class="badge badge-danger">Unpaid</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #6b7280;">No sales recorded yet</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Edit Product</a>
        <a href="{{ route('sales.create') }}?product_id={{ $product->id }}" class="btn btn-primary">Sell This Product</a>
    </div>
</div>
@endsection
