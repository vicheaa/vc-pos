<?php

namespace App\Contracts;

use Illuminate\Http\Request;
use App\Models\Product;

interface ProductServiceInterface
{
    public function createProduct(Request $request): Product;
}
