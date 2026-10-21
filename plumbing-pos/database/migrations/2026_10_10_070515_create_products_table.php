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
      Schema::create('products', function (Blueprint $table) {
        $table->id();

        $table->foreignId('category_id')
            ->constrained()
            ->restrictOnDelete();

        $table->string('sku')->unique();
        $table->string('barcode')->nullable()->unique();
        $table->string('name');
        $table->string('brand')->nullable();
        $table->string('size')->nullable();
        $table->string('unit')->default('piece');

        $table->decimal('cost_price', 12, 2);
        $table->decimal('selling_price', 12, 2);

        $table->decimal('quantity_on_hand', 12, 3)
            ->default(0);

        $table->decimal('reorder_level', 12, 3)
            ->default(0);

        $table->boolean('is_active')->default(true);

        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
