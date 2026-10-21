<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('supplier_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('reference')->unique();
            $table->date('purchase_date');
            $table->decimal('total_amount', 12, 2)->default(0);

            $table->enum('status', [
                'draft', 'received', 'cancelled'
            ])->default('draft');

            $table->text('notes')->nullable();
        });

        // 2. Purchase items
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->foreignId('purchase_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('line_total', 12, 2);
        });

        // 3. Sales
        Schema::table('sales', function (Blueprint $table) {
            $table->uuid('sale_uuid')->unique();
            $table->string('receipt_number')->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            $table->enum('status', [
                'pending', 'completed', 'cancelled'
            ])->default('pending');

            $table->timestamp('sold_at')->nullable();
        });

        // 4. Sale items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('sale_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('cost_price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2);
        });

        // 5. Payments
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('sale_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('method', ['cash', 'mpesa']);
            $table->decimal('amount', 12, 2);
            $table->string('transaction_reference')->nullable();

            $table->enum('status', [
                'pending', 'confirmed', 'failed'
            ])->default('pending');

            $table->timestamp('paid_at')->nullable();
        });

        // 6. Expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('title');
            $table->string('category');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('payment_method')->nullable();
            $table->text('description')->nullable();
        });

        // 7. Sale returns
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->foreignId('sale_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('sale_item_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('quantity', 12, 3);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->string('reason')->nullable();

            $table->enum('status', [
                'pending', 'approved', 'rejected', 'completed'
            ])->default('pending');
        });

        // 8. Audit logs
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
        });
    }

    public function down(): void
    {
        // Intentionally preserve the repaired schema and existing data.
    }
};