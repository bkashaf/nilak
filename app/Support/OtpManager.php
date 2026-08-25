<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpManager
{
    public function isEnabled(): bool
    {
        return (bool) config('otp.enabled', false);
    }

    public function canResend(User $user): bool
    {
        $lastSent = $user->otp_last_sent_at;
        if (! $lastSent) {
            return true;
        }

        $resendSeconds = (int) config('otp.resend_seconds', 60);

        return Carbon::now()->diffInSeconds($lastSent) >= $resendSeconds;
    }

    public function issueCode(User $user): array
    {
        $length = max(4, (int) config('otp.length', 6));
        $ttlSeconds = max(60, (int) config('otp.ttl_seconds', 120));
        $plainCode = $this->generateNumericCode($length);

        $user->forceFill([
            'otp_code_hash' => Hash::make($plainCode),
            'otp_expires_at' => Carbon::now()->addSeconds($ttlSeconds),
            'otp_attempts' => 0,
            'otp_last_sent_at' => Carbon::now(),
        ])->save();

        if ((bool) config('otp.debug_log', true)) {
            Log::info('OTP issued for user', [
                'user_id' => $user->id,
                'mobile' => $user->mobile,
                'otp_code' => $plainCode,
                'expires_at' => optional($user->otp_expires_at)?->toDateTimeString(),
            ]);
        }

        return [
            'code' => $plainCode,
            'expires_at' => $user->otp_expires_at,
        ];
    }

    public function verifyCode(User $user, string $code): bool
    {
        if (! filled($user->otp_code_hash) || ! $user->otp_expires_at) {
            return false;
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return false;
        }

        $maxAttempts = max(1, (int) config('otp.max_attempts', 5));
        if ((int) $user->otp_attempts >= $maxAttempts) {
            return false;
        }

        if (! Hash::check($code, $user->otp_code_hash)) {
            $user->increment('otp_attempts');
            return false;
        }

        $user->forceFill([
            'otp_code_hash' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
        ])->save();

        return true;
    }

    private function generateNumericCode(int $length): string
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= (string) random_int(0, 9);
        }

        return $code;
    }
}
