<?php

namespace Numanrki\WhmcsLicense;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Foundation\Application;

class WhmcsLicenseManager
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function verify(string $licenseKey, array $context = []): array
    {
        $config = config('whmcs-license');

        $cacheKey = 'whmcs_license_' . md5($licenseKey . json_encode($context));
        if ($config['cache_ttl'] > 0 && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $params = [
            'licensekey' => $licenseKey,
            'domain' => $context['domain'] ?? request()->getHost(),
            'ip' => $context['ip'] ?? request()->ip(),
            'dir' => $context['directory'] ?? base_path(),
        ];

        $params['md5hash'] = md5($config['secret_key'] . $licenseKey);

        $response = $this->makeRequest($config['verify_url'], $params);

        $result = $this->parseResponse($response, $config);

        if ($config['cache_ttl'] > 0) {
            Cache::put($cacheKey, $result, $config['cache_ttl']);
        }

        return $result;
    }

    protected function makeRequest(string $url, array $params): string
    {
        $config = config('whmcs-license');
        $attempts = 0;

        while ($attempts < $config['retries']) {
            try {
                $response = Http::timeout($config['timeout'])->post($url, $params);
                if ($response->successful()) {
                    return $response->body();
                }
            } catch (\Exception $e) {
                $attempts++;
                usleep($config['retry_sleep_ms'] * 1000);
            }
        }

        return '';
    }

    protected function parseResponse(string $response, array $config): array
    {
        $lines = explode("\n", $response);
        $data = [];

        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $data[$key] = $value;
            }
        }

        $status = $data['status'] ?? 'Invalid';
        $expiresAt = isset($data['validdomains']) ? Carbon::parse($data['duedate']) : null; // Assuming duedate is expires_at

        $checks = [
            'domain_ok' => isset($data['validdomain']) && str_contains($data['validdomain'], $params['domain']), // Simplified
            'ip_ok' => isset($data['validip']) && str_contains($data['validip'], $params['ip']),
            'dir_ok' => isset($data['validdirectory']) && str_contains($data['validdirectory'], $params['dir']),
        ];

        $valid = $status === 'Active';

        if (!$valid && $status === 'Expired' && $expiresAt) {
            $graceEnd = $expiresAt->addDays($config['admin_grace_days']);
            if (Carbon::now()->lessThan($graceEnd)) {
                $valid = true;
            }
        }

        if ($valid) {
            if (!$checks['domain_ok'] && !$config['allow_domain_conflict']) $valid = false;
            if (!$checks['ip_ok'] && !$config['allow_ip_conflict']) $valid = false;
            if (!$checks['dir_ok'] && !$config['allow_directory_conflict']) $valid = false;
        }

        return [
            'valid' => $valid,
            'data' => [
                'status' => $status,
                'expires_at' => $expiresAt ? $expiresAt->format('Y-m-d') : null,
                'product' => $data['productname'] ?? null,
                'checks' => $checks,
                'raw' => $data,
            ],
            'error' => $valid ? null : 'License verification failed',
        ];
    }

    public function reissue(string $licenseKey): array
    {
        // Implement reissue logic if needed, perhaps call verify with reissue param
        return $this->verify($licenseKey, ['reissue' => true]); // Placeholder
    }

    public function bindDomain(string $license, string $domain): void
    {
        Cache::put('whmcs_bound_domain_' . $license, $domain, config('whmcs-license.cache_ttl'));
    }

    public function getBoundDomain(string $license): ?string
    {
        return Cache::get('whmcs_bound_domain_' . $license);
    }

    public function checkDomain(string $license, string $domain): bool
    {
        $bound = $this->getBoundDomain($license);
        return $bound === $domain;
    }
}