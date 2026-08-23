<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;
use Morilog\Jalali\Jalalian;

class DateFormatter
{
    public function format(DateTimeInterface|string|null $date, ?string $locale = null): string
    {
        if ($date === null) {
            return '';
        }

        $locale ??= app()->getLocale();
        $carbon = $date instanceof DateTimeInterface
            ? Carbon::instance($date)
            : Carbon::parse($date);

        if ($locale === 'fa') {
            $value = Jalalian::fromCarbon($carbon)->format('l j F Y');

            return $this->toPersianDigits($value);
        }

        return $carbon->locale('en')->translatedFormat('l, F j, Y');
    }

    private function toPersianDigits(string $value): string
    {
        return strtr($value, [
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
        ]);
    }
}
