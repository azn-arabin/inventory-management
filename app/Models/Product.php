<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'purchase_price',
        'sell_price',
        'opening_stock',
        'current_stock',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'opening_stock' => 'integer',
        'current_stock' => 'integer',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getProfitMarginAttribute()
    {
        if ($this->purchase_price == 0) {
            return 0;
        }
        return (($this->sell_price - $this->purchase_price) / $this->purchase_price) * 100;
    }
}
