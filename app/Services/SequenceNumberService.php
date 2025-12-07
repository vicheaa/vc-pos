<?php

namespace App\Services;

use App\Models\SequenceNumber;
use Illuminate\Support\Facades\DB;
use Exception;

class SequenceNumberService
{
    /**
     * Generate the next sequence number for a given type.
     *
     * @param string $type The sequence type (e.g., 'INVOICE', 'PO')
     * @param int|null $shopId Optional shop ID for scoped sequences
     * @return string The formatted sequence number
     * @throws Exception
     */
    public function generateNextNumber(string $type, ?int $shopId = null): string
    {
        return DB::transaction(function () use ($type, $shopId) {
            // Find the sequence record and lock it for update to prevent concurrency issues
            $sequence = SequenceNumber::where('type', $type)
                ->where('shop_id', $shopId)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                 // Option 1: Throw error
                 // throw new Exception("Sequence not configured for type: {$type}");
                 
                 // Option 2: Auto-create a default one (safer for quick starts)
                 $sequence = SequenceNumber::create([
                     'type'          => $type,
                     'shop_id'       => $shopId,
                     'prefix'        => strtoupper($type) . '-',
                     'current_number'=> 0,
                     'padding'       => 6,
                     'description'   => 'Auto-generated sequence',
                 ]);
                 // Lock it again just to be safe if we were in a high race condition area, 
                 // though create is atomic enough for this flow usually.
            }

            // Increment
            $sequence->current_number++;
            $sequence->save();

            // Format
            $numberPart = str_pad($sequence->current_number, $sequence->padding, '0', STR_PAD_LEFT);
            
            return ($sequence->prefix ?? '') . $numberPart . ($sequence->suffix ?? '');
        });
    }
}
