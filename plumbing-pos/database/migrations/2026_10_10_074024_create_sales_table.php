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
        Schema::create('sales', function (Blueprint $table) {
        $table->id();

        $table->uuid('sale_uuid')->unique();
        $table->string('receipt_number')->unique();

        $table->foreignId('user_id')
            ->constrained()
            ->restrictOnDelete();

        $table->decimal('subtotal', 12, 2)->default(0);
        $table->decimal('discount', 12, 2)->default(0);
        $table->decimal('total_amount', 12, 2)->default(0);

        $table->enum('status', [
            'pending',
            'completed',
            'cancelled',
        ])->default('pending');

        $table->timestamp('sold_at')->nullable();
        $table->timestamps();
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
