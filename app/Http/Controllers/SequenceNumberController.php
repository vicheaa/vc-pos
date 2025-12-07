<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSequenceNumberRequest;
use App\Http\Requests\UpdateSequenceNumberRequest;
use App\Models\SequenceNumber;

class SequenceNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $sequenceService;

    public function __construct(\App\Services\SequenceNumberService $sequenceService)
    {
        $this->sequenceService = $sequenceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sequences = SequenceNumber::all();
        return \App\Http\Helpers\ApiResponse::success($sequences, 'Sequences retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSequenceNumberRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user() ? $request->user()->id : null;
        
        $sequence = SequenceNumber::create($data);
        return \App\Http\Helpers\ApiResponse::success($sequence, 'Sequence created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SequenceNumber $sequenceNumber)
    {
        return \App\Http\Helpers\ApiResponse::success($sequenceNumber, 'Sequence retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSequenceNumberRequest $request, SequenceNumber $sequenceNumber)
    {
        $sequenceNumber->update($request->validated());
        return \App\Http\Helpers\ApiResponse::success($sequenceNumber, 'Sequence updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SequenceNumber $sequenceNumber)
    {
        $sequenceNumber->delete();
        return \App\Http\Helpers\ApiResponse::success(null, 'Sequence deleted successfully');
    }

    /**
     * Generate the next sequence number for testing purposes.
     */
    public function generate(\Illuminate\Http\Request $request)
    {
         $request->validate([
             'type' => 'required|string',
             'shop_id' => 'nullable|exists:shops,id'
         ]);
         
         try {
             $number = $this->sequenceService->generateNextNumber($request->type, $request->shop_id);
             return \App\Http\Helpers\ApiResponse::success(['number' => $number], 'Sequence generated successfully');
         } catch (\Exception $e) {
             return \App\Http\Helpers\ApiResponse::error($e->getMessage(), 500);
         }
    }
}
