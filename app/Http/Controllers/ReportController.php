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
        $query = Sale::query();

        if ($request->has('start_date') && $request->start_date) {
            $query->where('sale_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('sale_date', '<=', $request->end_date . ' 23:59:59');
        }

        // Calculate totals
        $totalSales = $query->sum('subtotal');
        $totalDiscount = $query->sum('discount');
        $totalExpenses = $query->sum(DB::raw('quantity * (SELECT purchase_price FROM products WHERE id = sales.product_id)'));
        $totalPaid = $query->sum('paid_amount');
        $totalDue = $query->sum('due_amount');
        $grossProfit = $totalSales - $totalDiscount - $totalExpenses;
        $profitMargin = $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0;

        // Get VAT payable from account
        $vatPayableAccount = Account::where('code', '2120')->first();
        $discountAccount = Account::where('code', '4200')->first();

        $data = [
            'totalSales' => $totalSales - $totalDiscount,
            'totalExpenses' => $totalExpenses,
            'grossProfit' => $grossProfit,
            'profitMargin' => $profitMargin,
            'totalDiscount' => $totalDiscount,
            'vatPayable' => $vatPayableAccount ? $vatPayableAccount->balance : 0,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
        ];

        // Get accounts by type
        $revenueAccounts = Account::whereIn('type', ['revenue'])->get();
        $expenseAccounts = Account::whereIn('type', ['expense'])->get();

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
