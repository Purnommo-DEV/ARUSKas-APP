<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function index(): View
    {
        return view('admin.users.index');
    }

    public function data(): JsonResponse
    {
        return DataTables::eloquent(User::query()->with('roles:id,name')->select('users.*'))
            ->addColumn('role', fn (User $user) => $user->roles->first()?->name ?? '-')
            ->toJson();
    }

    public function store(UserRequest $request): JsonResponse
    {
        $this->userService->create($request->validated());

        return response()->json(['message' => 'User berhasil ditambahkan.'], 201);
    }

    public function show(User $user): JsonResponse
    {
        $user->loadMissing('roles:id,name');

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name,
            ],
        ]);
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $this->userService->update($user, $request->validated(), $request->user());

        return response()->json(['message' => 'User berhasil diperbarui.']);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->userService->delete($user, $request->user());

        return response()->json(['message' => 'User berhasil dihapus.']);
    }
}
