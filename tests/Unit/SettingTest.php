<?php

namespace Tests\Unit;

use App\Models\Setting;
use Tests\TestCase;

class SettingTest extends TestCase
{
    public function test_whatsapp_number_accessor_normalizes_indonesian_numbers(): void
    {
        $setting = new Setting(['confirmation_phone' => '0812 3456-789']);

        $this->assertSame('628123456789', $setting->whatsapp_number);
    }

    public function test_whatsapp_number_accessor_returns_null_when_phone_is_empty(): void
    {
        $setting = new Setting(['confirmation_phone' => '   ']);

        $this->assertNull($setting->whatsapp_number);
    }
}
