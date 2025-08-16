<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
