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
        Schema::create('sequence_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();  // If shop_id is null, it's a global sequence. If set, it's per shop.
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            
            
            $table->string('type'); // e.g. 'INVOICE', 'PO'
            $table->string('prefix')->nullable(); // e.g. 'INV-'
            $table->string('suffix')->nullable(); // e.g. '-2025'
            $table->bigInteger('current_number')->default(0); 
            $table->integer('padding')->default(6); // e.g. 000001
            
            $table->string('description')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['shop_id', 'type'], 'unique_shop_type_sequence');
            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequence_numbers');
    }
};
