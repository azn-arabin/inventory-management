@extends('layout')

@section('title', 'Chart of Accounts')

@section('content')
<div class="card">
    <h1 class="card-header">Chart of Accounts</h1>

    <p style="color: #6b7280; margin-bottom: 2rem;">
        Complete listing of all accounts used in the accounting system with current balances.
    </p>

    @foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expenses'] as $type => $label)
        <h2 style="font-size: 1.5rem; margin-top: 2rem; margin-bottom: 1rem; color: #0062cc; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem;">
            {{ $label }}
        </h2>

        <table style="margin-bottom: 2rem;">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Account Name</th>
                    <th>Type</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php $typeTotal = 0; @endphp
                @foreach($accounts->where('type', $type) as $account)
                    <tr>
                        <td><strong>{{ $account->code }}</strong></td>
                        <td>{{ $account->name }}</td>
                        <td>
                            <span class="badge badge-{{ $account->type == 'asset' ? 'success' : ($account->type == 'liability' ? 'danger' : ($account->type == 'revenue' ? 'primary' : 'warning')) }}">
                                {{ ucfirst($account->type) }}
                            </span>
                        </td>
                        <td class="text-right">
                            <strong>৳{{ number_format($account->balance, 2) }}</strong>
                        </td>
                    </tr>
                    @php $typeTotal += $account->balance; @endphp
                @endforeach
                @if($accounts->where('type', $type)->count() > 0)
                    <tr style="font-weight: bold; background-color: #f3f4f6;">
                        <td colspan="3">Total {{ $label }}</td>
                        <td class="text-right">৳{{ number_format($typeTotal, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach

    <div class="card" style="background-color: #f0f9ff;">
        <h3 style="color: #0062cc; margin-bottom: 1rem;">Accounting Equation</h3>
        <p style="font-size: 1.1rem; margin: 0;">
            <strong>Assets</strong> = <strong>Liabilities</strong> + <strong>Equity</strong> + (<strong>Revenue</strong> - <strong>Expenses</strong>)
        </p>
        <p style="color: #6b7280; margin-top: 0.5rem;">
            This fundamental equation must always balance in a properly maintained accounting system.
        </p>
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
    </div>
</div>
@endsection
