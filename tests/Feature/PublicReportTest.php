<?php

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Enums\PaymentMethod;
use App\Models\Category;
use App\Models\OpeningBalance;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_public_financial_report_and_its_ajax_endpoints(): void
    {
        Setting::current()->update([
            'study_name' => 'Kajian Ahad',
            'mosque_name' => 'Masjid Al-Ikhlas',
            'confirmation_phone' => '0812 3456 7890',
            'thanks_message' => 'Jazakumullahu Khairan',
            'blessing_message' => 'Baarakallahu Fiikum',
        ]);
        OpeningBalance::query()->create([
            'period_year' => now()->year,
            'period_month' => now()->month,
            'opening_balance' => 14_000,
        ]);
        $user = User::factory()->create();
        $income = Category::query()->create([
            'name' => 'Infak Jamaah',
            'type' => CategoryType::Income,
            'is_active' => true,
        ]);
        $expense = Category::query()->create([
            'name' => 'Konsumsi',
            'type' => CategoryType::Expense,
            'is_active' => true,
        ]);
        $currentMonth = now()->startOfMonth();
        $earlierIncome = $this->transaction($user, $income, $currentMonth->copy()->addDay(), 50_000, 'Infak awal');
        $sameDayIncome = $this->transaction($user, $income, $currentMonth->copy()->addDays(7), 100_000, 'Infak pertama');
        $latestIncome = $this->transaction($user, $income, $currentMonth->copy()->addDays(7), 200_000, 'Infak terbaru', 'transactions/bukti-infak.webp');
        $this->transaction($user, $expense, $currentMonth->copy()->addDays(8), 75_000, 'Belanja konsumsi');
        $this->transaction($user, $income, $currentMonth->copy()->subMonth()->addDays(3), 500_000, 'Infak bulan lalu');

        $this->assertSame('6281234567890', Setting::current()->whatsapp_number);

        $this->get(route('public.report'))
            ->assertOk()
            ->assertSee('Laporan Keuangan')
            ->assertSee('Kajian Ahad')
            ->assertSee('Jazakumullahu Khairan')
            ->assertSee('Chat via WhatsApp')
            ->assertSee('https://wa.me/6281234567890')
            ->assertSee('Detail Pemasukan')
            ->assertDontSee('Detail Pengeluaran')
            ->assertDontSee('Daftar Transaksi')
            ->assertDontSee('Detail Transaksi');

        $this->getJson(route('public.report.summary', [
            'month' => now()->month,
            'year' => now()->year,
        ]))
            ->assertOk()
            ->assertJsonPath('data.opening_balance', 14_000)
            ->assertJsonPath('data.cash_in', 350_000)
            ->assertJsonPath('data.income_total', 350_000)
            ->assertJsonCount(3, 'data.income_transactions')
            ->assertJsonPath('data.income_transactions.0.id', $latestIncome->id)
            ->assertJsonPath('data.income_transactions.1.id', $sameDayIncome->id)
            ->assertJsonPath('data.income_transactions.2.id', $earlierIncome->id)
            ->assertJsonPath('data.income_transactions.0.category_name', 'Infak Jamaah')
            ->assertJsonPath('data.income_transactions.0.payment_method', 'QRIS')
            ->assertJsonPath('data.income_transactions.0.proof_url', asset('storage/transactions/bukti-infak.webp'));

        $this->getJson(route('public.report.summary', [
            'month' => $currentMonth->copy()->subMonth()->month,
            'year' => $currentMonth->copy()->subMonth()->year,
        ]))
            ->assertOk()
            ->assertJsonPath('data.cash_in', 500_000)
            ->assertJsonPath('data.income_total', 500_000)
            ->assertJsonCount(1, 'data.income_transactions');

        $emptyMonth = $currentMonth->copy()->addMonthNoOverflow();
        $this->getJson(route('public.report.summary', [
            'month' => $emptyMonth->month,
            'year' => $emptyMonth->year,
        ]))
            ->assertOk()
            ->assertJsonPath('data.cash_in', 0)
            ->assertJsonPath('data.income_total', 0)
            ->assertJsonCount(0, 'data.income_transactions');

        $this->getJson(route('public.report.transactions.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'month' => now()->month,
            'year' => now()->year,
        ]))
            ->assertOk()
            ->assertJsonPath('opening_balance', 14_000)
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
    }

    private function transaction(
        User $user,
        Category $category,
        \Carbon\CarbonInterface $date,
        int $amount,
        string $notes,
        ?string $proofPath = null,
    ): Transaction {
        return Transaction::query()->create([
            'transaction_date' => $date->toDateString(),
            'week_start' => $date->toDateString(),
            'payment_method' => PaymentMethod::Qris,
            'category_id' => $category->id,
            'party_name' => 'Data privat tidak ditampilkan',
            'amount' => $amount,
            'notes' => $notes,
            'proof_path' => $proofPath,
            'created_by' => $user->id,
        ]);
    }
}
