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
        // This is the main order record
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();

            // Foreign keys
            $table->foreignId('user_id')->comment('The cashier who made the sale')->constrained('users')->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');

            // Financial details. Use decimal for accuracy.
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);

            // Order status
            $table->string('status')->default('COMPLETED'); // e.g., COMPLETED, PENDING, RETURNED
            $table->timestamps();
        });

        // This table holds each item within an order
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Link to the main order
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');

            // Product details at the time of sale
            $table->string('product_code');
            $table->foreign('product_code')->references('code')->on('products')->onDelete('restrict');

            // Use decimal for quantity to support selling by weight (e.g., 0.5 kg)
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2)->comment('Price of a single unit at time of sale');

            // Discount details for this line item
            $table->foreignId('promotion_id')->nullable()->constrained('promotions')->onDelete('set null');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Total discount for this line');

            // Final price for this line
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
