<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLedgerItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ledger()
    {
        return $this->belongsTo(StockLedger::class, 'stock_ledger_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_code', 'code');
    }
}
