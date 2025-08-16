<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\ProductServiceInterface;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct(
        protected ProductServiceInterface $productService
    ) {}

    public function index()
    {
        $products = Product::with('uom', 'category')->paginate(20);
        return ApiResponse::paginated($products);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request);
        return ApiResponse::success(data: $product, message: 'Product created successfully');
    }

    public function category_product_list(Request $request)
    {
        $category_code = $request->category_code;
        $products = Product::with('uom', 'category')
            ->where('category_code', $category_code)
            ->paginate(20);
        return ApiResponse::paginated($products);
    }
}
