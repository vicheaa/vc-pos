<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('cascade');
            $table->string('product_code');
            $table->foreign('product_code')->references('code')->on('products')->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->default(0);

            $table->unique(['shop_id', 'product_code']);

            $table->timestamps();
        });

        Schema::create('stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('cascade');

            $table->string('product_code');
            $table->foreign('product_code')->references('code')->on('products')->onDelete('cascade');

            $table->decimal('change', 10, 2); // The amount of change (+ or -)

            $table->decimal('new_quantity', 10, 2); // The stock level after the change

            $table->string('type'); // e.g., 'SALE', 'PURCHASE', 'RETURN', 'ADJUSTMENT'
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ledger');
        Schema::dropIfExists('stocks');
    }
};
