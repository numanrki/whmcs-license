<?php

use Numanrki\WhmcsLicense\WhmcsLicenseManager;
use Illuminate\Support\Facades\Http;

it('verifies a valid license', function () {
    Http::fake([
        config('whmcs-license.verify_url') => Http::response("status=Active\nproductname=My Product\nduedate=2025-12-31\nvaliddomain=example.com\nvalidip=192.168.1.1\nvaliddirectory=/path/to/dir", 200),
    ]);

    $manager = app(WhmcsLicenseManager::class);
    $result = $manager->verify('KEY-1234', [
        'domain' => 'example.com',
        'ip' => '192.168.1.1',
        'directory' => '/path/to/dir',
    ]);

    expect($result['valid'])->toBeTrue();
    expect($result['data']['status'])->toBe('Active');
    expect($result['data']['expires_at'])->toBe('2025-12-31');
    expect($result['data']['product'])->toBe('My Product');
    expect($result['data']['checks'])->toBe([
        'domain_ok' => true,
        'ip_ok' => true,
        'dir_ok' => true,
    ]);
});

it('fails for invalid license', function () {
    Http::fake([
        config('whmcs-license.verify_url') => Http::response("status=Invalid", 200),
    ]);

    $manager = app(WhmcsLicenseManager::class);
    $result = $manager->verify('INVALID-KEY');

    expect($result['valid'])->toBeFalse();
    expect($result['error'])->not->toBeNull();
});