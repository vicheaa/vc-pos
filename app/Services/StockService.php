<?php

namespace App\Services;

use App\Models\MovementType;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Exception;

class StockService
{
    /**
     * Adjust the stock quantity for a product.
     *
     * @param string $productCode
     * @param float $quantity Change in quantity (positive for addition, negative for subtraction)
     * @param string $type Transaction type (e.g., 'SALE', 'PURCHASE', 'ADJUSTMENT')
     * @param int|null $shopId
     * @param int|null $userId ID of the user performing the action
     * @return Stock
     * @throws Exception
     */
    public function adjustStock(string $productCode, float $quantity, string $type, ?int $shopId = null, ?int $userId = null, $invoiceNo = null)
    {
        $results = $this->createTransaction(
            [
                'shop_id' => $shopId,
                'type' => $type,
                'created_by' => $userId,
                'invoice_no' => $invoiceNo,
            ],
            [
                [
                    'product_code' => $productCode,
                    'quantity' => $quantity,
                ]
            ]
        );

        return $results[0] ?? null;
    }

    /**
     * Create a stock ledger transaction with multiple items.
     * 
     * @param array $headerData ['shop_id', 'type', 'created_by', 'invoice_no', 'po_no', 'ref', 'remarks']
     * @param array $itemsData [['product_code', 'quantity']]
     */
    public function createTransaction(array $headerData, array $itemsData)
    {
        return DB::transaction(function () use ($headerData, $itemsData) {
            // 1. Create Header
            $ledger = \App\Models\StockLedger::create([
                'shop_id'    => $headerData['shop_id'] ?? null,
                'type'       => $headerData['type'],
                'invoice_no' => $headerData['invoice_no'] ?? null,
                'po_no'      => $headerData['po_no'] ?? null,
                'ref'        => $headerData['ref'] ?? null,
                'remarks'    => $headerData['remarks'] ?? null,
                'created_by' => $headerData['created_by'] ?? null,
            ]);

            $results = [];

            // 2. Process Items
            foreach ($itemsData as $item) {
                $productCode = $item['product_code'];
                $quantity    = $item['quantity'];

                // Get movement type logic
                $movementType = MovementType::where('code', $headerData['type'])->first();
                if (!$movementType) {
                    throw new Exception("Invalid movement type: {$headerData['type']}");
                }

                $cleanQuantity = abs($quantity);
                $change = ($movementType->symbol === '+') ? $cleanQuantity : -$cleanQuantity;

                // Find or create stock
                $stock = Stock::firstOrCreate(
                    [
                        'product_code' => $productCode,
                        'shop_id'      => $headerData['shop_id'] ?? null
                    ],
                    ['quantity' => 0]
                );

                $newQuantity = $stock->quantity + $change;

                if ($newQuantity < 0) {
                     // Potential negative stock check
                }

                $stock->update(['quantity' => $newQuantity]);

                // Create Item Entry
                $ledger->items()->create([
                    'product_code' => $productCode,
                    'change'       => $change,
                    'new_quantity' => $newQuantity,
                ]);
                
                $results[] = $stock;
            }

            return $results; // Return array of updated stocks or the ledger itself depending on needs
        });
    }
}
