<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransactionCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_transaction_and_week_period_is_calculated_automatically(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $category = Category::query()->where('type', 'income')->firstOrFail();

        $this->actingAs($admin)
            ->postJson(route('admin.transactions.store'), [
                'transaction_date' => '2026-08-02',
                'payment_method' => 'qris',
                'category_id' => $category->id,
                'party_name' => 'Hamba Allah',
                'amount' => 250_000,
                'notes' => 'Donasi kajian',
            ])
            ->assertCreated();

        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame('2026-07-27', $transaction->week_start->toDateString());
        $this->assertSame($admin->id, $transaction->created_by);
        $this->assertDatabaseMissing('transactions', ['amount' => -250_000]);
    }

    public function test_inactive_category_cannot_be_used_for_new_transaction(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $category = Category::query()->firstOrFail();
        $category->update(['is_active' => false]);

        $this->actingAs($admin)
            ->postJson(route('admin.transactions.store'), [
                'transaction_date' => '2026-08-02',
                'payment_method' => 'cash',
                'category_id' => $category->id,
                'party_name' => 'Penguji',
                'amount' => 100_000,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('category_id');
    }

    public function test_existing_transaction_can_keep_its_category_after_category_is_deactivated(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $category = Category::query()->firstOrFail();
        $transaction = Transaction::query()->create([
            'transaction_date' => '2026-08-02',
            'week_start' => '2026-07-27',
            'payment_method' => 'cash',
            'category_id' => $category->id,
            'party_name' => 'Penguji',
            'amount' => 100_000,
            'created_by' => $admin->id,
        ]);
        $category->update(['is_active' => false]);

        $this->actingAs($admin)
            ->putJson(route('admin.transactions.update', $transaction), [
                'transaction_date' => '2026-08-02',
                'payment_method' => 'cash',
                'category_id' => $category->id,
                'party_name' => 'Penguji Diperbarui',
                'amount' => 100_000,
            ])
            ->assertOk();

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'category_id' => $category->id,
            'party_name' => 'Penguji Diperbarui',
        ]);
    }

    public function test_uploaded_proof_is_resized_and_stored_only_as_webp(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $category = Category::query()->where('type', 'income')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.transactions.store'), [
                'transaction_date' => '2026-08-02',
                'payment_method' => 'qris',
                'category_id' => $category->id,
                'party_name' => 'Penguji Bukti',
                'amount' => 250_000,
                'proof' => UploadedFile::fake()->image('bukti.png', 2400, 1200),
            ], ['Accept' => 'application/json'])
            ->assertCreated();

        $transaction = Transaction::query()->firstOrFail();
        $this->assertStringEndsWith('.webp', $transaction->proof_path);
        Storage::disk('public')->assertExists($transaction->proof_path);

        [$width, $height, $type] = getimagesize(Storage::disk('public')->path($transaction->proof_path));
        $this->assertSame(1920, $width);
        $this->assertSame(960, $height);
        $this->assertSame(IMAGETYPE_WEBP, $type);
    }

    public function test_replacing_and_removing_proof_deletes_old_files(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();
        $category = Category::query()->where('type', 'income')->firstOrFail();

        $payload = [
            'transaction_date' => '2026-08-02',
            'payment_method' => 'cash',
            'category_id' => $category->id,
            'party_name' => 'Penguji Bukti',
            'amount' => 100_000,
        ];

        $this->actingAs($admin)->post(route('admin.transactions.store'), [
            ...$payload,
            'proof' => UploadedFile::fake()->image('lama.jpg', 800, 600),
        ], ['Accept' => 'application/json'])->assertCreated();

        $transaction = Transaction::query()->firstOrFail();
        $oldPath = $transaction->proof_path;

        $this->actingAs($admin)->post(route('admin.transactions.update', $transaction), [
            ...$payload,
            '_method' => 'PUT',
            'proof' => UploadedFile::fake()->image('baru.png', 1000, 500),
        ], ['Accept' => 'application/json'])->assertOk();

        $newPath = $transaction->fresh()->proof_path;
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);

        $this->actingAs($admin)->putJson(route('admin.transactions.update', $transaction), [
            ...$payload,
            'remove_proof' => true,
        ])->assertOk();

        $this->assertNull($transaction->fresh()->proof_path);
        Storage::disk('public')->assertMissing($newPath);
    }

    public function test_admin_can_quick_add_category_and_receives_created_option_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->postJson(route('admin.categories.store'), [
                'name' => 'Perlengkapan Baru',
                'type' => 'expense',
                'is_active' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Perlengkapan Baru')
            ->assertJsonPath('data.type', 'expense')
            ->assertJsonPath('data.type_label', 'Pengeluaran');
    }
}
