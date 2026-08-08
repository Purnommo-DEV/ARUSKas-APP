<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settingService) {}

    public function edit(): View
    {
        return view('admin.settings.edit', ['setting' => Setting::current()]);
    }

    public function update(SettingRequest $request): JsonResponse
    {
        $setting = $this->settingService->update(
            Setting::current(),
            $request->validated(),
            $request->file('qris_image'),
            $request->file('logo'),
        );

        return response()->json([
            'message' => 'Pengaturan berhasil disimpan.',
            'data' => [
                'qris_url' => $setting->qris_image_path ? asset('storage/'.$setting->qris_image_path) : null,
                'logo_url' => $setting->logo_path ? asset('storage/'.$setting->logo_path) : null,
            ],
        ]);
    }
}
