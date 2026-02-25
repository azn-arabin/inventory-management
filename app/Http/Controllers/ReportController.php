<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show financial reports dashboard
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Generate financial report with date filtering
     */
    public function financial(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Build sales query with date filters
        $salesQuery = Sale::query();
        if ($startDate) {
            $salesQuery->where('sale_date', '>=', $startDate);
        }
        if ($endDate) {
            $salesQuery->where('sale_date', '<=', $endDate . ' 23:59:59');
        }

        // Clone query before aggregating (sum() consumes the builder)
        $totalSales = (clone $salesQuery)->sum('subtotal');
        $totalDiscount = (clone $salesQuery)->sum('discount');
        $totalVat = (clone $salesQuery)->sum('vat_amount');
        $totalPaid = (clone $salesQuery)->sum('paid_amount');
        $totalDue = (clone $salesQuery)->sum('due_amount');
        $totalExpenses = (clone $salesQuery)->sum(DB::raw('quantity * (SELECT purchase_price FROM products WHERE id = sales.product_id)'));
        $netSales = $totalSales - $totalDiscount;
        $grossProfit = $netSales - $totalExpenses;
        $profitMargin = $netSales > 0 ? ($grossProfit / $netSales) * 100 : 0;

        // Build journal entries query with same date filters for accurate account-level reporting
        $journalQuery = JournalEntry::query();
        if ($startDate) {
            $journalQuery->where('entry_date', '>=', $startDate);
        }
        if ($endDate) {
            $journalQuery->where('entry_date', '<=', $endDate . ' 23:59:59');
        }

        // Get revenue accounts with filtered balances
        $revenueAccountIds = Account::where('type', 'revenue')->pluck('id', 'code');
        $revenueAccounts = [];
        foreach ($revenueAccountIds as $code => $accountId) {
            $account = Account::find($accountId);
            $debits = (clone $journalQuery)->where('account_id', $accountId)->sum('debit_amount');
            $credits = (clone $journalQuery)->where('account_id', $accountId)->sum('credit_amount');
            // Revenue is credit-normal: balance = credits - debits
            $filteredBalance = $credits - $debits;
            $revenueAccounts[] = (object) [
                'name' => $account->name,
                'code' => $account->code,
                'balance' => $filteredBalance,
            ];
        }

        // Get expense accounts with filtered balances
        $expenseAccountIds = Account::where('type', 'expense')->pluck('id', 'code');
        $expenseAccounts = [];
        foreach ($expenseAccountIds as $code => $accountId) {
            $account = Account::find($accountId);
            $debits = (clone $journalQuery)->where('account_id', $accountId)->sum('debit_amount');
            $credits = (clone $journalQuery)->where('account_id', $accountId)->sum('credit_amount');
            // Expense is debit-normal: balance = debits - credits
            $filteredBalance = $debits - $credits;
            $expenseAccounts[] = (object) [
                'name' => $account->name,
                'code' => $account->code,
                'balance' => $filteredBalance,
            ];
        }

        // VAT payable for the filtered period
        $vatAccountId = Account::where('code', '2120')->value('id');
        $vatDebits = $vatAccountId ? (clone $journalQuery)->where('account_id', $vatAccountId)->sum('debit_amount') : 0;
        $vatCredits = $vatAccountId ? (clone $journalQuery)->where('account_id', $vatAccountId)->sum('credit_amount') : 0;
        $filteredVatPayable = $vatCredits - $vatDebits;

        $data = [
            'totalSales' => $netSales,
            'totalExpenses' => $totalExpenses,
            'grossProfit' => $grossProfit,
            'profitMargin' => $profitMargin,
            'totalDiscount' => $totalDiscount,
            'vatPayable' => $filteredVatPayable,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
        ];

        return view('reports.financial', compact('data', 'revenueAccounts', 'expenseAccounts'));
    }

    /**
     * Show journal entries report
     */
    public function journalEntries(Request $request)
    {
        $journalEntries = JournalEntry::with('account')
            ->orderBy('created_at', 'desc')
            ->orderBy('transaction_id', 'desc')
            ->orderBy('id', 'asc')
            ->paginate(50);

        return view('reports.journal_entries', compact('journalEntries'));
    }

    /**
     * Show chart of accounts with balances
     */
    public function chartOfAccounts()
    {
        $accounts = Account::orderBy('code')->get();

        return view('reports.chart_of_accounts', compact('accounts'));
    }

    /**
     * Show sales report
     */
    public function sales(Request $request)
    {
        $query = Sale::with('product');

        if ($request->has('start_date') && $request->start_date) {
            $query->where('sale_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('sale_date', '<=', $request->end_date . ' 23:59:59');
        }

        $sales = $query->orderBy('sale_date', 'desc')->paginate(20);

        $statisticsQuery = Sale::query();
        if ($request->has('start_date') && $request->start_date) {
            $statisticsQuery->where('sale_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $statisticsQuery->where('sale_date', '<=', $request->end_date . ' 23:59:59');
        }

        $statistics = [
            'totalSales' => $statisticsQuery->count(),
            'totalAmount' => $statisticsQuery->sum('total_amount'),
            'totalPaid' => $statisticsQuery->sum('paid_amount'),
            'totalDue' => $statisticsQuery->sum('due_amount'),
            'averageSale' => $statisticsQuery->count() > 0 ? $statisticsQuery->sum('total_amount') / $statisticsQuery->count() : 0,
        ];

        return view('reports.sales', compact('sales', 'statistics'));
    }

    /**
     * Show inventory report
     */
    public function inventory()
    {
        $products = \App\Models\Product::orderBy('name')->paginate(20);

        $allProducts = \App\Models\Product::all();

        $statistics = [
            'totalProducts' => $allProducts->count(),
            'totalStock' => $allProducts->sum('current_stock'),
            'totalInventoryValue' => $allProducts->sum(function ($product) {
                return $product->current_stock * $product->purchase_price;
            }),
            'lowStockCount' => $allProducts->filter(function ($product) {
                return $product->current_stock <= 10 && $product->current_stock > 0;
            })->count(),
            'potentialRevenue' => $allProducts->sum(function ($product) {
                return $product->current_stock * $product->sell_price;
            }),
            'potentialProfit' => $allProducts->sum(function ($product) {
                return $product->current_stock * ($product->sell_price - $product->purchase_price);
            }),
            'avgProfitMargin' => $allProducts->avg(function ($product) {
                return $product->purchase_price > 0 ? (($product->sell_price - $product->purchase_price) / $product->purchase_price) * 100 : 0;
            }),
        ];

        return view('reports.inventory', compact('products', 'statistics'));
    }
}
