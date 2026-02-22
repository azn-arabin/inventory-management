<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'discount',
        'vat_rate',
        'vat_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'sale_date',
        'customer_name',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'vat_rate' => 'decimal:4',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'transaction_id')->where('transaction_type', 'sale');
    }

    /**
     * Calculate all sale amounts
     */
    public static function calculateSaleAmounts($quantity, $unitPrice, $discount, $vatRate)
    {
        $subtotal = $quantity * $unitPrice;
        $afterDiscount = $subtotal - $discount;
        $vatAmount = $afterDiscount * $vatRate;
        $totalAmount = $afterDiscount + $vatAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'vat_amount' => round($vatAmount, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }
}
