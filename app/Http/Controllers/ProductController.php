<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index()
    {
        $products = Product::orderBy('name')->paginate(20);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created product in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0.01',
            'sell_price' => 'required|numeric|min:0.01',
            'opening_stock' => 'required|integer|min:0',
        ]);

        // Validate sell_price >= purchase_price
        if ($validated['sell_price'] < $validated['purchase_price']) {
            return back()->withErrors([
                'sell_price' => 'Sell price (৳' . number_format($validated['sell_price'], 2) . ') should not be less than purchase price (৳' . number_format($validated['purchase_price'], 2) . ').'
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'purchase_price' => $validated['purchase_price'],
                'sell_price' => $validated['sell_price'],
                'opening_stock' => $validated['opening_stock'],
                'current_stock' => $validated['opening_stock'],
            ]);

            // Create purchase journal entries for initial inventory
            if ($product->opening_stock > 0) {
                $this->createPurchaseJournalEntries($product, $product->opening_stock);
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product created successfully with journal entries!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load('sales');
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0.01',
            'sell_price' => 'required|numeric|min:0.01',
            'opening_stock' => 'required|integer|min:0',
        ]);

        // Validate sell_price >= purchase_price
        if ($validated['sell_price'] < $validated['purchase_price']) {
            return back()->withErrors([
                'sell_price' => 'Sell price (৳' . number_format($validated['sell_price'], 2) . ') should not be less than purchase price (৳' . number_format($validated['purchase_price'], 2) . ').'
            ])->withInput();
        }

        // Calculate the sold quantity before update
        $soldQuantity = $product->opening_stock - $product->current_stock;

        // Validate that new opening stock is not less than sold quantity
        if ($validated['opening_stock'] < $soldQuantity) {
            return back()->withErrors([
                'opening_stock' => "Opening stock cannot be less than already sold quantity ({$soldQuantity} units)."
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            $oldOpeningStock = $product->opening_stock;
            $newOpeningStock = $validated['opening_stock'];
            $stockDifference = $newOpeningStock - $oldOpeningStock;

            // Calculate new current stock
            $newCurrentStock = $newOpeningStock - $soldQuantity;

            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'purchase_price' => $validated['purchase_price'],
                'sell_price' => $validated['sell_price'],
                'opening_stock' => $newOpeningStock,
                'current_stock' => $newCurrentStock,
            ]);

            // If stock increased, create purchase journal entries for additional inventory
            if ($stockDifference > 0) {
                $this->createPurchaseJournalEntries($product, $stockDifference);
            }

            DB::commit();

            return redirect()->route('products.show', $product)
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Create purchase journal entries when adding inventory
     * Debit: Inventory (Asset increases)
     * Credit: Owner's Equity (Capital invested)
     */
    private function createPurchaseJournalEntries(Product $product, int $quantity)
    {
        $inventoryValue = $product->purchase_price * $quantity;
        if ($inventoryValue <= 0) {
            return;
        }

        $referenceNumber = 'PUR-' . str_pad($product->id, 6, '0', STR_PAD_LEFT);
        $description = "Purchase of {$quantity} units of {$product->name}";
        $entryDate = now();

        $inventoryAccount = Account::where('code', '1130')->first(); // Inventory
        $equityAccount = Account::where('code', '3100')->first();    // Owner's Equity

        if (!$inventoryAccount || !$equityAccount) {
            throw new \Exception('Required accounts not found. Please run the AccountSeeder.');
        }

        // Debit: Inventory (Asset increases)
        JournalEntry::create([
            'account_id' => $inventoryAccount->id,
            'transaction_type' => 'purchase',
            'transaction_id' => $product->id,
            'entry_date' => $entryDate,
            'description' => $description . ' - Inventory added',
            'debit_amount' => $inventoryValue,
            'credit_amount' => 0,
            'reference_number' => $referenceNumber,
        ]);
        $inventoryAccount->updateBalance($inventoryValue, true);

        // Credit: Owner's Equity (Capital invested in inventory)
        JournalEntry::create([
            'account_id' => $equityAccount->id,
            'transaction_type' => 'purchase',
            'transaction_id' => $product->id,
            'entry_date' => $entryDate,
            'description' => $description . ' - Capital invested',
            'debit_amount' => 0,
            'credit_amount' => $inventoryValue,
            'reference_number' => $referenceNumber,
        ]);
        $equityAccount->updateBalance($inventoryValue, false);
    }

    /**
     * Remove the specified product from storage
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
}
