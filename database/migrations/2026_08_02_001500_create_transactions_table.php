<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->date('transaction_date')->index();
            $table->date('week_start')->index();
            $table->string('payment_method', 20)->index();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('party_name');
            $table->unsignedBigInteger('amount');
            $table->text('notes')->nullable();
            $table->string('proof_path')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['transaction_date', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
