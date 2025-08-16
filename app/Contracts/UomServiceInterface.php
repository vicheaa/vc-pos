<?php

namespace App\Contracts;

use App\Models\Uom;
use Illuminate\Http\Request;

interface UomServiceInterface
{
    public function createUom(Request $request): Uom;
}
