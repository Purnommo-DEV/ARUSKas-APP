<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_balances', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->unsignedBigInteger('opening_balance');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['period_year', 'period_month']);
        });

        if (Schema::hasColumn('settings', 'opening_balance')) {
            $now = now();

            DB::table('settings')
                ->select(['opening_balance'])
                ->orderBy('id')
                ->each(function (object $setting) use ($now): void {
                    DB::table('opening_balances')->updateOrInsert(
                        [
                            'period_year' => $now->year,
                            'period_month' => $now->month,
                        ],
                        [
                            'opening_balance' => (int) $setting->opening_balance,
                            'notes' => 'Migrasi otomatis dari Pengaturan.',
                            'created_by' => null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                    );
                });
        }

        $columns = array_values(array_filter([
            Schema::hasColumn('settings', 'opening_balance') ? 'opening_balance' : null,
            Schema::hasColumn('settings', 'opening_balance_set') ? 'opening_balance_set' : null,
        ]));

        if ($columns !== []) {
            Schema::table('settings', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        $hasOpeningBalance = Schema::hasColumn('settings', 'opening_balance');
        $hasOpeningBalanceSet = Schema::hasColumn('settings', 'opening_balance_set');

        Schema::table('settings', function (Blueprint $table) use ($hasOpeningBalance, $hasOpeningBalanceSet): void {
            if (! $hasOpeningBalance) {
                $table->unsignedBigInteger('opening_balance')->default(0)->after('confirmation_phone');
            }

            if (! $hasOpeningBalanceSet) {
                $table->boolean('opening_balance_set')->default(false)->after('opening_balance');
            }
        });

        Schema::dropIfExists('opening_balances');
    }
};
