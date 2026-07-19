<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            if (! Schema::hasColumn('sites', 'whatsapp_group')) {
                $table->string('whatsapp_group')->nullable()->after('email');
            }
            if (! Schema::hasColumn('sites', 'type')) {
                $table->string('type')->nullable()->after('bank');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_group', 'type']);
        });
    }
};
