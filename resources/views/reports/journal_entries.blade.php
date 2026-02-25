@extends('layout')

@section('title', 'Journal Entries')

@section('content')
<div class="card">
    <h1 class="card-header">Journal Entries</h1>

    <p style="color: #6b7280; margin-bottom: 2rem;">
        All accounting transactions following double-entry bookkeeping principles. Each transaction has equal debits and credits.
    </p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Account</th>
                <th>Description</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th>Transaction</th>
            </tr>
        </thead>
        <tbody>
            @php $currentTransactionKey = null; @endphp
            @foreach($journalEntries as $entry)
                @php
                    $transactionKey = $entry->transaction_type . '-' . $entry->transaction_id;
                    $newTransaction = ($currentTransactionKey != $transactionKey);
                    $currentTransactionKey = $transactionKey;
                @endphp
                <tr style="{{ $newTransaction ? 'border-top: 2px solid #0062cc;' : '' }}">
                    <td>
                        @if($newTransaction)
                            {{ $entry->created_at->format('Y-m-d H:i') }}
                        @endif
                    </td>
                    <td>
                        <strong>{{ $entry->account->name }}</strong>
                        <br><small class="text-muted">{{ $entry->account->code }}</small>
                    </td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-right">
                        @if($entry->debit_amount > 0)
                            ৳{{ number_format($entry->debit_amount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($entry->credit_amount > 0)
                            ৳{{ number_format($entry->credit_amount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($newTransaction)
                            @if($entry->transaction_type === 'sale')
                                <a href="{{ route('sales.show', $entry->transaction_id) }}" class="btn btn-sm btn-primary">View Sale #{{ $entry->transaction_id }}</a>
                            @elseif($entry->transaction_type === 'payment')
                                @php $paymentSale = \App\Models\Payment::find($entry->transaction_id); @endphp
                                @if($paymentSale && $paymentSale->sale_id)
                                    <a href="{{ route('sales.show', $paymentSale->sale_id) }}" class="btn btn-sm" style="background-color: #059669; color: white;">Payment for Sale #{{ $paymentSale->sale_id }}</a>
                                @endif
                            @elseif($entry->transaction_type === 'purchase')
                                <a href="{{ route('products.show', $entry->transaction_id) }}" class="btn btn-sm btn-secondary">View Product #{{ $entry->transaction_id }}</a>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 2rem;">
        {{ $journalEntries->links() }}
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
    </div>
</div>
@endsection
