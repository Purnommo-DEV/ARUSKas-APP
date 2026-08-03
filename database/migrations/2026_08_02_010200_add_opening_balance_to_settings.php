<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('settings', 'opening_balance')) {
            Schema::table('settings', function (Blueprint $table): void {
                $table->unsignedBigInteger('opening_balance')->default(0)->after('confirmation_phone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'opening_balance')) {
            Schema::table('settings', function (Blueprint $table): void {
                $table->dropColumn('opening_balance');
            });
        }
    }
};
