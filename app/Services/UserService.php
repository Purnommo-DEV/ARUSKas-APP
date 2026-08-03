<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                ...Arr::only($data, ['name', 'email']),
                'password' => Hash::make($data['password']),
            ]);
            $user->assignRole($data['role']);

            return $user;
        });
    }

    public function update(User $user, array $data, User $actor): User
    {
        if ($user->is($actor) && $data['role'] !== 'admin') {
            throw ValidationException::withMessages([
                'role' => 'Role akun yang sedang digunakan tidak dapat diturunkan.',
            ]);
        }

        return DB::transaction(function () use ($user, $data): User {
            $attributes = Arr::only($data, ['name', 'email']);
            if (! empty($data['password'])) {
                $attributes['password'] = Hash::make($data['password']);
            }

            $user->update($attributes);
            $user->syncRoles([$data['role']]);

            return $user->refresh();
        });
    }

    public function delete(User $user, User $actor): void
    {
        if ($user->is($actor)) {
            throw ValidationException::withMessages(['user' => 'Akun yang sedang digunakan tidak dapat dihapus.']);
        }

        if ($user->transactions()->exists()) {
            throw ValidationException::withMessages([
                'user' => 'User yang memiliki riwayat transaksi tidak dapat dihapus.',
            ]);
        }

        DB::transaction(fn () => $user->delete());
    }
}
