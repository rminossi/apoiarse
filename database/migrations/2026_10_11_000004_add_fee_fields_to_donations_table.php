<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('gateway', 32)->nullable()->after('payment_method');
            $table->decimal('platform_fee', 10, 2)->nullable()->after('amount');
            $table->decimal('gateway_fee', 10, 2)->nullable()->after('platform_fee');
            $table->decimal('net_amount', 10, 2)->nullable()->after('gateway_fee');
            $table->string('fee_mode_applied', 32)->nullable()->after('net_amount');
            $table->boolean('split_applied')->default(false)->after('fee_mode_applied');
            $table->json('fee_breakdown')->nullable()->after('split_applied');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn([
                'gateway',
                'platform_fee',
                'gateway_fee',
                'net_amount',
                'fee_mode_applied',
                'split_applied',
                'fee_breakdown',
            ]);
        });
    }
};
