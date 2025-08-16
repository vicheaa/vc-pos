<?php

namespace App\Services;

use App\Contracts\UomServiceInterface;
use App\Models\Uom;
use Illuminate\Http\Request;

class UomService implements UomServiceInterface
{
    public function createUom(Request $request): Uom
    {
        $uom = Uom::create($request->all());
        return $uom;
    }
}
