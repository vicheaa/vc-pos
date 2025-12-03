<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Http\Helpers\ApiResponse;

class StockController extends Controller
{
    public function index()
    {
        $stock = Stock::all();
        return ApiResponse::success($stock);
    }

    public function store(Request $request)
    {
        $stock = Stock::create($request->all());
        return ApiResponse::success($stock);
    }

    public function update(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);
        $stock->update($request->all());
        return ApiResponse::success($stock);
    }

    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();
        return ApiResponse::success($stock);
    }
}
