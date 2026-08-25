<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstallerAccessible
{
    public function handle(Request $request, Closure $next): Response
    {
        $lockFile = storage_path('app/installed.lock');
        $allowPreview = (bool) config('installer.allow_preview_when_locked', false);

        if (file_exists($lockFile)) {
            $preview = $request->boolean('preview');
            if (! ($allowPreview && $preview)) {
                abort(404);
            }
        }

        return $next($request);
    }
}
