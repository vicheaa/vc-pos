<?php

namespace App\Http\Controllers;

use App\Contracts\UomServiceInterface;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\StoreUomRequest;
use App\Models\Uom;

class UomController extends Controller
{
    public function __construct(
        protected UomServiceInterface $uomService
    ) {}

    public function index()
    {
        $uoms = Uom::all();
        return response()->json($uoms);
        // return ApiResponse::success(message: 'UOM fetched successfully', data: Uom::all());
    }

    public function store(StoreUomRequest $request)
    {
        $uom = $this->uomService->createUom($request);
        return ApiResponse::success($uom, 'UOM created successfully');
    }
}
