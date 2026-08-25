<?php

namespace App\Support;

class PhoneNumberNormalizer
{
    public static function normalizeDigits(?string $value): string
    {
        $value ??= '';

        return strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
    }

    public static function toE164(string $countryCode, ?string $mobile): string
    {
        $countryCode = '+' . self::digitsOnly($countryCode);
        $ccDigits = ltrim($countryCode, '+');
        $mobile = self::normalizeDigits($mobile);

        $raw = preg_replace('/[^0-9+]/', '', $mobile) ?? '';
        if ($raw === '') {
            return $countryCode;
        }

        $digits = self::digitsOnly($raw);
        $national = $digits;

        if (str_starts_with($digits, '00' . $ccDigits)) {
            $national = substr($digits, strlen($ccDigits) + 2);
        } elseif (str_starts_with($digits, $ccDigits)) {
            $national = substr($digits, strlen($ccDigits));
        }

        // National trunk prefix (leading zero) is ignored when country code is provided.
        $national = ltrim($national, '0');

        return $national === '' ? $countryCode : $countryCode . $national;
    }

    public static function variants(?string $mobile, string $defaultCountryCode = '+98'): array
    {
        $mobile = self::normalizeDigits($mobile);
        $digits = self::digitsOnly($mobile);
        $variants = [];

        if ($mobile !== '') {
            $variants[] = preg_replace('/[^0-9+]/', '', $mobile) ?? '';
        }
        if ($digits !== '') {
            $variants[] = $digits;
        }

        $variants[] = self::toE164($defaultCountryCode, $mobile);

        if (str_starts_with($digits, '9') && strlen($digits) >= 9) {
            $variants[] = '0' . $digits;
            $variants[] = '+98' . ltrim($digits, '0');
        }

        if (str_starts_with($digits, '1') && strlen($digits) > 7) {
            $variants[] = '+1' . ltrim($digits, '0');
        }

        return array_values(array_unique(array_filter($variants)));
    }

    private static function digitsOnly(string $value): string
    {
        return preg_replace('/\D+/', '', self::normalizeDigits($value)) ?? '';
    }
}
