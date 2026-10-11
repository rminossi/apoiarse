<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('fee_mode', 32)->default('standard')->after('status');
            $table->string('fee_mode_request', 32)->nullable()->after('fee_mode');
            $table->boolean('split_payout_enabled')->default(false)->after('fee_mode_request');
            $table->string('split_payout_request', 32)->nullable()->after('split_payout_enabled');
            $table->timestamp('fee_reviewed_at')->nullable()->after('split_payout_request');
            $table->foreignId('fee_reviewed_by')->nullable()->after('fee_reviewed_at')->constrained('users')->nullOnDelete();
            $table->text('fee_review_notes')->nullable()->after('fee_reviewed_by');

            $table->index(['fee_mode_request', 'split_payout_request']);
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['fee_reviewed_by']);
            $table->dropIndex(['fee_mode_request', 'split_payout_request']);
            $table->dropColumn([
                'fee_mode',
                'fee_mode_request',
                'split_payout_enabled',
                'split_payout_request',
                'fee_reviewed_at',
                'fee_reviewed_by',
                'fee_review_notes',
            ]);
        });
    }
};
