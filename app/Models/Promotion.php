<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    /** @use HasFactory<\Database\Factories\PromotionFactory> */
    use HasFactory;

    protected $guarded = [];

    public function productPromotions()
    {
        return $this->hasMany(ProductPromotion::class);
    }

    protected $casts = [
        'value'     => 'float',
        'is_active' => 'boolean',
        'start_date'  => 'date',
        'end_date'    => 'date',
    ];

    public function products()
    {
        return $this->belongsToMany(
            Product::class,                // 1. The final model we want to get
            'product_promotions',          // 2. The name of our pivot table
            'promotion_id',                // 3. The foreign key on the pivot table for THIS model (Promotion)
            'product_code',                // 4. The foreign key on the pivot table for the RELATED model (Product)
            'id',                          // 5. The local key on THIS model (promotions.id)
            'code'                         // 6. The local key on the RELATED model (products.code)
        );
    }
}
