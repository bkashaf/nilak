<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->isProfileComplete()) {
            return redirect()
                ->route('account.profile.edit')
                ->with('warning', 'برای ادامه خرید، ابتدا پروفایل خود را کامل کنید.');
        }

        return $next($request);
    }
}
