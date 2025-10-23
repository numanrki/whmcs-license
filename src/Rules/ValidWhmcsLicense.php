<?php

namespace Numanrki\WhmcsLicense\Rules;

use Illuminate\Contracts\Validation\Rule;
use Numanrki\WhmcsLicense\WhmcsLicenseManager;

class ValidWhmcsLicense implements Rule
{
    protected $product;
    protected $status;

    public function __construct($product = null, $status = 'Active')
    {
        $this->product = $product;
        $this->status = $status;
    }

    public function passes($attribute, $value)
    {
        $manager = app(WhmcsLicenseManager::class);
        $result = $manager->verify($value);

        if (!$result['valid']) {
            return false;
        }

        if ($this->product && $result['data']['product'] !== $this->product) {
            return false;
        }

        if ($result['data']['status'] !== $this->status) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'The :attribute must be a valid WHMCS license.';
    }
}