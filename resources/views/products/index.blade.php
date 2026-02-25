@extends('layout')

@section('title', 'Products')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 class="card-header mb-0">Products</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary">+ Add Product</a>
    </div>

    @if($products->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-right">Purchase Price</th>
                    <th class="text-right">Sell Price</th>
                    <th class="text-right">Stock</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            @if($product->description)
                                <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</small>
                            @endif
                        </td>
                        <td class="text-right">৳{{ number_format($product->purchase_price, 2) }}</td>
                        <td class="text-right">৳{{ number_format($product->sell_price, 2) }}</td>
                        <td class="text-right">
                            <span class="badge {{ $product->current_stock == 0 ? 'badge-danger' : ($product->current_stock < 10 ? 'badge-warning' : 'badge-success') }}">
                                {{ $product->current_stock }} units
                            </span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-secondary">View</a>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $products->links() }}
        </div>
    @else
        <p class="text-muted">No products found. <a href="{{ route('products.create') }}">Add your first product</a></p>
    @endif
</div>
@endsection
