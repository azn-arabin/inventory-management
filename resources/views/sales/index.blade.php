@extends('layout')

@section('title', 'Sales')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 class="card-header mb-0">Sales Transactions</h1>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">+ New Sale</a>
    </div>

    @if($sales->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Customer</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Due</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                        <td>{{ $sale->product->name }}</td>
                        <td>{{ $sale->customer_name }}</td>
                        <td class="text-right">{{ $sale->quantity }}</td>
                        <td class="text-right">৳{{ number_format($sale->total_amount, 2) }}</td>
                        <td class="text-right">৳{{ number_format($sale->paid_amount, 2) }}</td>
                        <td class="text-right">
                            @if($sale->due_amount > 0)
                                <span class="badge badge-warning">৳{{ number_format($sale->due_amount, 2) }}</span>
                            @else
                                <span class="badge badge-success">Paid</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-primary">View</a>
                            @if($sale->due_amount > 0)
                                <a href="{{ route('payments.create', $sale) }}" class="btn btn-sm btn-success">Collect Payment</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            {{ $sales->links() }}
        </div>
    @else
        <p class="text-muted">No sales recorded yet. <a href="{{ route('sales.create') }}">Record your first sale</a></p>
    @endif
</div>
@endsection
