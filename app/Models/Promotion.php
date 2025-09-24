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
    ];
}
