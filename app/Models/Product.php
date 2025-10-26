<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded      = [];
    protected $primaryKey   = 'code';
    protected $keyType      = 'string';
    public $incrementing    = false;

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_code', 'code');
    }

    protected $casts = [
        'is_active'     => 'boolean',
        'cost_price'    => 'float',
        'selling_price' => 'float',
    ];

    public function promotions()
    {
        return $this->belongsToMany(
            Promotion::class,
            'product_promotions', // The name of your pivot table
            'product_code',       // The foreign key for the Product model
            'promotion_id',       // The foreign key for the Promotion model
            'code',               // The local key on the products table
            'id'                  // The local key on the promotions table
        );
    }
}
