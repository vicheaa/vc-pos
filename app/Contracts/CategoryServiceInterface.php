<?php

namespace App\Contracts;

use App\Models\Category;
use Illuminate\Http\Request;

interface CategoryServiceInterface
{
    public function createCategory(Request $request): Category;
}
