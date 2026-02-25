@extends('layout')

@section('title', 'Reports')

@section('content')
<div>
    <h1>Financial Reports</h1>
    <p style="color: #6b7280; margin-bottom: 2rem;">View comprehensive financial reports, journal entries, and inventory analysis</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        
        <div class="card">
            <h3 style="margin-bottom: 1rem;">📊 Financial Report</h3>
            <p style="color: #6b7280; margin-bottom: 1rem;">Comprehensive financial performance including total sales, expenses, VAT, and profit margins with date filtering</p>
            <a href="{{ route('reports.financial') }}" class="btn btn-primary">View Report</a>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1rem;">📖 Journal Entries</h3>
            <p style="color: #6b7280; margin-bottom: 1rem;">View all accounting journal entries with double-entry bookkeeping details</p>
            <a href="{{ route('reports.journal_entries') }}" class="btn btn-primary">View Report</a>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1rem;">🏦 Chart of Accounts</h3>
            <p style="color: #6b7280; margin-bottom: 1rem;">Complete chart of accounts with current balances and account types</p>
            <a href="{{ route('reports.chart_of_accounts') }}" class="btn btn-primary">View Report</a>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1rem;">💰 Sales Report</h3>
            <p style="color: #6b7280; margin-bottom: 1rem;">Detailed sales transactions with customer information and payment status</p>
            <a href="{{ route('reports.sales') }}" class="btn btn-primary">View Report</a>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1rem;">📦 Inventory Report</h3>
            <p style="color: #6b7280; margin-bottom: 1rem;">Current inventory levels, product values, and stock alerts</p>
            <a href="{{ route('reports.inventory') }}" class="btn btn-primary">View Report</a>
        </div>

    </div>
</div>
@endsection
