<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    protected $fillable = ['name', 'name_kh', 'symbol'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
