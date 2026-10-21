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
        Schema::create('payments', function (Blueprint $table) {
        $table->id();

        $table->foreignId('sale_id')
            ->constrained()
            ->restrictOnDelete();

        $table->enum('method', [
            'cash',
            'mpesa',
        ]);

        $table->decimal('amount', 12, 2);

        $table->string('transaction_reference')->nullable();

        $table->enum('status', [
            'pending',
            'confirmed',
            'failed',
        ])->default('pending');

        $table->timestamp('paid_at')->nullable();
        $table->timestamps();
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
