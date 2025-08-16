<?php

namespace App\Services;

use App\Contracts\ProductServiceInterface;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductService implements ProductServiceInterface
{
    public function createProduct(Request $request): Product
    {
        $product = Product::create($request->all());
        return $product;
    }
}
