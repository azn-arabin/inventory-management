@extends('layout')

@section('title', 'Collect Payment')

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <h1 class="card-header">Collect Payment - Sale #{{ $sale->id }}</h1>

    <div style="background-color: #f3f4f6; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: #0062cc;">Sale Summary</h3>
        <table style="font-size: 0.95rem;">
            <tr>
                <td><strong>Product:</strong></td>
                <td>{{ $sale->product->name }}</td>
            </tr>
            <tr>
                <td><strong>Customer:</strong></td>
                <td>{{ $sale->customer_name }}</td>
            </tr>
            <tr>
                <td><strong>Sale Date:</strong></td>
                <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Total Amount:</strong></td>
                <td>৳{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            <tr style="color: #10b981;">
                <td><strong>Already Paid:</strong></td>
                <td><strong>৳{{ number_format($totalPaid, 2) }}</strong></td>
            </tr>
            <tr style="color: #ef4444; font-weight: bold; font-size: 1.1rem; border-top: 2px solid #d1d5db;">
                <td><strong>Due Amount:</strong></td>
                <td><strong>৳{{ number_format($totalDue, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    @if($sale->payments->count() > 0)
        <div style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #0062cc;">Payment History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th class="text-right">Amount</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background-color: #f0fdf4;">
                        <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                        <td><span class="badge badge-success">Initial Payment</span></td>
                        <td class="text-right">৳{{ number_format($sale->paid_amount - $sale->payments->sum('amount'), 2) }}</td>
                        <td>Payment at time of sale</td>
                    </tr>
                    @foreach($sale->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge {{ $payment->payment_method == 'cash' ? 'badge-success' : ($payment->payment_method == 'bank' ? 'badge-primary' : 'badge-warning') }}">
                                    {{ ucfirst($payment->payment_method) }}
                                </span>
                            </td>
                            <td class="text-right">৳{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($totalDue > 0)
        <form method="POST" action="{{ route('payments.store', $sale) }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="amount" class="form-label">Payment Amount (৳) *</label>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                           value="{{ old('amount', number_format($totalDue, 2, '.', '')) }}" 
                           min="0.01" max="{{ $totalDue }}" required>
                    <small class="text-muted">Maximum: ৳{{ number_format($totalDue, 2) }}</small>
                </div>

                <div class="form-group">
                    <label for="payment_method" class="form-label">Payment Method *</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="mobile" {{ old('payment_method') == 'mobile' ? 'selected' : '' }}>Mobile Payment</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Optional payment notes">{{ old('notes') }}</textarea>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">💰 Collect Payment</button>
                <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    @else
        <div style="text-align: center; padding: 2rem; background-color: #f0fdf4; border-radius: 8px;">
            <span class="badge badge-success" style="font-size: 1.2rem; padding: 0.75rem 1.5rem;">✓ Fully Paid</span>
            <p style="margin-top: 1rem; color: #16a34a;">This sale has been fully paid. No outstanding balance.</p>
        </div>
        <div style="margin-top: 1.5rem;">
            <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary">Back to Sale Details</a>
        </div>
    @endif
</div>
@endsection
