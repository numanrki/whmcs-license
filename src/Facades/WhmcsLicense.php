<?php

namespace Numanrki\WhmcsLicense\Facades;

use Illuminate\Support\Facades\Facade;

class WhmcsLicense extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Numanrki\WhmcsLicense\WhmcsLicenseManager::class;
    }
}