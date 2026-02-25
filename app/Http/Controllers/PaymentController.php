<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Payment;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Show the payment collection form for a sale
     */
    public function create(Sale $sale)
    {
        $sale->load(['product', 'payments']);
        $totalPaid = $sale->paid_amount;
        $totalDue = $sale->due_amount;

        return view('payments.create', compact('sale', 'totalPaid', 'totalDue'));
    }

    /**
     * Store a new payment for a sale
     */
    public function store(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,mobile',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check that payment doesn't exceed due amount
        if ($validated['amount'] > $sale->due_amount) {
            return back()->withErrors([
                'amount' => 'Payment amount (৳' . number_format($validated['amount'], 2) . ') cannot exceed due amount (৳' . number_format($sale->due_amount, 2) . ').'
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            // Create payment record
            $payment = Payment::create([
                'sale_id' => $sale->id,
                'amount' => $validated['amount'],
                'payment_date' => now(),
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update sale amounts
            $sale->paid_amount += $validated['amount'];
            $sale->due_amount -= $validated['amount'];
            $sale->save();

            // Create journal entries for payment collection
            $this->createPaymentJournalEntries($sale, $payment);

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Payment of ৳' . number_format($validated['amount'], 2) . ' collected successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Create journal entries for a payment collection
     * Debit: Cash (money received)
     * Credit: Accounts Receivable (reduce what customer owes)
     */
    private function createPaymentJournalEntries(Sale $sale, Payment $payment)
    {
        $referenceNumber = 'PAY-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
        $description = "Payment collected for Sale #{$sale->id} from {$sale->customer_name}";

        $cashAccount = Account::where('code', '1110')->first();
        $accountsReceivableAccount = Account::where('code', '1120')->first();

        if (!$cashAccount || !$accountsReceivableAccount) {
            throw new \Exception('Required accounts not found. Please run the AccountSeeder.');
        }

        // Debit: Cash (Asset increases)
        JournalEntry::create([
            'account_id' => $cashAccount->id,
            'transaction_type' => 'payment',
            'transaction_id' => $payment->id,
            'entry_date' => $payment->payment_date,
            'description' => $description . ' - Cash received',
            'debit_amount' => $payment->amount,
            'credit_amount' => 0,
            'reference_number' => $referenceNumber,
        ]);
        $cashAccount->updateBalance($payment->amount, true);

        // Credit: Accounts Receivable (Asset decreases)
        JournalEntry::create([
            'account_id' => $accountsReceivableAccount->id,
            'transaction_type' => 'payment',
            'transaction_id' => $payment->id,
            'entry_date' => $payment->payment_date,
            'description' => $description . ' - Receivable cleared',
            'debit_amount' => 0,
            'credit_amount' => $payment->amount,
            'reference_number' => $referenceNumber,
        ]);
        $accountsReceivableAccount->updateBalance($payment->amount, false);
    }
}
