@extends('layout')

@section('title', 'Dashboard')

@section('content')
<h1 style="font-size: 2rem; margin-bottom: 2rem; color: #1f2937;">Welcome to Inventory Management System</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Total Products</div>
        <div class="stat-value">{{ $totalProducts }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Sales</div>
        <div class="stat-value">{{ $totalSales }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Revenue</div>
        <div class="stat-value">৳{{ number_format($totalRevenue, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Total Due</div>
        <div class="stat-value">৳{{ number_format($totalDue, 2) }}</div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
        <div class="stat-title">Cash Balance</div>
        <div class="stat-value">৳{{ number_format($cashBalance, 2) }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <div class="stat-title">Inventory Value</div>
        <div class="stat-value">৳{{ number_format($inventoryBalance, 2) }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
        <div class="stat-title">Accounts Receivable</div>
        <div class="stat-value">৳{{ number_format($receivableBalance, 2) }}</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
        <div class="stat-title">Sales Revenue</div>
        <div class="stat-value">৳{{ number_format($revenueBalance, 2) }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
    <div class="card">
        <h2 class="card-header">Low Stock Products</h2>
        @if($lowStockProducts->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-right">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td class="text-right">
                                <span class="badge {{ $product->current_stock == 0 ? 'badge-danger' : 'badge-warning' }}">
                                    {{ $product->current_stock }} units
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">All products have sufficient stock.</p>
        @endif
    </div>

    <div class="card">
        <h2 class="card-header">Recent Sales</h2>
        @if($recentSales->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSales as $sale)
                        <tr>
                            <td>{{ $sale->product->name }} ({{ $sale->quantity }}x)</td>
                            <td class="text-right">৳{{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">No sales recorded yet.</p>
        @endif
    </div>
</div>

<div style="margin-top: 2rem; text-align: center;">
    <a href="{{ route('sales.create') }}" class="btn btn-primary" style="margin-right: 1rem;">Make a Sale</a>
    <a href="{{ route('products.create') }}" class="btn btn-success" style="margin-right: 1rem;">Add Product</a>
    <a href="{{ route('reports.financial') }}" class="btn btn-secondary">View Reports</a>
</div>
@endsection
