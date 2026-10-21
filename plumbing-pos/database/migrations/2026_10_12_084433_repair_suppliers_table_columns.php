<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('name')->default('');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('kra_pin')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        // Intentionally preserve the supplier table and its data.
    }
};