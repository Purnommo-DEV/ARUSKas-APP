<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('settings', 'opening_balance_set')) {
            Schema::table('settings', function (Blueprint $table): void {
                $table->boolean('opening_balance_set')->default(false)->after('opening_balance');
            });

            DB::table('settings')->where('opening_balance', '>', 0)->update(['opening_balance_set' => true]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'opening_balance_set')) {
            Schema::table('settings', function (Blueprint $table): void {
                $table->dropColumn('opening_balance_set');
            });
        }
    }
};
