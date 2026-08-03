<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('transactions', 'proof_path')) {
            Schema::table('transactions', function (Blueprint $table): void {
                $table->string('proof_path')->nullable()->after('notes');
            });
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE transactions MODIFY amount BIGINT UNSIGNED NOT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE transactions MODIFY amount DECIMAL(18, 2) NOT NULL');
        }

        if (Schema::hasColumn('transactions', 'proof_path')) {
            Schema::table('transactions', function (Blueprint $table): void {
                $table->dropColumn('proof_path');
            });
        }
    }
};
