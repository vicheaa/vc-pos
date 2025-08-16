<?php

namespace App\Services;

use App\Contracts\CategoryServiceInterface;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryService implements CategoryServiceInterface
{
    public function createCategory(Request $request): Category
    {
        $category = Category::create($request->all());
        return $category;
    }
}
