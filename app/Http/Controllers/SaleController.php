<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of sales
     */
    public function index()
    {
        $sales = Sale::with('product')
            ->orderBy('sale_date', 'desc')
            ->paginate(20);

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new sale
     */
    public function create(Request $request)
    {
        $products = Product::where('current_stock', '>', 0)->get();
        $vatRate = env('VAT_RATE', 0.05);
        $selectedProductId = $request->query('product_id');

        return view('sales.create', compact('products', 'vatRate', 'selectedProductId'));
    }

    /**
     * Store a newly created sale in storage with journal entries
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($validated['product_id']);

            // Check stock availability
            if ($product->current_stock < $validated['quantity']) {
                throw new \Exception('Insufficient stock! Available: ' . $product->current_stock);
            }

            $quantity = $validated['quantity'];
            $unitPrice = $product->sell_price;
            $discount = $validated['discount'] ?? 0;
            $paidAmount = $validated['paid_amount'];
            $vatRate = env('VAT_RATE', 0.05);

            // Calculate amounts
            $amounts = Sale::calculateSaleAmounts($quantity, $unitPrice, $discount, $vatRate);

            // Validate discount does not exceed subtotal
            if ($discount > $amounts['subtotal']) {
                throw new \Exception('Discount (৳' . number_format($discount, 2) . ') cannot exceed subtotal (৳' . number_format($amounts['subtotal'], 2) . ').');
            }

            // Validate paid amount does not exceed total amount
            if ($paidAmount > $amounts['total_amount']) {
                throw new \Exception('Paid amount (৳' . number_format($paidAmount, 2) . ') cannot exceed total amount (৳' . number_format($amounts['total_amount'], 2) . '). Overpayment is not allowed.');
            }

            $dueAmount = $amounts['total_amount'] - $paidAmount;

            // Create sale record
            $sale = Sale::create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $amounts['subtotal'],
                'discount' => $discount,
                'vat_rate' => $vatRate,
                'vat_amount' => $amounts['vat_amount'],
                'total_amount' => $amounts['total_amount'],
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'sale_date' => now(),
                'customer_name' => $validated['customer_name'] ?? 'Walk-in Customer',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update product stock
            $product->current_stock -= $quantity;
            $product->save();

            // Create journal entries (Double-Entry Accounting)
            $this->createJournalEntries($sale, $product);

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale recorded successfully with journal entries!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Create journal entries for a sale (Double-Entry Bookkeeping)
     */
    private function createJournalEntries(Sale $sale, Product $product)
    {
        $referenceNumber = 'SALE-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT);
        $costOfGoodsSold = $product->purchase_price * $sale->quantity;

        // Get relevant accounts
        $cashAccount = Account::where('code', '1110')->first(); // Cash
        $accountsReceivableAccount = Account::where('code', '1120')->first(); // Accounts Receivable
        $inventoryAccount = Account::where('code', '1130')->first(); // Inventory
        $salesRevenueAccount = Account::where('code', '4100')->first(); // Sales Revenue
        $discountAccount = Account::where('code', '4200')->first(); // Discount Given
        $vatPayableAccount = Account::where('code', '2120')->first(); // VAT Payable
        $cogsAccount = Account::where('code', '5100')->first(); // Cost of Goods Sold

        $entryDate = $sale->sale_date;
        $description = "Sale of {$sale->quantity} units of {$product->name} to {$sale->customer_name}";

        // Journal Entry 1: Record Cash Received
        // Debit: Cash (Asset)
        if ($sale->paid_amount > 0) {
            $entry1 = JournalEntry::create([
                'account_id' => $cashAccount->id,
                'transaction_type' => 'sale',
                'transaction_id' => $sale->id,
                'entry_date' => $entryDate,
                'description' => $description . ' - Cash received',
                'debit_amount' => $sale->paid_amount,
                'credit_amount' => 0,
                'reference_number' => $referenceNumber,
            ]);
            $cashAccount->updateBalance($sale->paid_amount, true);
        }

        // Journal Entry 2: Record Amounts Receivable (if any due)
        // Debit: Accounts Receivable (Asset)
        if ($sale->due_amount > 0) {
            $entry2 = JournalEntry::create([
                'account_id' => $accountsReceivableAccount->id,
                'transaction_type' => 'sale',
                'transaction_id' => $sale->id,
                'entry_date' => $entryDate,
                'description' => $description . ' - Amount due',
                'debit_amount' => $sale->due_amount,
                'credit_amount' => 0,
                'reference_number' => $referenceNumber,
            ]);
            $accountsReceivableAccount->updateBalance($sale->due_amount, true);
        }

        // Journal Entry 3: Record Sales Revenue (Gross method)
        // Credit: Sales Revenue (Revenue) - use GROSS subtotal so that
        // Discount Given (debit) offsets it, keeping double-entry balanced
        $revenueAmount = $sale->subtotal; // gross subtotal before discount
        $entry3 = JournalEntry::create([
            'account_id' => $salesRevenueAccount->id,
            'transaction_type' => 'sale',
            'transaction_id' => $sale->id,
            'entry_date' => $entryDate,
            'description' => $description . ' - Revenue recognized',
            'debit_amount' => 0,
            'credit_amount' => $revenueAmount,
            'reference_number' => $referenceNumber,
        ]);
        $salesRevenueAccount->updateBalance($revenueAmount, false);

        // Journal Entry 4: Record Discount (if any)
        // Debit: Discount Given (Contra-Revenue)
        if ($sale->discount > 0) {
            $entry4 = JournalEntry::create([
                'account_id' => $discountAccount->id,
                'transaction_type' => 'sale',
                'transaction_id' => $sale->id,
                'entry_date' => $entryDate,
                'description' => $description . ' - Discount given',
                'debit_amount' => $sale->discount,
                'credit_amount' => 0,
                'reference_number' => $referenceNumber,
            ]);
            $discountAccount->updateBalance($sale->discount, true);
        }

        // Journal Entry 5: Record VAT Payable
        // Credit: VAT Payable (Liability)
        $entry5 = JournalEntry::create([
            'account_id' => $vatPayableAccount->id,
            'transaction_type' => 'sale',
            'transaction_id' => $sale->id,
            'entry_date' => $entryDate,
            'description' => $description . ' - VAT collected',
            'debit_amount' => 0,
            'credit_amount' => $sale->vat_amount,
            'reference_number' => $referenceNumber,
        ]);
        $vatPayableAccount->updateBalance($sale->vat_amount, false);

        // Journal Entry 6: Record Cost of Goods Sold
        // Debit: Cost of Goods Sold (Expense)
        $entry6 = JournalEntry::create([
            'account_id' => $cogsAccount->id,
            'transaction_type' => 'sale',
            'transaction_id' => $sale->id,
            'entry_date' => $entryDate,
            'description' => $description . ' - Cost of goods sold',
            'debit_amount' => $costOfGoodsSold,
            'credit_amount' => 0,
            'reference_number' => $referenceNumber,
        ]);
        $cogsAccount->updateBalance($costOfGoodsSold, true);

        // Journal Entry 7: Reduce Inventory
        // Credit: Inventory (Asset)
        $entry7 = JournalEntry::create([
            'account_id' => $inventoryAccount->id,
            'transaction_type' => 'sale',
            'transaction_id' => $sale->id,
            'entry_date' => $entryDate,
            'description' => $description . ' - Inventory reduction',
            'debit_amount' => 0,
            'credit_amount' => $costOfGoodsSold,
            'reference_number' => $referenceNumber,
        ]);
        $inventoryAccount->updateBalance($costOfGoodsSold, false);
    }

    /**
     * Display the specified sale
     */
    public function show(Sale $sale)
    {
        $sale->load(['product', 'journalEntries.account', 'payments']);

        // Also load payment journal entries
        $paymentIds = $sale->payments->pluck('id');
        $paymentJournalEntries = JournalEntry::with('account')
            ->where('transaction_type', 'payment')
            ->whereIn('transaction_id', $paymentIds)
            ->get();

        return view('sales.show', compact('sale', 'paymentJournalEntries'));
    }

    /**
     * Show the form for editing the specified sale
     */
    public function edit(Sale $sale)
    {
        // For simplicity, we won't allow editing sales after they're created
        // as it would require reversing journal entries
        return redirect()->route('sales.show', $sale)
            ->with('info', 'Sales cannot be edited. Please create an adjustment or reversal entry.');
    }

    /**
     * Remove the specified sale from storage
     */
    public function destroy(Sale $sale)
    {
        // For proper accounting, we should not delete sales
        // Instead, create a reversal entry
        return redirect()->route('sales.index')
            ->with('info', 'Sales cannot be deleted. Please create a reversal entry if needed.');
    }
}
