<?php

namespace Database\Seeders;

use App\Models\MovementType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovementTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $movementTypes = [
            [
                'code' => 'PO-RECEIVE',
                'name' => 'Purchase Order Receive',
                'description' => 'Purchase Order Receive',
                'is_active' => true,
                'symbol' => '+',
            ],
            [
                'code' => 'PO-RETURN',
                'name' => 'Purchase Order Return',
                'description' => 'Purchase Order Return',
                'is_active' => true,
                'symbol' => '-',
            ],
            [
                'code' => 'PO-ADJUSTMENT',
                'name' => 'Purchase Order Adjustment',
                'description' => 'Purchase Order Adjustment',
                'is_active' => true,
                'symbol' => '-',
            ],
            [
                'code' => 'PO-TRANSFER',
                'name' => 'Purchase Order Transfer',
                'description' => 'Purchase Order Transfer',
                'is_active' => true,
                'symbol' => '-',
            ],
            [
                'code' => 'SALE-ORDER',
                'name' => 'Sale Order',
                'description' => 'Sale Order',
                'is_active' => true,
                'symbol' => '-',
            ],
            [
                'code' => 'LOST',
                'name' => 'Lost',
                'description' => 'Lost',
                'is_active' => true,
                'symbol' => '-',
            ],
            [
                'code' => 'EXPIRED',
                'name' => 'Expired',
                'description' => 'Expired',
                'is_active' => true,
                'symbol' => '-',
            ],
            [
                'code' => 'DONATE',
                'name' => 'Donate',
                'description' => 'Donate',
                'is_active' => true,
                'symbol' => '-',
            ],

        ];

        foreach ($movementTypes as $movementType) {
            MovementType::create($movementType);
        }
    }
}
