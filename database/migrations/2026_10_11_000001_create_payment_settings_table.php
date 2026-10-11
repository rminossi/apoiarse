<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('platform_fee_percent', 8, 4)->default(5);
            $table->decimal('platform_fee_fixed', 10, 2)->default(0);
            $table->decimal('mp_pix_max_amount', 10, 2)->default(200);
            $table->decimal('mp_pix_fee_percent', 8, 4)->default(0.99);
            $table->decimal('asaas_pix_fee_fixed', 10, 2)->default(1.99);
            $table->decimal('asaas_card_fee_fixed', 10, 2)->default(0.49);
            $table->decimal('asaas_card_fee_percent', 8, 4)->default(2.99);
            $table->string('platform_asaas_wallet_id')->nullable();
            $table->text('public_fee_intro')->nullable();
            $table->text('public_fee_footnote')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
