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
        $products = Product::query()
            ->with('uom', 'category')

            // Conditionally add the category filter
            ->when($request->input('category_code'), function ($query, $categoryCode) {
                $query->where('category_code', $categoryCode);
            })

            // Conditionally add the search filter
            ->when($request->input('search'), function ($query, $searchTerm) {
                // This 'where' groups the 'orWhere' clauses
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('name_kh', 'like', "%{$searchTerm}%")
                        ->orWhere('code', 'like', "%{$searchTerm}%");
                });
            })

            ->paginate(20)
            ->appends($request->query()); // Keep query params on pagination links

        return ApiResponse::paginated($products);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request);
        return ApiResponse::success(data: $product, message: 'Product created successfully');
    }

    // The private function category_product_list() is no longer needed.
}
