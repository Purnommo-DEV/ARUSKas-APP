<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class SettingService
{
    public function update(Setting $setting, array $data, ?UploadedFile $qris, ?UploadedFile $logo): Setting
    {
        $newFiles = [];
        $oldFiles = [];
        $requestedOpeningBalance = (int) $data['opening_balance'];
        $isOpeningBalanceChangeConfirmed = (bool) ($data['confirm_opening_balance_change'] ?? false);

        if ($setting->opening_balance_set
            && $requestedOpeningBalance !== $setting->opening_balance
            && ! $isOpeningBalanceChangeConfirmed) {
            throw ValidationException::withMessages([
                'opening_balance' => 'Konfirmasi diperlukan sebelum mengubah Saldo Awal Kas.',
            ]);
        }

        try {
            if ($qris) {
                $newFiles['qris_image_path'] = $qris->store('settings/qris', 'public');
                $oldFiles[] = $setting->qris_image_path;
            }

            if ($logo) {
                $newFiles['logo_path'] = $logo->store('settings/logo', 'public');
                $oldFiles[] = $setting->logo_path;
            }

            $setting = DB::transaction(function () use ($setting, $data, $newFiles, $requestedOpeningBalance, $isOpeningBalanceChangeConfirmed): Setting {
                $settingData = Arr::only($data, [
                    'study_name',
                    'mosque_name',
                    'address',
                    'confirmation_phone',
                    'thanks_message',
                    'blessing_message',
                ]);

                if (! $setting->opening_balance_set || $isOpeningBalanceChangeConfirmed) {
                    $settingData['opening_balance'] = $requestedOpeningBalance;
                    $settingData['opening_balance_set'] = true;
                }

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
