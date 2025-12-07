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

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_kh')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            $table->string('po_no')->unique();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_name_kh')->nullable();
            $table->string('supplier_phone')->nullable();
            $table->string('supplier_phone_kh')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'closed'])->default('pending');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('total_tax', 10, 2)->default(0);
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->date('po_date')->default(now());
            $table->string('note')->nullable();

            $table->unsignedBigInteger('shop_id')->nullable();  // is warehouse
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('set null');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });


        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');

            $table->string('product_code')->nullable();
            $table->foreign('product_code')->references('code')->on('products')->onDelete('set null');

            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('purchase_order_items');
    }
};
