<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // Account types
    const TYPE_ASSET = 'asset';
    const TYPE_LIABILITY = 'liability';
    const TYPE_EQUITY = 'equity';
    const TYPE_REVENUE = 'revenue';
    const TYPE_EXPENSE = 'expense';

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Check if account is a debit-normal account
     */
    public function isDebitNormal()
    {
        return in_array($this->type, [self::TYPE_ASSET, self::TYPE_EXPENSE]);
    }

    /**
     * Update account balance based on journal entry
     */
    public function updateBalance($amount, $isDebit)
    {
        if ($this->isDebitNormal()) {
            // For debit-normal accounts: debit increases, credit decreases
            $this->balance += $isDebit ? $amount : -$amount;
        } else {
            // For credit-normal accounts: credit increases, debit decreases
            $this->balance += $isDebit ? -$amount : $amount;
        }
        $this->save();
    }
}
