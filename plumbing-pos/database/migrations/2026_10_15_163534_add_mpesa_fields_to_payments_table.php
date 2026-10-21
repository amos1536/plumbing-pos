
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('mpesa_checkout_request_id')
                ->nullable()
                ->unique();

            $table->string('mpesa_merchant_request_id')
                ->nullable();

            $table->string('mpesa_phone_number', 20)
                ->nullable();

            $table->string('mpesa_result_code', 20)
                ->nullable();

            $table->text('mpesa_result_description')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique([
                'mpesa_checkout_request_id',
            ]);

            $table->dropColumn([
                'mpesa_checkout_request_id',
                'mpesa_merchant_request_id',
                'mpesa_phone_number',
                'mpesa_result_code',
                'mpesa_result_description',
            ]);
        });
    }
};
