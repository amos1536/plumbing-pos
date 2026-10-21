
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('payments', 'mpesa_checkout_request_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('mpesa_checkout_request_id')
                    ->nullable()
                    ->unique();
            });
        }

        if (!Schema::hasColumn('payments', 'mpesa_merchant_request_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('mpesa_merchant_request_id')
                    ->nullable();
            });
        }

        if (!Schema::hasColumn('payments', 'phone_number')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('phone_number', 20)->nullable();
            });
        }

        if (!Schema::hasColumn('payments', 'mpesa_result_code')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('mpesa_result_code', 20)->nullable();
            });
        }

        if (!Schema::hasColumn('payments', 'mpesa_result_description')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->text('mpesa_result_description')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Intentionally leave existing payment data and columns untouched.
        // Some columns may have existed before this migration.
    }
};
