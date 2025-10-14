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

    public function index(Request $request)
    {
        $query = Product::query();

        $query->with('uom', 'category');

        $query->when($request->input('search'), function ($q, $searchTerm) {
            $q->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('name_kh', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%");
            });
        });

        $products = $query->paginate(20)->appends($request->query());

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
