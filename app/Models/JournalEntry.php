<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'transaction_type',
        'transaction_id',
        'entry_date',
        'description',
        'debit_amount',
        'credit_amount',
        'reference_number',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'entry_date' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the transaction (polymorphic)
     */
    public function transaction()
    {
        if ($this->transaction_type === 'sale') {
            return $this->belongsTo(Sale::class, 'transaction_id');
        }
        return null;
    }
}
