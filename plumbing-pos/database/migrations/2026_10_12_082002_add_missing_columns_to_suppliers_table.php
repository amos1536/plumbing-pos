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
        if (!Schema::hasColumn('suppliers', 'name')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('name')->default('');
            });
        }

        if (!Schema::hasColumn('suppliers', 'phone')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('phone')->nullable();
            });
        }

        if (!Schema::hasColumn('suppliers', 'email')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('email')->nullable();
            });
        }

        if (!Schema::hasColumn('suppliers', 'address')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('address')->nullable();
            });
        }

        if (!Schema::hasColumn('suppliers', 'kra_pin')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('kra_pin')->nullable();
            });
        }

        if (!Schema::hasColumn('suppliers', 'is_active')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            //
        });
    }
};
