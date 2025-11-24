<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Helpers\ApiResponse;
use App\Contracts\CategoryServiceInterface;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {
        // parent::__construct();
    }
    public function index()
    {
        // return ApiResponse::success(message: 'Categories fetched successfully', data: Category::get());
        // return ApiResponse::paginated(Category::paginate(10));
        $categories = Category::all();
        return response()->json($categories);
    }
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request);
        return ApiResponse::success('Category created successfully', $category);
    }
}
