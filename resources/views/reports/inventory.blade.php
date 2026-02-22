@extends('layout')

@section('title', 'Inventory Report')

@section('content')
<div class="card">
    <h1 class="card-header">Inventory Report</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="background-color: #f0f9ff;">
            <h4 style="color: #0062cc;">Total Products</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #0062cc;">{{ $statistics['totalProducts'] }}</p>
        </div>
        <div class="card" style="background-color: #f0fdf4;">
            <h4 style="color: #16a34a;">Total Stock</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #16a34a;">{{ $statistics['totalStock'] }} units</p>
        </div>
        <div class="card" style="background-color: #fefce8;">
            <h4 style="color: #ca8a04;">Inventory Value</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #ca8a04;">৳{{ number_format($statistics['totalInventoryValue'], 2) }}</p>
        </div>
        <div class="card" style="background-color: #fef2f2;">
            <h4 style="color: #dc2626;">Low Stock Items</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #dc2626;">{{ $statistics['lowStockCount'] }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th class="text-right">Opening Stock</th>
                <th class="text-right">Current Stock</th>
                <th class="text-right">Purchase Price</th>
                <th class="text-right">Sell Price</th>
                <th class="text-right">Stock Value</th>
                <th class="text-right">Potential Revenue</th>
                <th>Stock Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td class="text-right">{{ $product->opening_stock }}</td>
                    <td class="text-right"><strong>{{ $product->current_stock }}</strong></td>
                    <td class="text-right">৳{{ number_format($product->purchase_price, 2) }}</td>
                    <td class="text-right">৳{{ number_format($product->sell_price, 2) }}</td>
                    <td class="text-right">
                        ৳{{ number_format($product->current_stock * $product->purchase_price, 2) }}
                    </td>
                    <td class="text-right">
                        ৳{{ number_format($product->current_stock * $product->sell_price, 2) }}
                    </td>
                    <td>
                        @if($product->current_stock <= 0)
                            <span class="badge badge-danger">Out of Stock</span>
                        @elseif($product->current_stock <= 10)
                            <span class="badge badge-warning">Low Stock</span>
                        @else
                            <span class="badge badge-success">In Stock</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot style="background-color: #f3f4f6; font-weight: bold;">
            <tr>
                <td colspan="5">TOTALS</td>
                <td class="text-right">৳{{ number_format($products->sum(fn($p) => $p->current_stock * $p->purchase_price), 2) }}</td>
                <td class="text-right">৳{{ number_format($products->sum(fn($p) => $p->current_stock * $p->sell_price), 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 2rem;">
        {{ $products->links() }}
    </div>

    <div class="card" style="background-color: #f9fafb; margin-top: 2rem;">
        <h3 style="margin-bottom: 1rem;">Inventory Insights</h3>
        <ul style="list-style-position: inside;">
            <li>Total inventory purchase value: <strong>৳{{ number_format($statistics['totalInventoryValue'], 2) }}</strong></li>
            <li>Potential revenue if all stock sold: <strong>৳{{ number_format($statistics['potentialRevenue'], 2) }}</strong></li>
            <li>Potential gross profit: <strong>৳{{ number_format($statistics['potentialProfit'], 2) }}</strong></li>
            <li>Average profit margin: <strong>{{ number_format($statistics['avgProfitMargin'], 2) }}%</strong></li>
        </ul>
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
        <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
    </div>
</div>
@endsection
