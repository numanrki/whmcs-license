# numanrki/whmcs-license
[![GitHub stars](https://img.shields.io/github/stars/numanrki/whmcs-license?style=social)](https://github.com/numanrki/whmcs-license)
[![Packagist Downloads](https://img.shields.io/packagist/dt/numanrki/whmcs-license.svg)](https://packagist.org/packages/numanrki/whmcs-license)
[![Packagist Version](https://img.shields.io/packagist/v/numanrki/whmcs-license.svg)](https://packagist.org/packages/numanrki/whmcs-license)
[![Packagist License](https://img.shields.io/packagist/l/numanrki/whmcs-license.svg)](https://packagist.org/packages/numanrki/whmcs-license)
[![Buy Me a Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-ffdd00?style=for-the-badge&logo=buy-me-a-coffee&logoColor=black)](https://buymeacoffee.com/numanrki)


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
LICENSE_SERVER_URL=https://example.com
LICENSE_SERVER_VERIFY_URL=https://example.com/modules/servers/licensing/verify.php
LICENSE_SERVER_SECRET_KEY=your-secret-key
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
## Implementation Guide

To implement the WHMCS License package in your Laravel application for license verification in any software, follow these detailed, single steps. This guide assumes you have a basic Laravel project set up.

### Step 1: Install the Package
- Open your terminal in the root directory of your Laravel project.
- Run the command: `composer require numanrki/whmcs-license`.
- Wait for Composer to download and install the package and its dependencies.

### Step 2: Publish the Configuration File
- In your terminal, run: `php artisan vendor:publish --tag=whmcs-license-config`.
- This creates a new file at `config/whmcs-license.php` in your project.
- Open `config/whmcs-license.php` to review default settings (optional, as most configuration is done via .env).

### Step 3: Configure Environment Variables
- Open your project's `.env` file in a text editor.
- Add the following lines, replacing placeholders with your actual WHMCS details:
  ```
  LICENSE_SERVER_URL=https://your-whmcs-server.com
  LICENSE_SERVER_VERIFY_URL=https://your-whmcs-server.com/modules/servers/licensing/verify.php
  LICENSE_SERVER_SECRET_KEY=your-secret-key
  LICENSE_ADMIN_GRACE_DAYS=10
  LICENSE_FORCE_CHECK_COOLDOWN=60
  LICENSE_TIMEOUT=10
  LICENSE_CACHE_TTL=3600
  LICENSE_RETRIES=2
  LICENSE_RETRY_SLEEP_MS=300
  LICENSE_ENABLE_ROUTE=true
  LICENSE_ALLOW_REISSUE=false
  LICENSE_ALLOW_DOMAIN_CONFLICT=false
  LICENSE_ALLOW_IP_CONFLICT=false
  LICENSE_ALLOW_DIRECTORY_CONFLICT=false
  ```
- Save the `.env` file.
- Clear the config cache if needed by running `php artisan config:clear`.

### Step 4: Verify a License in Code
- Open the PHP file where you want to perform license verification (e.g., a controller).
- At the top of the file, add: `use Numanrki\WhmcsLicense\Facades\WhmcsLicense;`.
- In your method, add the verification logic:
  ```php
  $result = WhmcsLicense::verify('YOUR-LICENSE-KEY', [
      'domain' => request()->getHost(),
      'ip' => request()->ip(),
      'directory' => base_path(),
  ]);
  ```
- Check the result:
  ```php
  if ($result['valid']) {
      // Proceed with licensed features
  } else {
      // Handle invalid license, e.g., return an error response
  }
  ```
- Save the file.

### Step 5: Set Up Middleware for Route Protection
- Open `app/Http/Kernel.php`.
- In the `$routeMiddleware` array, add:
  ```php
  'whmcs.verified' => \Numanrki\WhmcsLicense\Http\Middleware\VerifyWhmcsLicense::class,
  ```
- Save `Kernel.php`.
- Open your routes file (e.g., `routes/web.php` or `routes/api.php`).
- Apply the middleware to protected routes:
  ```php
  Route::middleware('whmcs.verified')->group(function () {
      Route::get('/protected', function () {
          // Protected content
      });
  });
  ```
- Save the routes file.
- The middleware will check for `X-License-Key` header or `license_key` query parameter.

### Step 6: Use Validation Rule for License Checks
- Open the file where you handle form validation (e.g., a controller or request class).
- At the top, add: `use Numanrki\WhmcsLicense\Rules\ValidWhmcsLicense;`.
- In your validation logic, add:
  ```php
  $request->validate([
      'license_key' => ['required', new ValidWhmcsLicense('Expected Product Name', 'Active')],
  ]);
  ```
- Save the file.
- This rule verifies the license key against your WHMCS server.

### Step 7: Test License Verification via CLI
- Open your terminal in the project root.
- Run: `php artisan whmcs:verify YOUR-LICENSE-KEY --domain=example.com --ip=192.168.1.1 --dir=/path/to/your/app`.
- Review the output for verification results.

### Step 8: Implement Domain Binding
- In a PHP file (e.g., controller), add: `use Numanrki\WhmcsLicense\Facades\WhmcsLicense;`.
- Bind a domain to a license key: `WhmcsLicense::bindDomain('YOUR-LICENSE-KEY', 'example.com');`.
- Retrieve the bound domain: `$boundDomain = WhmcsLicense::getBoundDomain('YOUR-LICENSE-KEY');`.
- Check if a domain matches: `$isMatching = WhmcsLicense::checkDomain('YOUR-LICENSE-KEY', 'example.com');`.
- Use these in your logic to enforce domain-specific licensing.
- Save the file.

### Step 9: Enable and Use the Optional Verification Route
- Ensure `LICENSE_ENABLE_ROUTE=true` is set in your `.env` file (from Step 3).
- Run `php artisan route:clear` to refresh routes if needed.
- Access the route in your browser or via API: `http://your-app-url/whmcs/verify?license_key=YOUR-LICENSE-KEY`.
- The route will return the verification result.

### Step 10: Test the Implementation
- Run your Laravel application (e.g., `php artisan serve`).
- Test protected routes, validation, and verification logic.
- If issues arise, check logs for errors and ensure your WHMCS server is reachable.

By following these steps, you can fully integrate the WHMCS license system into your Laravel-based software for secure license management.

## Testing

The package includes tests. Run them with PHPUnit or Pest after setting up.

## License

MIT License.