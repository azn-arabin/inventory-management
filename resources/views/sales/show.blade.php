@extends('layout')

@section('title', 'Sale Details')

@section('content')
<div class="card">
    <h1 class="card-header">Sale Details - #{{ $sale->id }}</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <div>
            <h3 style="font-size: 1.2rem; margin-bottom: 1rem; color: #0062cc;">Sale Information</h3>
            <p><strong>Date:</strong> {{ $sale->sale_date->format('Y-m-d H:i:s') }}</p>
            <p><strong>Product:</strong> {{ $sale->product->name }}</p>
            <p><strong>Customer:</strong> {{ $sale->customer_name }}</p>
            <p><strong>Quantity:</strong> {{ $sale->quantity }} units</p>
            @if($sale->notes)
                <p><strong>Notes:</strong> {{ $sale->notes }}</p>
            @endif
        </div>

        <div>
            <h3 style="font-size: 1.2rem; margin-bottom: 1rem; color: #0062cc;">Amount Breakdown</h3>
            <table style="font-size: 0.95rem;">
                <tr>
                    <td>Unit Price:</td>
                    <td class="text-right">৳{{ number_format($sale->unit_price, 2) }}</td>
                </tr>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">৳{{ number_format($sale->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-৳{{ number_format($sale->discount, 2) }}</td>
                </tr>
                <tr>
                    <td>VAT ({{ $sale->vat_rate * 100 }}%):</td>
                    <td class="text-right">৳{{ number_format($sale->vat_amount, 2) }}</td>
                </tr>
                <tr style="font-weight: bold; border-top: 2px solid #d1d5db;">
                    <td>Total Amount:</td>
                    <td class="text-right">৳{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
                <tr style="color: #10b981;">
                    <td>Paid Amount:</td>
                    <td class="text-right">৳{{ number_format($sale->paid_amount, 2) }}</td>
                </tr>
                <tr style="color: #ef4444; font-weight: 600;">
                    <td>Due Amount:</td>
                    <td class="text-right">৳{{ number_format($sale->due_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <h2 style="font-size: 1.5rem; margin-top: 2rem; margin-bottom: 1rem; color: #0062cc; border-bottom: 2px solid #0062cc; padding-bottom: 0.5rem;">
        Journal Entries (Double-Entry Bookkeeping)
    </h2>

    <table>
        <thead>
            <tr>
                <th>Account</th>
                <th>Description</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebits = 0;
                $totalCredits = 0;
            @endphp
            @foreach($sale->journalEntries as $entry)
                <tr>
                    <td>
                        <strong>{{ $entry->account->name }}</strong>
                        <br><small class="text-muted">{{ $entry->account->code }} - {{ ucfirst($entry->account->type) }}</small>
                    </td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-right">
                        @if($entry->debit_amount > 0)
                            ৳{{ number_format($entry->debit_amount, 2) }}
                            @php $totalDebits += $entry->debit_amount; @endphp
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($entry->credit_amount > 0)
                            ৳{{ number_format($entry->credit_amount, 2) }}
                            @php $totalCredits += $entry->credit_amount; @endphp
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f3f4f6; border-top: 2px solid #0062cc;">
                <td colspan="2">TOTALS</td>
                <td class="text-right">৳{{ number_format($totalDebits, 2) }}</td>
                <td class="text-right">৳{{ number_format($totalCredits, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: center; padding: 1rem;">
                    @if(abs($totalDebits - $totalCredits) < 0.01)
                        <span class="badge badge-success">✓ Journal Entries Balanced</span>
                    @else
                        <span class="badge badge-danger">✗ Journal Entries NOT Balanced</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 2rem;">
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Back to Sales</a>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">New Sale</a>
    </div>
</div>
@endsection
