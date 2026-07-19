<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('type')->constrained('categories')->nullOnDelete();
            $table->boolean('is_featured')->default(false)->after('status');
            $table->unsignedInteger('featured_order')->default(0)->after('is_featured');
            $table->date('end_date')->nullable()->after('featured_order');
            $table->string('short_description', 300)->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn(['is_featured', 'featured_order', 'end_date', 'short_description']);
        });
    }
};
