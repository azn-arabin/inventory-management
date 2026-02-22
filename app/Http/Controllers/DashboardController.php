<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Account;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard
     */
    public function index()
    {
        // Get summary statistics
        $totalProducts = Product::count();
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total_amount');
        $totalDue = Sale::sum('due_amount');
        
        // Low stock products
        $lowStockProducts = Product::where('current_stock', '<', 10)
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get();

        // Recent sales
        $recentSales = Sale::with('product')
            ->orderBy('sale_date', 'desc')
            ->limit(5)
            ->get();

        // Get account balances for key accounts
        $cashBalance = Account::where('code', '1110')->value('balance') ?? 0;
        $inventoryBalance = Account::where('code', '1130')->value('balance') ?? 0;
        $receivableBalance = Account::where('code', '1120')->value('balance') ?? 0;
        $revenueBalance = Account::where('code', '4100')->value('balance') ?? 0;

        return view('dashboard', compact(
            'totalProducts',
            'totalSales',
            'totalRevenue',
            'totalDue',
            'lowStockProducts',
            'recentSales',
            'cashBalance',
            'inventoryBalance',
            'receivableBalance',
            'revenueBalance'
        ));
    }
}
