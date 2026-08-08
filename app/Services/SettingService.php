<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SettingService
{
    public function update(Setting $setting, array $data, ?UploadedFile $qris, ?UploadedFile $logo): Setting
    {
        $newFiles = [];
        $oldFiles = [];
        try {
            if ($qris) {
                $newFiles['qris_image_path'] = $qris->store('settings/qris', 'public');
                $oldFiles[] = $setting->qris_image_path;
            }

            if ($logo) {
                $newFiles['logo_path'] = $logo->store('settings/logo', 'public');
                $oldFiles[] = $setting->logo_path;
            }

            $setting = DB::transaction(function () use ($setting, $data, $newFiles): Setting {
                $settingData = Arr::only($data, [
                    'study_name',
                    'mosque_name',
                    'address',
                    'confirmation_phone',
                    'thanks_message',
                    'blessing_message',
                ]);

                $setting->update([
                    ...$settingData,
                    ...$newFiles,
                ]);

                return $setting->refresh();
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete(array_values($newFiles));
            throw $exception;
        }

        Storage::disk('public')->delete(array_filter($oldFiles));

        return $setting;
    }
}
