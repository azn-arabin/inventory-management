@extends('layout')

@section('title', 'Sales Report')

@section('content')
<div class="card">
    <h1 class="card-header">Sales Report</h1>

    <form method="GET" action="{{ route('reports.sales') }}" style="margin-bottom: 2rem;">
        <div class="form-row">
            <div class="form-group">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="form-group">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="form-group">
                <label class="form-label" style="visibility: hidden;">Filter</label>
                <button type="submit" class="btn btn-primary">Apply Filter</button>
                <a href="{{ route('reports.sales') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="background-color: #f0f9ff;">
            <h4 style="color: #0062cc;">Total Sales</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #0062cc;">{{ $statistics['totalSales'] }}</p>
        </div>
        <div class="card" style="background-color: #f0fdf4;">
            <h4 style="color: #16a34a;">Total Amount</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #16a34a;">৳{{ number_format($statistics['totalAmount'], 2) }}</p>
        </div>
        <div class="card" style="background-color: #f0fdf4;">
            <h4 style="color: #059669;">Paid</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #059669;">৳{{ number_format($statistics['totalPaid'], 2) }}</p>
        </div>
        <div class="card" style="background-color: #fef2f2;">
            <h4 style="color: #dc2626;">Due</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #dc2626;">৳{{ number_format($statistics['totalDue'], 2) }}</p>
        </div>
        <div class="card" style="background-color: #fefce8;">
            <h4 style="color: #ca8a04;">Avg Sale</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #ca8a04;">৳{{ number_format($statistics['averageSale'], 2) }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product</th>
                <th>Customer</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Total</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Due</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr>
                    <td>{{ $sale->sale_date->format('Y-m-d') }}</td>
                    <td>{{ $sale->product->name }}</td>
                    <td>{{ $sale->customer_name }}</td>
                    <td class="text-right">{{ $sale->quantity }}</td>
                    <td class="text-right">৳{{ number_format($sale->unit_price, 2) }}</td>
                    <td class="text-right">৳{{ number_format($sale->discount, 2) }}</td>
                    <td class="text-right"><strong>৳{{ number_format($sale->total_amount, 2) }}</strong></td>
                    <td class="text-right">৳{{ number_format($sale->paid_amount, 2) }}</td>
                    <td class="text-right">৳{{ number_format($sale->due_amount, 2) }}</td>
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
                    <td colspan="11" style="text-align: center; color: #6b7280;">No sales found</td>
                </tr>
            @endforelse
        </tbody>
        @if($sales->count() > 0)
            <tfoot style="background-color: #f3f4f6; font-weight: bold;">
                <tr>
                    <td colspan="6">TOTALS</td>
                    <td class="text-right">৳{{ number_format($sales->sum('total_amount'), 2) }}</td>
                    <td class="text-right">৳{{ number_format($sales->sum('paid_amount'), 2) }}</td>
                    <td class="text-right">৳{{ number_format($sales->sum('due_amount'), 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div style="margin-top: 2rem;">
        {{ $sales->links() }}
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">New Sale</a>
    </div>
</div>
@endsection
