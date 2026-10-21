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
        Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();

        $table->foreignId('product_id')
            ->constrained()
            ->restrictOnDelete();

        $table->enum('type', [
            'opening',
            'purchase',
            'sale',
            'return',
            'adjustment',
        ]);

        $table->decimal('quantity', 12, 3);
        $table->string('reference')->nullable();
        $table->text('notes')->nullable();

        $table->foreignId('user_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        $table->timestamps();
     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
