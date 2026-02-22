@extends('layout')

@section('title', 'Financial Report')

@section('content')
<div class="card">
    <h1 class="card-header">Financial Report</h1>

    <form method="GET" action="{{ route('reports.financial') }}" style="margin-bottom: 2rem;">
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
                <a href="{{ route('reports.financial') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    @if(request('start_date') || request('end_date'))
        <div style="padding: 1rem; background-color: #e0f7fa; border-left: 4px solid #0062cc; margin-bottom: 2rem;">
            <strong>Date Range:</strong> 
            {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('Y-m-d') : 'Beginning' }} 
            to 
            {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('Y-m-d') : 'Today' }}
        </div>
    @endif

    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #0062cc;">Revenue</h2>
    <table style="margin-bottom: 2rem;">
        <thead>
            <tr>
                <th>Account</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $totalRevenue = 0; @endphp
            @foreach($revenueAccounts as $account)
                <tr>
                    <td>{{ $account->name }} ({{ $account->code }})</td>
                    <td class="text-right">৳{{ number_format($account->balance, 2) }}</td>
                </tr>
                @php $totalRevenue += ($account->type == 'revenue' && $account->code != '4200') ? $account->balance : -$account->balance; @endphp
            @endforeach
            <tr style="font-weight: bold; background-color: #f3f4f6;">
                <td>Total Revenue</td>
                <td class="text-right">৳{{ number_format($totalRevenue, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #0062cc;">Expenses</h2>
    <table style="margin-bottom: 2rem;">
        <thead>
            <tr>
                <th>Account</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $totalExpenses = 0; @endphp
            @foreach($expenseAccounts as $account)
                <tr>
                    <td>{{ $account->name }} ({{ $account->code }})</td>
                    <td class="text-right">৳{{ number_format($account->balance, 2) }}</td>
                </tr>
                @php $totalExpenses += $account->balance; @endphp
            @endforeach
            <tr style="font-weight: bold; background-color: #f3f4f6;">
                <td>Total Expenses</td>
                <td class="text-right">৳{{ number_format($totalExpenses, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: #0062cc;">Summary</h2>
    <div class="card" style="background-color: #f9fafb;">
        <table style="font-size: 1rem;">
            <tr>
                <td><strong>Total Sales:</strong></td>
                <td class="text-right"><strong>৳{{ number_format($data['totalSales'], 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Total Expenses:</strong></td>
                <td class="text-right"><strong>৳{{ number_format($data['totalExpenses'], 2) }}</strong></td>
            </tr>
            <tr style="border-top: 2px solid #0062cc;">
                <td style="font-size: 1.2rem;"><strong>Gross Profit:</strong></td>
                <td class="text-right" style="font-size: 1.2rem; color: {{ $data['grossProfit'] >= 0 ? '#10b981' : '#ef4444' }};">
                    <strong>৳{{ number_format($data['grossProfit'], 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td><strong>Profit Margin:</strong></td>
                <td class="text-right" style="color: {{ $data['profitMargin'] >= 0 ? '#10b981' : '#ef4444' }};">
                    <strong>{{ number_format($data['profitMargin'], 2) }}%</strong>
                </td>
            </tr>
        </table>
    </div>

    <h2 style="font-size: 1.5rem; margin-top: 2rem; margin-bottom: 1rem; color: #0062cc;">Additional Metrics</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <div class="card" style="background-color: #fefce8;">
            <h4 style="color: #ca8a04;">Total Discount Given</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #ca8a04;">৳{{ number_format($data['totalDiscount'], 2) }}</p>
        </div>

        <div class="card" style="background-color: #fef2f2;">
            <h4 style="color: #dc2626;">VAT Payable</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #dc2626;">৳{{ number_format($data['vatPayable'], 2) }}</p>
        </div>

        <div class="card" style="background-color: #f0fdf4;">
            <h4 style="color: #16a34a;">Total Paid</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #16a34a;">৳{{ number_format($data['totalPaid'], 2) }}</p>
        </div>

        <div class="card" style="background-color: #fef2f2;">
            <h4 style="color: #ef4444;">Total Due</h4>
            <p style="font-size: 1.8rem; margin: 0; color: #ef4444;">৳{{ number_format($data['totalDue'], 2) }}</p>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">Back to Reports</a>
    </div>
</div>
@endsection
