<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: This assumes 'users' and 'categories' tables already exist.
     */
    public function up(): void
    {
        // Table names should be plural by convention
        Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_kh')->nullable();
            $table->string('symbol')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->string('code', 50)->primary();

            $table->string('name');
            $table->string('name_kh')->nullable();
            $table->text('thumbnail')->nullable(); // Fixed typo
            $table->text('description')->nullable();

            // Use 'cost_price' and decimal for currency
            $table->decimal('cost_price', 10, 2)->default(0); // Fixed typo
            $table->decimal('selling_price', 10, 2);

            $table->boolean('is_active')->default(true);

            $table->foreignId('uom_id')->nullable()->constrained('uoms')->onDelete('set null');

            $table->string('category_code')->nullable();
            $table->foreign('category_code')->references('code')->on('categories')->onDelete('set null');

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * The down method should drop tables in the reverse order of creation.
     */
    public function down(): void
    {
        // Drop 'products' first as it has foreign keys to the other tables
        Schema::dropIfExists('products');
        Schema::dropIfExists('uoms');
    }
};
