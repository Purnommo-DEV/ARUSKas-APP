<?php

namespace Tests\Feature;

use App\Models\Setting;
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
            'opening_balance' => 14_000,
            'confirmation_phone' => '0812 3456 7890',
            'thanks_message' => 'Jazakumullahu Khairan',
            'blessing_message' => 'Baarakallahu Fiikum',
        ]);

        $this->assertSame('6281234567890', Setting::current()->whatsapp_number);

        $this->get(route('public.report'))
            ->assertOk()
            ->assertSee('Laporan Keuangan Kajian')
            ->assertSee('Kajian Ahad')
            ->assertSee('Jazakumullahu Khairan')
            ->assertSee('Chat via WhatsApp')
            ->assertSee('https://wa.me/6281234567890')
            ->assertDontSee('Daftar Transaksi')
            ->assertDontSee('Detail Transaksi');

        $this->getJson(route('public.report.summary', [
            'month' => now()->month,
            'year' => now()->year,
        ]))
            ->assertOk()
            ->assertJsonPath('data.opening_balance', 14_000);

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
}
