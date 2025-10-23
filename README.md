# numanrki/whmcs-license

A Laravel package for integrating WHMCS Licensing Addon verification system. This package allows Laravel applications to verify licenses remotely, supports middleware and validation rules, and follows Laravel conventions.

## Requirements

- PHP >= 8.1
- Laravel 10 or 11

## Installation

1. Install the package via Composer:

```
composer require numanrki/whmcs-license
```

2. Publish the configuration file:

```
php artisan vendor:publish --tag=whmcs-license-config
```

This will create `config/whmcs-license.php` where you can customize the settings.

## Configuration

Add the following to your `.env` file:

```
# Licensing / WHMCS
LICENSE_SERVER_URL=https://onebighost.com
LICENSE_SERVER_VERIFY_URL=https://onebighost.com/modules/servers/licensing/verify.php
LICENSE_SERVER_SECRET_KEY=onebighost-postsoft
LICENSE_ADMIN_GRACE_DAYS=10
LICENSE_FORCE_CHECK_COOLDOWN=60
LICENSE_TIMEOUT=10
LICENSE_CACHE_TTL=3600
LICENSE_RETRIES=2
LICENSE_RETRY_SLEEP_MS=300
LICENSE_ENABLE_ROUTE=true

# Conflict and policy options
LICENSE_ALLOW_REISSUE=false
LICENSE_ALLOW_DOMAIN_CONFLICT=false
LICENSE_ALLOW_IP_CONFLICT=false
LICENSE_ALLOW_DIRECTORY_CONFLICT=false
```

Note: Replace the example values with your actual WHMCS server details.

## Usage

### Verifying a License

Use the facade to verify a license:

```php
use Numanrki\WhmcsLicense\Facades\WhmcsLicense;

$result = WhmcsLicense::verify('KEY-1234', [
    'domain' => request()->getHost(),
    'ip' => request()->ip(),
    'directory' => base_path(),
]);

if ($result['valid']) {
    // License is valid
} else {
    // Handle invalid license
}
```

The result is an array with `valid` (bool), `data` (array with status, expires_at, product, checks, raw), and `error` (string or null).

### Middleware

Protect routes with the middleware:

In `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ...
    'whmcs.verified' => \Numanrki\WhmcsLicense\Http\Middleware\VerifyWhmcsLicense::class,
];
```

Apply to routes:

```php
Route::middleware('whmcs.verified')->group(function () {
    // Protected routes
});
```

The middleware checks for `X-License-Key` header or `license_key` query param.

### Validation Rule

Use the validation rule in forms or requests:

```php
use Numanrki\WhmcsLicense\Rules\ValidWhmcsLicense;

$request->validate([
    'license_key' => ['required', new ValidWhmcsLicense('My Product', 'Active')],
]);
```

### CLI Command

Verify a license from the command line:

```
php artisan whmcs:verify KEY-1234 --domain=example.com --ip=192.168.1.1 --dir=/path/to/dir
```

### Domain Binding

Bind and check domains:

```php
WhmcsLicense::bindDomain('KEY-1234', 'example.com');
$bound = WhmcsLicense::getBoundDomain('KEY-1234');
$isMatching = WhmcsLicense::checkDomain('KEY-1234', 'example.com');
```

## Optional Route

If `LICENSE_ENABLE_ROUTE=true` in `.env`, a route `/whmcs/verify` is available for verification via GET request with `license_key` query param.

## Testing

The package includes tests. Run them with PHPUnit or Pest after setting up.

## License

MIT License.