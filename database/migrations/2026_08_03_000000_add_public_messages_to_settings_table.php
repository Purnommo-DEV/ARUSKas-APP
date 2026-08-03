<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->string('thanks_message', 120)->nullable()->after('confirmation_phone');
            $table->string('blessing_message', 120)->nullable()->after('thanks_message');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->dropColumn(['thanks_message', 'blessing_message']);
        });
    }
};
