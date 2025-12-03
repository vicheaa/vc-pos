<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $warehouse = [
            [
                'business_name' => 'Angkor Mart',
                'address'       => '03031004',
                'phone_number'  => '098765142',
                'owner_id'      => 1,
            ]
        ];

        foreach ($warehouse as $warehouse) {
            Shop::create($warehouse);
        }
    }
}
