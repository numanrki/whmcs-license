<?php

namespace Numanrki\WhmcsLicense\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Numanrki\WhmcsLicense\WhmcsLicenseManager;
use Symfony\Component\HttpFoundation\Response;

class VerifyWhmcsLicense
{
    public function handle(Request $request, Closure $next)
    {
        $licenseKey = $request->header('X-License-Key') ?? $request->query('license_key');

        if (!$licenseKey) {
            return response()->json(['error' => 'License key is required'], Response::HTTP_UNAUTHORIZED);
        }

        $context = [
            'domain' => $request->getHost(),
            'ip' => $request->ip(),
            'directory' => $request->header('X-Directory') ?? base_path(),
        ];

        $manager = app(WhmcsLicenseManager::class);
        $result = $manager->verify($licenseKey, $context);

        if (!$result['valid']) {
            return response()->json(['error' => $result['error'] ?? 'Invalid license'], Response::HTTP_FORBIDDEN);
        }

        $request->attributes->set('whmcs_license', $result['data']);

        return $next($request);
    }
}